<?php

namespace App\Services;

use App\Models\PermitApplication;

class PermitApplicationWorkflowService
{
    public function assertCanTransition(string $from, string $to): void
    {
        $allowed = $this->allowedTransitions();

        if (!in_array($to, $allowed[$from] ?? [], true)) {
            abort(422, 'Invalid status transition');
        }
    }

    public function transition(PermitApplication $application, string $to, ?string $notes, string $changedByType, $changedById): PermitApplication
    {
        $from = (string) $application->status;
        $this->assertCanTransition($from, $to);

        $application->update([
            'status' => $to,
        ]);

        $application->statusLogs()->create([
            'from_status' => $from,
            'to_status' => $to,
            'notes' => $notes,
            'changed_by_type' => $changedByType,
            'changed_by_id' => $changedById,
        ]);

        return $application;
    }

    private function allowedTransitions(): array
    {
        return [
            'draft' => ['submitted', 'cancelled'],
            'submitted' => ['under_review', 'document_incomplete', 'cancelled'],
            'under_review' => ['document_incomplete', 'quoted', 'cancelled'],
            'document_incomplete' => ['under_review', 'cancelled'],
            'quoted' => ['quotation_accepted', 'under_review', 'cancelled'],
            'quotation_accepted' => ['payment_pending', 'payment_verified', 'cancelled'],
            'payment_pending' => ['payment_verified', 'cancelled'],
            'payment_verified' => ['converted_to_project', 'in_progress', 'cancelled'],
            'converted_to_project' => ['in_progress', 'cancelled'],
            'in_progress' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];
    }
}
