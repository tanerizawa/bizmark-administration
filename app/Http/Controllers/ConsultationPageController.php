<?php

namespace App\Http\Controllers;

use App\Models\ConsultRequest;
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
}
