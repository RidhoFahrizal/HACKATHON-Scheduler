<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Scheduling\Actions\ApplyRescheduleAction;
use App\Domain\Scheduling\Enums\DayOfWeek;
use App\Domain\Scheduling\Enums\RescheduleStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\RescheduleRequestResource;
use App\Models\RescheduleRequest;
use App\Models\Room;
use App\Models\Schedule;
use App\Services\AuditTrail;
use App\Services\DemoActorResolver;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class RescheduleRequestController extends Controller
{
    public function __construct(
        private readonly DemoActorResolver $actorResolver,
        private readonly ApplyRescheduleAction $applyAction,
        private readonly AuditTrail $auditTrail,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = RescheduleRequest::with([
            'schedule.subject',
            'schedule.lecturer',
            'schedule.room',
            'targetRoom',
            'requester.role',
        ])->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('role')) {
            $roleCode = $request->query('role');
            $query->whereHas('requester.role', fn ($q) => $q->where('code', $roleCode));
        }

        return RescheduleRequestResource::collection($query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scheduleId' => ['required', 'uuid', 'exists:schedule,id'],
            'targetDate' => ['required', 'date'],
            'targetDay' => ['nullable', 'string', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu'],
            'targetStartTime' => ['required', 'string'],
            'targetEndTime' => ['required', 'string'],
            'targetRoomId' => ['nullable', 'uuid', 'exists:room,id'],
            'targetRoomName' => ['nullable', 'string', 'max:255'],
            'durationType' => ['nullable', 'string', 'in:1_minggu,2_minggu,3_minggu,4_minggu,permanen'],
            'reason' => ['required', 'string', 'max:1000'],
            'urgency' => ['nullable', 'string', 'in:Normal,Tinggi,Mendesak'],
        ]);

        $targetRoomId = $this->resolveRoomId($validated);
        if (! $targetRoomId) {
            return response()->json(['message' => 'Ruangan tujuan tidak valid atau tidak ditemukan.'], 422);
        }

        $targetDay = $validated['targetDay'] ?? $this->deriveDayName($validated['targetDate']);
        $currentRole = $this->actorResolver->roleFor($request);
        $user = $this->actorResolver->userFor($currentRole);

        $reschedule = DB::transaction(function () use ($validated, $targetRoomId, $targetDay, $user) {
            $lastReq = RescheduleRequest::lockForUpdate()->orderByDesc('created_at')->first();
            $nextSeq = 1;
            if ($lastReq && preg_match('/REQ-(\d+)/', $lastReq->request_code, $matches)) {
                $nextSeq = (int) $matches[1] + 1;
            }
            $requestCode = sprintf('REQ-%04d', $nextSeq);

            return RescheduleRequest::create([
                'request_code' => $requestCode,
                'schedule_id' => $validated['scheduleId'],
                'requester_id' => $user->id,
                'target_date' => $validated['targetDate'],
                'target_day' => $targetDay,
                'target_start_time' => $validated['targetStartTime'],
                'target_end_time' => $validated['targetEndTime'],
                'target_room_id' => $targetRoomId,
                'duration_type' => $validated['durationType'] ?? '1_minggu',
                'reason' => $validated['reason'],
                'urgency' => $validated['urgency'] ?? 'Normal',
                'status' => RescheduleStatus::Pending->value,
            ]);
        });

        $this->auditTrail->record(
            level: 'info',
            module: 'Pengajuan Jadwal',
            action: 'submit_reschedule',
            details: "Pengajuan {$reschedule->request_code} diajukan oleh {$user->name}",
            actor: $user,
            status: 'Terkirim',
        );

        if ($currentRole === 'dosen' || $currentRole === 'baak') {
            $applyResult = $this->applyAction->execute($reschedule, $user, 'Otomatis disetujui');
            if (! $applyResult['success']) {
                $reschedule->delete();

                return response()->json([
                    'message' => $applyResult['message'] ?? 'Gagal memproses permohonan.',
                    'conflicts' => $applyResult['conflicts'] ?? [],
                ], 422);
            }
        }

        return (new RescheduleRequestResource($reschedule->fresh([
            'schedule.subject',
            'schedule.lecturer',
            'schedule.room',
            'targetRoom',
            'requester.role',
        ])))
            ->response()
            ->setStatusCode(201);
    }

    public function approve(Request $request, RescheduleRequest $rescheduleRequest): JsonResponse
    {
        $role = $this->actorResolver->roleFor($request);
        if ($role === 'mahasiswa') {
            return response()->json(['message' => 'Mahasiswa tidak memiliki izin menyetujui permohonan jadwal.'], 403);
        }

        if ($rescheduleRequest->status !== RescheduleStatus::Pending->value) {
            return response()->json([
                'message' => "Permohonan tidak dapat disetujui karena berstatus {$rescheduleRequest->status}.",
            ], 409);
        }

        $user = $this->actorResolver->userFor($role);
        $reviewNotes = $request->input('reviewNotes', 'Disetujui');

        $result = $this->applyAction->execute($rescheduleRequest, $user, $reviewNotes);
        if (! $result['success']) {
            return response()->json([
                'message' => $result['message'],
                'conflicts' => $result['conflicts'] ?? [],
            ], 422);
        }

        return response()->json([
            'data' => new RescheduleRequestResource($rescheduleRequest->fresh([
                'schedule.subject',
                'schedule.lecturer',
                'schedule.room',
                'targetRoom',
                'requester.role',
            ])),
        ]);
    }

    public function reject(Request $request, RescheduleRequest $rescheduleRequest): JsonResponse
    {
        $role = $this->actorResolver->roleFor($request);
        if ($role === 'mahasiswa') {
            return response()->json(['message' => 'Mahasiswa tidak memiliki izin menolak permohonan jadwal.'], 403);
        }

        if ($rescheduleRequest->status !== RescheduleStatus::Pending->value) {
            return response()->json([
                'message' => "Permohonan tidak dapat ditolak karena berstatus {$rescheduleRequest->status}.",
            ], 409);
        }

        $validated = $request->validate([
            'reviewNotes' => ['required', 'string', 'max:500'],
        ]);

        $user = $this->actorResolver->userFor($role);

        $rescheduleRequest->update([
            'status' => RescheduleStatus::Rejected->value,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'review_notes' => $validated['reviewNotes'],
        ]);

        $this->auditTrail->record(
            level: 'shift',
            module: 'Persetujuan Jadwal',
            action: 'reject_reschedule',
            details: "Permohonan {$rescheduleRequest->request_code} ditolak: {$validated['reviewNotes']}",
            actor: $user,
            status: 'Ditolak',
        );

        return response()->json([
            'data' => new RescheduleRequestResource($rescheduleRequest->fresh([
                'schedule.subject',
                'schedule.lecturer',
                'schedule.room',
                'targetRoom',
                'requester.role',
            ])),
        ]);
    }

    private function resolveRoomId(array $data): ?string
    {
        if (! empty($data['targetRoomId'])) {
            return $data['targetRoomId'];
        }

        if (! empty($data['targetRoomName'])) {
            $name = trim($data['targetRoomName']);
            $room = Room::where('name', $name)->orWhere('code', $name)->first();
            if ($room) {
                return $room->id;
            }
        }

        return null;
    }

    private function deriveDayName(string $date): string
    {
        $carbon = Carbon::parse($date);
        $dayIndex = ($carbon->dayOfWeekIso - 1); // 0 (Mon) - 6 (Sun)
        return DayOfWeek::tryFrom($dayIndex)?->label() ?? 'Senin';
    }
}
