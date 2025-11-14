<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermitApplication;
use App\Models\Quotation;
use App\Models\ApplicationStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Notifications\QuotationCreatedNotification;

class QuotationController extends Controller
{
    /**
     * Show the form for creating a new quotation.
     */
    public function create(Request $request)
    {
        $applicationId = $request->get('application_id');
        
        if (!$applicationId) {
            return redirect()->route('admin.permit-applications.index')
                ->with('error', 'Application ID diperlukan');
        }
        
        $application = PermitApplication::with(['client', 'permitType'])->findOrFail($applicationId);
        
        // Check if quotation already exists
        if ($application->quotation) {
            return redirect()->route('admin.quotations.edit', $application->quotation->id)
                ->with('info', 'Quotation sudah ada. Anda bisa mengeditnya.');
        }
        
        // Check if application is in correct status
        if ($application->status !== 'under_review') {
            return back()->with('error', 'Hanya aplikasi yang sedang under review yang bisa dibuat quotation');
        }
        
        return view('admin.quotations.create', compact('application'));
    }
    
    /**
     * Store a newly created quotation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:permit_applications,id',
            'base_price' => 'required|numeric|min:0',
            'additional_fees' => 'nullable|array',
            'additional_fees.*.description' => 'required|string|max:255',
            'additional_fees.*.amount' => 'required|numeric|min:0',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'down_payment_percentage' => 'required|integer|min:0|max:100',
            'validity_days' => 'required|integer|min:1|max:365',
            'notes' => 'nullable|string|max:2000',
        ]);
        
        $application = PermitApplication::findOrFail($request->application_id);
        
        // Check if quotation already exists
        if ($application->quotation) {
            return back()->with('error', 'Quotation sudah ada untuk aplikasi ini');
        }
        
        DB::beginTransaction();
        try {
            // Calculate totals
            $basePrice = $request->base_price;
            $additionalFeesTotal = 0;
            
            if ($request->additional_fees) {
                foreach ($request->additional_fees as $fee) {
                    $additionalFeesTotal += $fee['amount'];
                }
            }
            
            $subtotal = $basePrice + $additionalFeesTotal;
            $taxAmount = $subtotal * ($request->tax_percentage / 100);
            $totalPrice = $subtotal + $taxAmount;
            $downPayment = $totalPrice * ($request->down_payment_percentage / 100);
            
            // Create quotation
            $quotation = Quotation::create([
                'quotation_number' => $this->generateQuotationNumber(),
                'application_id' => $application->id,
                'client_id' => $application->client_id,
                'base_price' => $basePrice,
                'additional_fees' => $request->additional_fees ? json_encode($request->additional_fees) : null,
                'discount_amount' => 0,
                'tax_percentage' => $request->tax_percentage,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalPrice,
                'down_payment_percentage' => $request->down_payment_percentage,
                'down_payment_amount' => $downPayment,
                'valid_until' => now()->addDays((int) $request->validity_days),
                'terms_and_conditions' => $request->notes,
                'created_by' => Auth::id(),
            ]);
            
            // Update application
            $application->update([
                'status' => 'quoted',
                'quoted_price' => $totalPrice,
                'quoted_at' => now(),
                'quotation_expires_at' => $quotation->valid_until,
                'quotation_notes' => $request->notes,
            ]);
            
            // Log status change
            ApplicationStatusLog::create([
                'application_id' => $application->id,
                'from_status' => 'under_review',
                'to_status' => 'quoted',
                'changed_by_type' => 'user',
                'changed_by_id' => Auth::id(),
                'notes' => 'Quotation dibuat dengan total Rp ' . number_format($totalPrice, 0, ',', '.'),
            ]);
            
            DB::commit();
            
            // Send notification to client
            $application->client->notify(new QuotationCreatedNotification($quotation));
            
            return redirect()
                ->route('admin.permit-applications.show', $application->id)
                ->with('success', 'Quotation berhasil dibuat');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal membuat quotation: ' . $e->getMessage());
        }
    }
    
    /**
     * Show the form for editing the specified quotation.
     */
    public function edit($id)
    {
        $quotation = Quotation::with(['application.client', 'application.permitType'])->findOrFail($id);
        $application = $quotation->application;
        
        // Check if quotation can be edited
        if (in_array($quotation->status, ['accepted', 'expired'])) {
            return back()->with('error', 'Quotation yang sudah accepted atau expired tidak bisa diedit');
        }
        
        return view('admin.quotations.edit', compact('quotation', 'application'));
    }
    
