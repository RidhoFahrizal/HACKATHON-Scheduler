<?php

namespace App\Domain\Scheduling\Actions;

use App\Domain\Scheduling\Enums\DayOfWeek;
use App\Domain\Scheduling\Enums\RescheduleStatus;
use App\Domain\Scheduling\Support\ScheduleConflictChecker;
use App\Domain\Scheduling\Support\SlotMapper;
use App\Models\Booking;
use App\Models\RescheduleRequest;
use App\Models\Schedule;
use App\Models\User;
use App\Services\AuditTrail;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ApplyRescheduleAction
{
    public function __construct(
        private readonly SlotMapper $slotMapper,
        private readonly ScheduleConflictChecker $conflictChecker,
        private readonly AuditTrail $auditTrail,
    ) {}

    /**
     * @return array{success: bool, message?: string, conflicts?: list<string>}
     */
    public function execute(RescheduleRequest $request, ?User $reviewer = null, ?string $reviewNotes = null): array
    {
        return DB::transaction(function () use ($request, $reviewer, $reviewNotes) {
            $schedule = Schedule::where('id', $request->schedule_id)->lockForUpdate()->first();
            if (! $schedule) {
                return ['success' => false, 'message' => 'Jadwal perkuliahan tidak ditemukan.'];
            }

            $slotRange = $this->slotMapper->rangeFor($request->target_start_time, $request->target_end_time);
            if (! $slotRange) {
                return [
                    'success' => false,
                    'message' => 'Waktu perkuliahan yang dipilih tidak selaras dengan kisi slot waktu sistem.',
                ];
            }
            [$startSlot, $endSlot] = $slotRange;

            $dayInt = $this->parseDayOfWeek($request->target_day);
            if ($dayInt === null) {
                return ['success' => false, 'message' => 'Hari yang dipilih tidak valid.'];
            }

            $conflicts = $this->conflictChecker->conflicts(
                day: $dayInt,
                startSlot: $startSlot,
                endSlot: $endSlot,
                roomId: $request->target_room_id,
                lecturerId: $schedule->lecturerId,
                semesterType: $schedule->semesterType ?? 'ganjil',
                ignoreScheduleId: $schedule->id,
                lock: true,
            );

            if (! empty($conflicts)) {
                return [
                    'success' => false,
                    'message' => 'Terdapat bentrok jadwal pada slot atau ruangan yang dituju.',
                    'conflicts' => $conflicts,
                ];
            }

            if ($request->duration_type === 'permanen') {
                $schedule->update([
                    'day' => $dayInt,
                    'startSlot' => $startSlot,
                    'endSlot' => $endSlot,
                    'roomId' => $request->target_room_id,
                ]);
            } else {
                Booking::create([
                    'scheduleId' => $schedule->id,
                    'day' => $dayInt,
                    'startSlot' => $startSlot,
                    'endSlot' => $endSlot,
                    'roomId' => $request->target_room_id,
                ]);
            }

            $request->update([
                'status' => RescheduleStatus::Approved->value,
                'reviewed_by' => $reviewer?->id,
                'reviewed_at' => now(),
                'review_notes' => $reviewNotes ?? $request->review_notes,
            ]);

            $this->auditTrail->record(
                level: 'shift',
                module: 'Persetujuan Jadwal',
                action: 'approve_reschedule',
                details: "Permohonan {$request->request_code} disetujui untuk {$request->target_day} ({$request->target_start_time} - {$request->target_end_time})",
                actor: $reviewer,
                status: 'Disetujui',
            );

            return ['success' => true];
        });
    }

    private function parseDayOfWeek(string $dayName): ?int
    {
        $map = [
            'senin' => DayOfWeek::SENIN->value,
            'selasa' => DayOfWeek::SELASA->value,
            'rabu' => DayOfWeek::RABU->value,
            'kamis' => DayOfWeek::KAMIS->value,
            'jumat' => DayOfWeek::JUMAT->value,
            'sabtu' => DayOfWeek::SABTU->value,
            'minggu' => DayOfWeek::MINGGU->value,
        ];

        return $map[strtolower(trim($dayName))] ?? null;
    }
}
