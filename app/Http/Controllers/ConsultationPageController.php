<?php

namespace App\Http\Controllers;

use App\Models\ConsultRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ConsultationPageController extends Controller
{
    /**
     * Show consultation request form
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('consultation.index');
    }

    /**
     * Show consultation result
     * 
     * @param int $requestId
     * @return \Illuminate\View\View
     */
    public function result($requestId)
    {
        $consultation = ConsultRequest::with('kbli')->findOrFail($requestId);
        
        return view('consultation.result', compact('consultation'));
    }

    /**
     * Download consultation result as PDF
     * 
     * @param int $requestId
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf($requestId)
    {
        $consultation = ConsultRequest::with('kbli')->findOrFail($requestId);
        
        $pdf = Pdf::loadView('consultation.pdf', compact('consultation'))
            ->setPaper('a4', 'portrait');
        
        $filename = 'Estimasi_Biaya_' . $consultation->id . '_' . now()->format('Ymd') . '.pdf';
        
        return $pdf->download($filename);
    }
}
