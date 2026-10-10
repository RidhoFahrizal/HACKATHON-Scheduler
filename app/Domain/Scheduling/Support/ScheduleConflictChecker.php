<?php

namespace App\Domain\Scheduling\Support;

use App\Models\Schedule;

final class ScheduleConflictChecker
{
    /**
     * Source-attributed messages (which class holds the room or lecturer) so the UI never blames a student for a clash.
     * Pass $lock inside a transaction so two approvals cannot claim the same slot concurrently.
     *
     * @return list<string>
     */
    public function conflicts(
        int $day,
        int $startSlot,
        int $endSlot,
        string $roomId,
        ?string $lecturerId,
        string $semesterType,
        ?string $ignoreScheduleId = null,
        bool $lock = false,
    ): array {
        $query = Schedule::query()
            ->with(['subject:id,name', 'room:id,name', 'lecturer:id,username'])
            ->where('day', $day)
            ->where('semesterType', $semesterType)
            ->where('startSlot', '<=', $endSlot)
            ->where('endSlot', '>=', $startSlot)
            ->where(function ($inner) use ($roomId, $lecturerId) {
                $inner->where('roomId', $roomId);
                if ($lecturerId !== null && $lecturerId !== '') {
                    $inner->orWhere('lecturerId', $lecturerId);
                }
            });

        if ($ignoreScheduleId !== null) {
            $query->where('id', '!=', $ignoreScheduleId);
        }

        if ($lock) {
            $query->lockForUpdate();
        }

        $messages = [];

        foreach ($query->get() as $other) {
            $subject = $other->subject?->name ?? 'matakuliah lain';

            if ($other->roomId === $roomId) {
                $messages[] = 'Ruang '.($other->room?->name ?? '').' sudah dipakai '.$subject.'.';
            }
            if ($lecturerId !== null && $other->lecturerId === $lecturerId) {
                $messages[] = 'Dosen '.($other->lecturer?->username ?? '').' sudah mengajar '.$subject.' pada jam yang sama.';
            }
        }

        return $messages;
    }
}
