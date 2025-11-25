<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\InterviewSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InterviewController extends Controller
{
    /**
     * Show interview details to candidate via access token.
     */
    public function show(Request $request, string $token)
    {
        $interview = InterviewSchedule::with(['jobApplication.jobVacancy'])
            ->where('access_token', $token)
            ->firstOrFail();

        // Check if interview is expired (more than 7 days past interview date)
        if ($interview->scheduled_at->addDays(7)->isPast()) {
            return view('candidate.interview-expired');
        }

        // Calculate time until interview
        $timeUntil = $interview->scheduled_at->diffForHumans();
        $canJoin = $interview->scheduled_at->subMinutes(15)->isPast() && 
                   $interview->scheduled_at->addMinutes($interview->duration_minutes)->isFuture();

        // Get interview preparation tips
        $tips = $this->getInterviewTips($interview->interview_type);

        return view('candidate.interview', compact('interview', 'timeUntil', 'canJoin', 'tips'));
    }

    /**
     * Request reschedule (candidate-initiated).
     */
    public function requestReschedule(Request $request, string $token)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'preferred_dates' => 'required|array|min:1|max:3',
            'preferred_dates.*' => 'date|after:now',
        ]);

        $interview = InterviewSchedule::where('access_token', $token)->firstOrFail();

        // Check if can still reschedule (at least 24 hours before interview)
        if ($interview->scheduled_at->subHours(24)->isPast()) {
            return redirect()
                ->back()
                ->with('error', 'Permintaan reschedule harus dilakukan minimal 24 jam sebelum jadwal interview.');
        }

        // Store reschedule request in interview notes
        $rescheduleData = [
            'requested_at' => now()->toDateTimeString(),
            'reason' => $validated['reason'],
            'preferred_dates' => $validated['preferred_dates'],
        ];

        $interview->update([
            'reschedule_request' => $rescheduleData,
            'status' => 'rescheduled',
        ]);

        // TODO: Send notification to HR admin

        return redirect()
            ->back()
            ->with('success', 'Permintaan reschedule telah dikirim. Tim HR akan menghubungi Anda segera.');
    }

    /**
     * Join meeting (redirect to meeting link).
     */
    public function join(string $token)
    {
        $interview = InterviewSchedule::where('access_token', $token)->firstOrFail();

        // Validate can join
        if ($interview->scheduled_at->subMinutes(15)->isFuture()) {
            return redirect()
                ->route('candidate.interview.show', $token)
                ->with('error', 'Anda hanya dapat join 15 menit sebelum jadwal interview.');
        }

        if ($interview->scheduled_at->addMinutes($interview->duration_minutes)->isPast()) {
            return redirect()
                ->route('candidate.interview.show', $token)
                ->with('error', 'Waktu interview sudah berakhir.');
        }

        // Log join attempt
        $interview->update([
            'candidate_joined_at' => now(),
        ]);

        // Redirect to meeting link
        return redirect()->away($interview->meeting_link);
    }

    /**
     * Get interview preparation tips based on type.
     */
    private function getInterviewTips(string $type): array
    {
        $commonTips = [
            'Pastikan koneksi internet stabil',
            'Siapkan dokumen identitas dan CV Anda',
            'Kenakan pakaian rapi dan profesional',
            'Pilih tempat yang tenang dan pencahayaan baik',
            'Login 10-15 menit sebelum jadwal',
        ];

        $specificTips = match($type) {
            'video' => [
                'Test kamera dan mikrofon Anda',
                'Pastikan background rapi dan profesional',
                'Tutup aplikasi lain untuk performa optimal',
                'Siapkan headset untuk audio yang lebih jelas',
            ],
            'phone' => [
                'Pastikan HP terisi penuh atau tersambung charger',
                'Catat nomor HR untuk berjaga-jaga',
                'Simpan catatan pertanyaan di dekat Anda',
            ],
            'in-person' => [
                'Cek lokasi interview sehari sebelumnya',
                'Berangkat lebih awal untuk menghindari keterlambatan',
                'Bawa dokumen fisik (CV, sertifikat, portfolio)',
                'Siapkan transportasi backup',
            ],
            'panel' => [
                'Siapkan jawaban untuk pertanyaan umum',
                'Latih presentasi diri Anda',
                'Bawa portfolio atau contoh karya jika relevan',
                'Bersiap menghadapi banyak interviewer',
            ],
            default => [],
        };

        return array_merge($commonTips, $specificTips);
    }
}
