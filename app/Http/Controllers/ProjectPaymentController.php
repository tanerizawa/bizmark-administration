<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectPaymentController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:dp,progress,final',
            'payment_method' => 'required|in:transfer,cash,check,other',
            'bank_account_id' => 'nullable|exists:cash_accounts,id',
            'reference_number' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Handle file upload
        if ($request->hasFile('receipt_file')) {
            $file = $request->file('receipt_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('receipts/payments', $filename, 'public');
            $validated['receipt_file'] = $path;
        }

        $validated['project_id'] = $project->id;
        $validated['created_by'] = Auth::id();

        ProjectPayment::create($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Pembayaran berhasil dicatat');
    }

    public function destroy(ProjectPayment $payment)
    {
        $project = $payment->project;
        
        // Delete file if exists
        if ($payment->receipt_file && \Storage::disk('public')->exists($payment->receipt_file)) {
            \Storage::disk('public')->delete($payment->receipt_file);
        }

        $payment->delete();

        return redirect()->route('projects.show', $project)
            ->with('success', 'Pembayaran berhasil dihapus');
    }
}

