<?php

namespace App\Http\Controllers;

use App\Models\BetaTester;
use App\Models\BetaTesterDocument;
use App\Notifications\BetaTesterDocumentLinkNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BetaTesterController extends Controller
{
    /**
     * Display landing page untuk beta tester
     */
    public function index()
    {
        return view('beta-tester.index');
    }

    /**
     * Display registration form
     */
    public function register()
    {
        return view('beta-tester.register');
    }

    /**
     * Process registration
     */
    public function store(Request $request)
    {
        \Log::info('Beta Tester Registration Started', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'place_of_birth' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'address' => 'required|string',
            'identity_number' => 'required|string|max:50',
            'identity_type' => 'required|in:ktp,ktm',
            'university' => 'required|string|max:255',
            'faculty' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'student_id' => 'required|string|max:50',
            'semester' => 'required|integer|min:1|max:14',
            'email' => 'required|email|unique:beta_testers,email',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'motivation' => 'required|string|min:100',
        ], [
            'full_name.required' => 'Nama lengkap wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            'motivation.min' => 'Motivasi minimal 100 karakter',
        ]);

        if ($validator->fails()) {
            \Log::warning('Beta Tester Validation Failed', [
                'errors' => $validator->errors()->toArray()
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            \Log::info('Creating Beta Tester record');

            // Create beta tester
            $betaTester = BetaTester::create([
                'full_name' => $request->full_name,
                'place_of_birth' => $request->place_of_birth,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'identity_number' => $request->identity_number,
                'identity_type' => $request->identity_type,
                'university' => $request->university,
                'faculty' => $request->faculty,
                'major' => $request->major,
                'student_id' => $request->student_id,
                'semester' => $request->semester,
                'email' => $request->email,
                'phone' => $request->phone,
                'whatsapp' => $request->whatsapp ?? $request->phone,
                'motivation' => $request->motivation,
                'status' => 'registered',
                'registration_ip' => $request->ip(),
                'registration_user_agent' => $request->userAgent(),
            ]);

            \Log::info('Beta Tester Created', ['id' => $betaTester->id]);

            // Log registration activity
            $betaTester->logActivity('registration', 'Pendaftaran beta tester berhasil');

            \Log::info('Creating document templates');

            // Create document templates
            $this->createDocumentTemplates($betaTester);

            // Update status to documents_pending
            $betaTester->markAsDocumentsPending();

            // Send notification with dashboard link
            try {
                $betaTester->notify(new BetaTesterDocumentLinkNotification($betaTester, false));
                \Log::info('Document link email sent', ['beta_tester_id' => $betaTester->id]);
            } catch (\Exception $e) {
                \Log::error('Failed to send document link email', [
                    'beta_tester_id' => $betaTester->id,
                    'error' => $e->getMessage()
                ]);
            }

            DB::commit();

            \Log::info('Beta Tester Registration Completed Successfully', [
                'beta_tester_id' => $betaTester->id,
                'registration_number' => $betaTester->registration_number,
                'access_token' => $betaTester->access_token
            ]);

            // Redirect with access token (not registration number)
            return redirect()->route('beta-tester.dashboard', ['token' => $betaTester->access_token])
                ->with('success', 'Pendaftaran berhasil! Silakan tanda tangani dokumen yang diperlukan.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Beta Tester Registration Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat pendaftaran. Silakan coba lagi.')
                ->withInput();
        }
    }

    /**
     * Create document templates for beta tester
     */
    private function createDocumentTemplates(BetaTester $betaTester)
    {
        \Log::info('Creating document templates', ['beta_tester_id' => $betaTester->id]);
        
        // Pakta Integritas template
        $paktaIntegritas = $this->getPaktaIntegritasTemplate();
        BetaTesterDocument::create([
            'beta_tester_id' => $betaTester->id,
            'document_type' => 'pakta_integritas',
            'document_title' => 'Pakta Integritas - Program Beta Tester',
            'document_content' => $paktaIntegritas,
            'is_signed' => false,
        ]);

        \Log::info('Pakta Integritas document created');

        // NDA template
        $nda = $this->getNdaTemplate();
        BetaTesterDocument::create([
            'beta_tester_id' => $betaTester->id,
            'document_type' => 'nda',
            'document_title' => 'Non-Disclosure Agreement (NDA)',
            'document_content' => $nda,
            'is_signed' => false,
        ]);
        
        \Log::info('NDA document created');
    }

    /**
     * Display dashboard
     */
    public function dashboard(Request $request)
    {
        $token = $request->token ?? $request->get('token');
        
        if (!$token) {
            return redirect()->route('beta-tester.index')
                ->with('error', 'Token tidak valid');
        }

        // Find by access_token
        $betaTester = BetaTester::where('access_token', $token)->first();

        if (!$betaTester) {
            return redirect()->route('beta-tester.index')
                ->with('error', 'Token tidak valid atau sudah kadaluarsa');
        }

        // Check if token is valid
        if (!$betaTester->hasValidAccessToken()) {
            return redirect()->route('beta-tester.index')
                ->with('error', 'Token sudah kadaluarsa. Silakan hubungi administrator.');
        }

        $documents = $betaTester->documents;
        $recentActivities = $betaTester->activities()->latest()->limit(10)->get();

        return view('beta-tester.dashboard', compact('betaTester', 'documents', 'recentActivities'));
    }

    /**
     * View document
     */
    public function viewDocument(Request $request, $documentId)
    {
        $token = $request->token ?? $request->get('token');
        
        if (!$token) {
            return redirect()->route('beta-tester.index')
                ->with('error', 'Token tidak valid');
        }
        
        $betaTester = BetaTester::where('access_token', $token)->first();

        if (!$betaTester || !$betaTester->hasValidAccessToken()) {
            return redirect()->route('beta-tester.index')
                ->with('error', 'Akses tidak valid atau token sudah kadaluarsa');
        }

        $document = BetaTesterDocument::where('id', $documentId)
            ->where('beta_tester_id', $betaTester->id)
            ->first();

        if (!$document) {
            return redirect()->route('beta-tester.dashboard', ['token' => $token])
                ->with('error', 'Dokumen tidak ditemukan');
        }

        // Log view activity
        $betaTester->logActivity('document_view', 'Melihat dokumen: ' . $document->document_title, [
            'document_id' => $document->id,
        ]);

        return view('beta-tester.document-view', compact('betaTester', 'document'));
    }

    /**
     * Sign document
     */
    public function signDocument(Request $request, $documentId)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'agreement' => 'required|accepted',
        ], [
            'agreement.accepted' => 'Anda harus menyetujui dokumen untuk melanjutkan',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $betaTester = BetaTester::where('access_token', $request->token)->first();

        if (!$betaTester || !$betaTester->hasValidAccessToken()) {
            return redirect()->route('beta-tester.index')
                ->with('error', 'Akses tidak valid atau token sudah kadaluarsa');
        }

        $document = BetaTesterDocument::where('id', $documentId)
            ->where('beta_tester_id', $betaTester->id)
            ->first();

        if (!$document) {
            return redirect()->route('beta-tester.dashboard', ['token' => $request->token])
                ->with('error', 'Dokumen tidak ditemukan');
        }

        if ($document->is_signed) {
            return redirect()->route('beta-tester.dashboard', ['token' => $request->token])
                ->with('info', 'Dokumen sudah ditandatangani sebelumnya');
        }

        try {
            $document->signDocument();

            return redirect()->route('beta-tester.dashboard', ['token' => $request->token])
                ->with('success', 'Dokumen berhasil ditandatangani');

        } catch (\Exception $e) {
            \Log::error('Document Signing Error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menandatangani dokumen');
        }
    }

    /**
     * Download signed document PDF
     */
    public function downloadPdf(Request $request, $documentId)
    {
        $token = $request->token ?? $request->get('token');
        
        if (!$token) {
            abort(403, 'Token tidak valid');
        }
        
        $betaTester = BetaTester::where('access_token', $token)->first();

        if (!$betaTester || !$betaTester->hasValidAccessToken()) {
            abort(403, 'Akses tidak valid atau token sudah kadaluarsa');
        }

        $document = BetaTesterDocument::where('id', $documentId)
            ->where('beta_tester_id', $betaTester->id)
            ->where('is_signed', true)
            ->first();

        if (!$document) {
            abort(404, 'Dokumen tidak ditemukan atau belum ditandatangani');
        }

        // Log download activity
        $betaTester->logActivity('document_download', 'Mengunduh dokumen: ' . $document->document_title, [
            'document_id' => $document->id,
        ]);

        return $document->downloadPdf();
    }

    /**
     * Get Pakta Integritas template
     */
    private function getPaktaIntegritasTemplate(): string
    {
        return <<<EOT
# PAKTA INTEGRITAS
## Program Beta Tester Bizmark.ID

Yang bertanda tangan di bawah ini:

**Nama:** [Nama Lengkap]
**Tempat, Tanggal Lahir:** [Tempat, Tanggal Lahir]
**Alamat:** [Alamat]
**Nomor Identitas:** [Nomor Identitas] ([Jenis Identitas])
**Universitas:** [Universitas]
**Fakultas:** [Fakultas]
**Program Studi:** [Program Studi]
**NIM:** [NIM]
**Semester:** [Semester]
**Email:** [Email]
**Nomor Telepon:** [Nomor Telepon]

Dengan ini menyatakan dengan sebenar-benarnya bahwa saya:

1. **Berkomitmen untuk berpartisipasi secara aktif** dalam Program Beta Tester Bizmark.ID dengan penuh tanggung jawab dan dedikasi.

2. **Akan melaporkan temuan dengan objektif dan jujur**, tanpa ada manipulasi data atau informasi untuk kepentingan pribadi maupun pihak lain.

3. **Tidak akan menyalahgunakan akses sistem** yang diberikan untuk keperluan di luar lingkup program beta testing.

4. **Tidak akan mengungkapkan informasi rahasia** yang diperoleh selama program kepada pihak luar tanpa izin tertulis dari PT Cangah Pajaratan Mandiri.

5. **Memahami bahwa partisipasi ini bukan merupakan hubungan kerja** dan tidak menimbulkan hak untuk meminta pekerjaan tetap.

6. **Bersedia menerima konsekuensi** apabila melanggar pakta integritas ini, termasuk namun tidak terbatas pada:
   - Pencabutan akses sistem
   - Pembatalan sertifikat
   - Tidak diberikan kompensasi
   - Tindakan hukum sesuai peraturan yang berlaku

7. **Akan menjalankan tugas sesuai Terms of Reference (TOR)** yang telah ditetapkan dengan penuh integritas.

Demikian pakta integritas ini saya buat dengan sebenar-benarnya dalam keadaan sadar dan tanpa paksaan dari pihak manapun.

**Ditandatangani secara digital pada:** [Tanggal Hari Ini]

**Tanda Tangan Digital:**
[Nama Lengkap]
[Email]
EOT;
    }

    /**
     * Get NDA template
     */
    private function getNdaTemplate(): string
    {
        return <<<EOT
# PERJANJIAN KERAHASIAAN (NDA)
## Non-Disclosure Agreement - Program Beta Tester Bizmark.ID

Perjanjian ini dibuat pada: **[Tanggal Hari Ini]**

Antara:

**PT Cangah Pajaratan Mandiri**
Alamat: Kantor Bizmark.ID
(selanjutnya disebut sebagai "Perusahaan")

Dan:

**Nama:** [Nama Lengkap]
**Tempat, Tanggal Lahir:** [Tempat, Tanggal Lahir]
**Alamat:** [Alamat]
**Nomor Identitas:** [Nomor Identitas] ([Jenis Identitas])
**Universitas:** [Universitas]
**Fakultas:** [Fakultas]
**Program Studi:** [Program Studi]
**NIM:** [NIM]
**Email:** [Email]
**Nomor Telepon:** [Nomor Telepon]
(selanjutnya disebut sebagai "Penerima Informasi")

## PASAL 1: DEFINISI

1.1 "Informasi Rahasia" mencakup namun tidak terbatas pada:
- Kode sumber (source code) sistem Bizmark.ID
- Algoritma dan logika bisnis
- Data klien dan pengguna
- Strategi bisnis dan rencana pengembangan
- Dokumen internal perusahaan
- Bug dan kerentanan sistem yang ditemukan
- Informasi finansial dan operasional

1.2 "Program Beta Tester" adalah program pengujian sistem yang diselenggarakan oleh Perusahaan.

## PASAL 2: KEWAJIBAN KERAHASIAAN

2.1 Penerima Informasi setuju untuk:
- Menjaga kerahasiaan semua Informasi Rahasia
- Tidak mengungkapkan kepada pihak ketiga tanpa persetujuan tertulis
- Menggunakan informasi hanya untuk tujuan program beta testing
- Melindungi informasi dengan tingkat keamanan yang wajar

2.2 Kewajiban ini berlaku selama masa program dan **3 (tiga) tahun** setelahnya.

## PASAL 3: PEMBATASAN AKSES

3.1 Akses ke sistem Bizmark.ID hanya untuk keperluan testing sesuai TOR.

3.2 Dilarang:
- Mengunduh atau menyalin kode sumber
- Melakukan reverse engineering
- Mencoba mengakses area di luar scope testing
- Menggunakan akses untuk kepentingan pribadi atau pihak lain

## PASAL 4: HAK KEKAYAAN INTELEKTUAL

4.1 Semua hak kekayaan intelektual tetap menjadi milik Perusahaan.

4.2 Temuan bug atau saran improvement menjadi milik Perusahaan.

## PASAL 5: BUKAN HUBUNGAN KERJA

5.1 Program ini tidak menimbulkan hubungan kerja atau kewajiban perusahaan untuk merekrut.

5.2 Kompensasi bersifat honorarium partisipasi, bukan gaji atau upah.

## PASAL 6: KONSEKUENSI PELANGGARAN

6.1 Pelanggaran NDA dapat mengakibatkan:
- Tuntutan hukum perdata dan/atau pidana
- Ganti rugi materiil dan imateriil
- Pencabutan sertifikat dan kompensasi
- Pemblokiran akses permanen

## PASAL 7: HUKUM YANG BERLAKU

7.1 Perjanjian ini tunduk pada hukum Republik Indonesia.

## PASAL 8: PERSETUJUAN

Dengan menandatangani perjanjian ini secara digital, Penerima Informasi menyatakan:
- Telah membaca dan memahami seluruh isi perjanjian
- Setuju untuk terikat dengan semua ketentuan
- Bersedia bertanggung jawab atas pelanggaran yang dilakukan

**Ditandatangani secara digital pada:** [Tanggal Hari Ini]

**Penerima Informasi:**
[Nama Lengkap]
[Email]

**Perusahaan:**
PT Cangah Pajaratan Mandiri
(Bizmark.ID)
EOT;
    }
}

