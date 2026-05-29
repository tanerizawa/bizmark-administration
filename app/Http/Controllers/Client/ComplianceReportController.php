<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateComplianceReportJob;
use App\Models\ComplianceReport;
use App\Models\ReportTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

/**
 * P10 — Client portal for compliance report generation.
 */
class ComplianceReportController extends Controller
{
    public function index()
    {
        $client = Auth::guard('client')->user();
        $reports = ComplianceReport::where('generated_by', $client->id)
            ->with(['template', 'project'])
            ->orderByDesc('created_at')
            ->paginate(10);
        $templates = ReportTemplate::where('is_active', true)->get(['id', 'name', 'type']);

        return view('client.compliance-reports.index', compact('reports', 'templates'));
    }

    public function create()
    {
        $templates = ReportTemplate::where('is_active', true)->get();
        $projects = Auth::guard('client')->user()
            ->projects()
            ->whereIn('status', ['active', 'in_progress'])
            ->get(['id', 'name']);

        return view('client.compliance-reports.create', compact('templates', 'projects'));
    }

    public function store(Request $request)
    {
        $client = Auth::guard('client')->user();

        $request->validate([
            'template_id' => 'required|exists:report_templates,id',
            'project_id' => 'required|exists:projects,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'input_data' => 'required|array',
        ]);

        // Verify project belongs to client
        $project = $client->projects()->where('id', $request->project_id)->firstOrFail();

        $report = ComplianceReport::create([
            'project_id' => $project->id,
            'template_id' => $request->template_id,
            'generated_by' => $client->id,
            'input_data' => $request->input_data,
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
            'status' => 'draft',
        ]);

        GenerateComplianceReportJob::dispatch($report->id);

        return redirect()->route('client.compliance-reports.index')
            ->with('success', 'Laporan sedang dibuat. Anda akan diberitahu saat selesai (estimasi 5-10 menit).');
    }

    public function download(ComplianceReport $report)
    {
        $client = Auth::guard('client')->user();

        if ($report->generated_by !== $client->id) {
            abort(403);
        }

        if ($report->status !== 'ready' || ! $report->pdf_path) {
            return back()->withErrors(['status' => 'Laporan belum siap diunduh.']);
        }

        return Storage::download($report->pdf_path, basename($report->pdf_path));
    }

    public function templateParameters(ReportTemplate $template)
    {
        return response()->json($template->required_parameters ?? []);
    }

    public function sendEmail(Request $request, ComplianceReport $report)
    {
        $client = Auth::guard('client')->user();
        abort_if($report->generated_by !== $client->id || $report->status !== 'ready' || ! $report->pdf_path, 403);

        $validated = $request->validate(['email' => ['required', 'email', 'max:255']]);

        $filePath = Storage::path($report->pdf_path);
        $fileName = basename($report->pdf_path);

        Mail::send([], [], function ($m) use ($validated, $filePath, $fileName, $report) {
            $m->to($validated['email'])
                ->subject('Laporan Compliance: '.($report->template->name ?? 'Laporan'))
                ->setBody('Terlampir laporan compliance dari Bizmark. Periode: '.$report->period_start->format('d M Y').' – '.$report->period_end->format('d M Y').'.', 'text/plain')
                ->attach($filePath, ['as' => $fileName, 'mime' => 'application/pdf']);
        });

        return response()->json(['success' => true]);
    }
}
