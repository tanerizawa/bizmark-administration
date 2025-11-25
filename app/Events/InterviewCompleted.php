<?php

namespace App\Events;

use App\Models\InterviewSchedule;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InterviewCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public InterviewSchedule $interview,
        public string $recommendation,
        public float $overallRating
    ) {}
}