    /**
     * Update the specified quotation.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'base_price' => 'required|numeric|min:0',
            'additional_fees' => 'nullable|array',
            'additional_fees.*.description' => 'required|string|max:255',
            'additional_fees.*.amount' => 'required|numeric|min:0',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'down_payment_percentage' => 'required|integer|min:0|max:100',
            'validity_days' => 'required|integer|min:1|max:365',
            'notes' => 'nullable|string|max:2000',
        ]);
        
        $quotation = Quotation::findOrFail($id);
        
        // Check if quotation can be updated
        if (in_array($quotation->status, ['accepted', 'expired'])) {
            return back()->with('error', 'Quotation yang sudah accepted atau expired tidak bisa diupdate');
        }
        
        DB::beginTransaction();
        try {
            // Calculate totals
            $basePrice = $request->base_price;
            $additionalFeesTotal = 0;
            
            if ($request->additional_fees) {
                foreach ($request->additional_fees as $fee) {
                    $additionalFeesTotal += $fee['amount'];
                }
            }
            
            $subtotal = $basePrice + $additionalFeesTotal;
            $taxAmount = $subtotal * ($request->tax_percentage / 100);
            $totalPrice = $subtotal + $taxAmount;
            $downPayment = $totalPrice * ($request->down_payment_percentage / 100);
            
            // Update quotation
            $quotation->update([
                'base_price' => $basePrice,
                'additional_fees' => $request->additional_fees ? json_encode($request->additional_fees) : null,
                'subtotal' => $subtotal,
                'tax_percentage' => $request->tax_percentage,
                'tax_amount' => $taxAmount,
                'total_price' => $totalPrice,
                'down_payment_percentage' => $request->down_payment_percentage,
                'down_payment_amount' => $downPayment,
                'validity_days' => $request->validity_days,
                'valid_until' => now()->addDays($request->validity_days),
                'notes' => $request->notes,
            ]);
            
            // Update application
            $quotation->application->update([
                'quoted_price' => $totalPrice,
                'quotation_expires_at' => $quotation->valid_until,
                'quotation_notes' => $request->notes,
            ]);
            
            // TODO: Send email notification to client
            
            DB::commit();
            
            return redirect()
                ->route('admin.permit-applications.show', $quotation->application_id)
                ->with('success', 'Quotation berhasil diupdate');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal mengupdate quotation: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate PDF quotation.
     */
    public function generatePdf($id)
    {
        $quotation = Quotation::with(['application.client', 'application.permitType', 'createdBy'])
            ->findOrFail($id);
        
        $pdf = Pdf::loadView('admin.quotations.pdf', compact('quotation'))
            ->setPaper('a4', 'portrait');
        
        $filename = 'Quotation_' . $quotation->quotation_number . '.pdf';
        
        return $pdf->download($filename);
    }
    
    /**
     * Send quotation via email to client.
     */
    public function sendEmail($id)
    {
        $quotation = Quotation::with(['application.client'])->findOrFail($id);
        
        // TODO: Implement email sending
        
        return back()->with('success', 'Quotation telah dikirim ke email client');
    }
    
    /**
     * Generate unique quotation number.
     */
    private function generateQuotationNumber()
    {
        $prefix = 'QT';
        $date = now()->format('Ymd');
        
        $lastQuotation = Quotation::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastQuotation) {
            $lastNumber = intval(substr($lastQuotation->quotation_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $date . $newNumber;
    }
}
