<?php

namespace App\Http\Controllers;

use App\Domain\Scheduling\Actions\EvaluateSchedulingAction;
use App\Domain\Scheduling\DTO\RescheduleRequest;
use App\Domain\Scheduling\Enums\DayOfWeek;
use App\Domain\Scheduling\Enums\Scope;
use App\Domain\Scheduling\ValueObjects\TimeSlotGrid;
use App\Models\Building;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SchedulingController extends Controller
{
    public function catalog()
    {
        $slots = TimeSlotGrid::generate(
            (int) Setting::get('slot_duration_minutes', 50),
            (int) Setting::get('min_start_hour', 7),
            (int) Setting::get('max_end_hour', 20),
            (string) Setting::get('lunch_break_start', '12:00'),
            (string) Setting::get('lunch_break_end', '13:00'),
        );

        $rooms = Room::query()
            ->with('building:id,code,name')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (Room $room) => [
                'id' => $room->id,
                'code' => $room->code,
                'name' => $room->name,
                'capacity' => $room->capacity,
                'type' => $room->type,
                'floor' => $room->floor,
                'building' => $room->building ? [
                    'id' => $room->building->id,
                    'code' => $room->building->code,
                    'name' => $room->building->name,
                ] : null,
            ]);

        $schedules = Schedule::query()
            ->with(['subject:id,name,credits', 'lecturer:id,username', 'room:id,name,building_id'])
            ->whereNull('deleted_at')
            ->orderBy('day')
            ->orderBy('startSlot')
            ->get()
            ->map(function (Schedule $schedule) use ($slots) {
                $start = $slots[$schedule->startSlot] ?? null;
                $end = $slots[$schedule->endSlot] ?? null;

                return [
                    'id' => $schedule->id,
                    'day' => DayOfWeek::from($schedule->day)->label(),
                    'start_slot' => $schedule->startSlot,
                    'end_slot' => $schedule->endSlot,
                    'start_time' => $start['start_time'] ?? null,
                    'end_time' => $end['end_time'] ?? null,
                    'subject' => $schedule->subject?->name ?? 'Matakuliah',
                    'credits' => $schedule->subject?->credits ?? 0,
                    'lecturer' => $schedule->lecturer?->username ?? '',
                    'room_id' => $schedule->roomId,
                    'room' => $schedule->room?->name ?? '',
                    'building_id' => $schedule->room?->building_id,
                ];
            });

        return response()->json([
            'buildings' => Building::query()->orderBy('name')->get(['id', 'code', 'name', 'floors_count']),
            'rooms' => $rooms,
            'schedules' => $schedules,
        ]);
    }

    public function evaluate(Request $request, EvaluateSchedulingAction $action)
    {
        $validated = $request->validate([
            'scheduleId' => ['required', 'uuid', Rule::exists('schedule', 'id')->whereNull('deleted_at')],
            'scope' => 'required|in:once,onwards',
            'target' => 'required|date',
        ]);

        $rescheduleRequest = new RescheduleRequest(
            scheduleId: $validated['scheduleId'],
            scope: Scope::from($validated['scope']),
            target: $validated['target'],
        );

        $result = $action->execute($rescheduleRequest);

        return response()->json($this->removeStudentIdentifiers($result));
    }

    public function evaluateStream(Request $request, EvaluateSchedulingAction $action): StreamedResponse
    {
        $validated = $request->validate([
            'scheduleId' => ['required', 'uuid', Rule::exists('schedule', 'id')->whereNull('deleted_at')],
            'scope' => 'required|in:once,onwards',
            'target' => 'required|date',
        ]);

        $rescheduleRequest = new RescheduleRequest(
            scheduleId: $validated['scheduleId'],
            scope: Scope::from($validated['scope']),
            target: $validated['target'],
        );

        return new StreamedResponse(function () use ($action, $rescheduleRequest) {
            foreach ($action->executeStream($rescheduleRequest) as $data) {
                echo 'data: '.json_encode($this->removeStudentIdentifiers($data))."\n\n";
                ob_flush();
                flush();
                usleep(100000);
            }

            echo "event: done\ndata: \n\n";
            ob_flush();
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    private function removeStudentIdentifiers(array $data): array
    {
        foreach ($data as $key => $value) {
            if (in_array($key, ['student_ids', 'studentIds', 'conflicting_student_ids'], true)) {
                unset($data[$key]);

                continue;
            }

            if (is_array($value)) {
                $data[$key] = $this->removeStudentIdentifiers($value);
            }
        }

        return $data;
    }
}
