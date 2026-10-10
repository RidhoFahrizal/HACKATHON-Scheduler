<?php

namespace App\Http\Resources;

use App\Domain\Scheduling\Enums\DayOfWeek;
use App\Domain\Scheduling\Enums\RescheduleStatus;
use App\Domain\Scheduling\Support\SlotMapper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RescheduleRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $slotMapper = app(SlotMapper::class);
        $schedule = $this->schedule;

        $origScheduleStr = 'Jadwal Reguler';
        if ($schedule) {
            $times = $slotMapper->timesFor($schedule->startSlot, $schedule->endSlot);
            $dayName = DayOfWeek::tryFrom($schedule->day)?->label() ?? 'Hari ' . $schedule->day;
            $timeStr = $times ? ($times['start_time'] . ' - ' . $times['end_time']) : '';
            $roomName = $schedule->room?->name ?? '';
            $origScheduleStr = "{$dayName}, {$timeStr} ({$roomName})";
        }

        $proposedTimeStr = substr($this->target_start_time, 0, 5) . ' - ' . substr($this->target_end_time, 0, 5);
        $proposedRoomName = $this->targetRoom?->name ?? '';
        $proposedScheduleStr = "{$this->target_day}, {$proposedTimeStr} ({$proposedRoomName})";

        $statusEnum = RescheduleStatus::tryFrom($this->status);
        $statusLabel = $statusEnum ? $statusEnum->label() : $this->status;

        $roleLabel = $this->requester?->role?->name ?? 'Mahasiswa';

        return [
            'id' => $this->request_code,
            'uuid' => $this->id,
            'courseId' => $this->schedule_id,
            'courseTitle' => $schedule?->subject?->name ?? 'Matakuliah',
            'lecturerName' => $schedule?->lecturer?->username ?? '',
            'requesterRole' => $roleLabel,
            'requesterName' => $this->requester?->name ?? '',
            'originalSchedule' => $origScheduleStr,
            'proposedSchedule' => $proposedScheduleStr,
            'reason' => $this->reason,
            'status' => $statusLabel,
            'statusCode' => $this->status,
            'urgency' => $this->urgency,
            'durationType' => $this->duration_type,
            'submittedAt' => $this->created_at ? $this->created_at->format('d M Y H:i') : '',
            'reviewNotes' => $this->review_notes,
        ];
    }
}
