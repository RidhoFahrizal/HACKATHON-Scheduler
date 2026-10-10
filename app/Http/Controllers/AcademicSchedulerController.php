<?php

namespace App\Http\Controllers;

use App\Http\Resources\RescheduleRequestResource;
use App\Models\Lecturer;
use App\Models\RescheduleRequest;
use App\Models\Room;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SystemAuditLog;
use App\Services\MockAcademicService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class AcademicSchedulerController extends Controller
{
    private const ROLES = ['mahasiswa', 'dosen', 'baak'];

    public function __construct(private readonly MockAcademicService $mockAcademic) {}

    public function index(Request $request): View
    {
        $role = $request->session()->get('role', 'mahasiswa');
        $payload = $this->buildPayload($role);

        return view('scheduler.index', [
            'payload' => $payload,
        ]);
    }

    public function switchRole(Request $request): Response
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(self::ROLES)],
        ]);

        $request->session()->put('role', $validated['role']);

        return response()->noContent();
    }

    private function buildPayload(string $role): array
    {
        $base = $this->mockAcademic->payload();

        try {
            $dbRooms = Room::with('building')->where('is_active', true)->get();
            if ($dbRooms->isNotEmpty()) {
                $base['masterRooms'] = $dbRooms->map(fn ($r) => [
                    'id' => $r->id,
                    'code' => $r->code,
                    'name' => $r->name,
                    'building' => $r->building?->name ?? 'Gedung D4',
                    'capacity' => (int) $r->capacity,
                    'type' => $r->type ?? 'teori',
                    'floor' => (int) ($r->floor ?? 1),
                ])->toArray();
            }

            $dbSubjects = Subject::whereNull('deleted_at')->get();
            if ($dbSubjects->isNotEmpty()) {
                $base['masterSubjects'] = $dbSubjects->map(fn ($s) => [
                    'id' => $s->id,
                    'code' => $s->code ?? '',
                    'name' => $s->name,
                    'sks' => (int) $s->credits,
                    'semester' => $s->semester !== null ? (int) $s->semester : null,
                    'department' => $s->department ?? '',
                ])->toArray();
            }

            $dbLecturers = Lecturer::whereNull('deleted_at')->get();
            if ($dbLecturers->isNotEmpty()) {
                $base['masterLecturers'] = $dbLecturers->map(fn ($l) => [
                    'id' => $l->id,
                    'name' => $l->username,
                    'nip' => $l->nip ?? '',
                    'code' => $l->code ?? '',
                    'academic_title' => $l->academic_title ?? '',
                    'department' => $l->department ?? '',
                ])->toArray();
            }

            $dbStudents = Student::whereNull('deleted_at')->get();
            if ($dbStudents->isNotEmpty()) {
                $base['masterStudents'] = $dbStudents->map(fn ($st) => [
                    'id' => $st->id,
                    'name' => $st->username,
                    'nrp' => $st->nrp ?? '',
                    'cohort' => String($st->cohort_year ?? '2023'),
                    'major' => $st->department ?? '',
                ])->toArray();
            }

            $dbRequests = RescheduleRequest::with([
                'schedule.subject',
                'schedule.lecturer',
                'schedule.room',
                'targetRoom',
                'requester.role',
            ])->orderByDesc('created_at')->get();
            if ($dbRequests->isNotEmpty()) {
                $base['rescheduleRequests'] = RescheduleRequestResource::collection($dbRequests)->resolve();
            }

            $dbLogs = SystemAuditLog::orderByDesc('timestamp')->take(50)->get();
            if ($dbLogs->isNotEmpty()) {
                $base['systemLogs'] = $dbLogs->map(fn ($log) => [
                    'id' => $log->id,
                    'timestamp' => $log->timestamp ? $log->timestamp->format('d M Y H:i') : '',
                    'level' => $log->level,
                    'levelLabel' => ucfirst($log->level),
                    'actor' => $log->actor_name,
                    'module' => $log->module,
                    'detail' => $log->details ?? '',
                    'status' => $log->status,
                ])->toArray();
            }
        } catch (Throwable) {
            // Graceful fallback to mock data when database tables are unavailable or unmigrated.
        }

        return [
            ...$base,
            'activeRole' => $role,
            'switchRoleUrl' => route('scheduler.switch-role'),
        ];
    }
}
