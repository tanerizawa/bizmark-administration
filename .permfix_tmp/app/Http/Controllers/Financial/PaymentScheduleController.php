<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\PaymentSchedule;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentScheduleController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'invoice_id' => 'nullable|exists:invoices,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $schedule = $project->paymentSchedules()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Payment schedule created',
            'schedule' => $schedule,
        ]);
    }

    public function markPaid(Request $request, PaymentSchedule $schedule)
    {
        $validated = $request->validate([
            'payment_method' => ['required', Rule::in(PaymentMethod::activeCodes())],
            'reference_number' => 'nullable|string|max:255',
            'cash_account_id' => 'required|exists:cash_accounts,id',
        ]);

        $schedule->markAsPaid(
            $validated['payment_method'],
            $validated['reference_number'] ?? null,
            $validated['cash_account_id']
        );

        return response()->json([
            'success' => true,
            'message' => 'Payment marked as paid',
            'schedule' => $schedule->fresh(),
        ]);
    }

    public function destroy(PaymentSchedule $schedule)
    {
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment schedule deleted',
        ]);
    }
}
