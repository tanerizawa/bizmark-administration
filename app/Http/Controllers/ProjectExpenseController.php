<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\PaymentMethod;
use App\Models\ProjectExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProjectExpenseController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'category' => ['required', Rule::in(ProjectExpense::categoryKeys())],
            'vendor_name' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'payment_method' => ['required', Rule::in(PaymentMethod::activeCodes())],
            'bank_account_id' => 'nullable|exists:cash_accounts,id',
            'description' => 'nullable|string',
            'is_billable' => 'nullable|boolean',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Handle file upload
        if ($request->hasFile('receipt_file')) {
            $file = $request->file('receipt_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('receipts/expenses', $filename, 'public');
            $validated['receipt_file'] = $path;
        }

        $validated['project_id'] = $project->id;
        $validated['created_by'] = Auth::id();
        $validated['is_billable'] = $request->boolean('is_billable');

        ProjectExpense::create($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Pengeluaran berhasil dicatat');
    }

    public function destroy(ProjectExpense $expense)
    {
        $project = $expense->project;
        
        // Delete file if exists
        if ($expense->receipt_file && \Storage::disk('public')->exists($expense->receipt_file)) {
            \Storage::disk('public')->delete($expense->receipt_file);
        }

        $expense->delete();

        return redirect()->route('projects.show', $project)
            ->with('success', 'Pengeluaran berhasil dihapus');
    }
}
