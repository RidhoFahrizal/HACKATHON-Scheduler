<?php

namespace App\Services;

use App\Domain\Scheduling\Enums\DayOfWeek;
use App\Domain\Scheduling\ValueObjects\TimeSlotGrid;
use App\Models\Lecturer;
use App\Models\RescheduleRequest;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SystemAuditLog;

final class MockAcademicService
{
    private function isDatabaseReady(): bool
    {
        try {
            if (! function_exists('app') || ! app()->has('db')) {
                return false;
            }
            return \Illuminate\Support\Facades\Schema::hasTable('room')
                && Room::count() > 0;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function profiles(): array
    {
        if (! $this->isDatabaseReady()) {
            return $this->defaultProfiles();
        }

        try {
            return $this->databaseProfiles();
        } catch (\Throwable) {
            return $this->defaultProfiles();
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function rooms(): array
    {
        if (! $this->isDatabaseReady()) {
            return $this->defaultRooms();
        }

        try {
            return Room::with('building')
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->map(fn (Room $r) => [
                    'id' => $r->id,
                    'code' => $r->code,
                    'name' => $r->name,
                    'building' => $r->building?->name ?? 'Gedung D4',
                    'capacity' => $r->capacity,
                    'type' => $r->type,
                    'floor' => $r->floor,
                ])
                ->toArray();
        } catch (\Throwable) {
            return $this->defaultRooms();
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function subjects(): array
    {
        if (! $this->isDatabaseReady()) {
            return $this->defaultSubjects();
        }

        try {
            return Subject::whereNull('deleted_at')
                ->orderBy('name')
                ->get()
                ->map(fn (Subject $s) => [
                    'id' => $s->id,
                    'code' => $s->code ?? 'MK-'.$s->id,
                    'name' => $s->name,
                    'sks' => $s->credits,
                ])
                ->toArray();
        } catch (\Throwable) {
            return $this->defaultSubjects();
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function lecturers(): array
    {
        if (! $this->isDatabaseReady()) {
            return $this->defaultLecturers();
        }

        try {
            return Lecturer::whereNull('deleted_at')
                ->orderBy('username')
                ->get()
                ->map(fn (Lecturer $l) => [
                    'id' => $l->id,
                    'name' => $l->username,
                    'nip' => $l->nip ?? '',
                    'email' => $l->email ?? '',
                    'department' => $l->department ?? '',
                ])
                ->toArray();
        } catch (\Throwable) {
            return $this->defaultLecturers();
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function students(): array
    {
        if (! $this->isDatabaseReady()) {
            return $this->defaultStudents();
        }

        try {
            return Student::whereNull('deleted_at')
                ->orderBy('username')
                ->get()
                ->map(fn (Student $s) => [
                    'id' => $s->id,
                    'name' => $s->username,
                    'nrp' => $s->nrp ?? '',
                    'cohort' => (string) ($s->cohort_year ?? '2022'),
                    'major' => $s->department ?? 'D4 Teknik Informatika',
                    'class' => $s->class,
                ])
                ->toArray();
        } catch (\Throwable) {
            return $this->defaultStudents();
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function rescheduleRequests(): array
    {
        if (! $this->isDatabaseReady()) {
            return $this->defaultRescheduleRequests();
        }

        try {
            return RescheduleRequest::with([
                'schedule.subject',
                'schedule.lecturer',
                'schedule.room',
                'targetRoom',
                'requester.role',
            ])
                ->orderByDesc('created_at')
                ->get()
                ->map(function (RescheduleRequest $r) {
                    $originalDay = $r->schedule ? (DayOfWeek::tryFrom($r->schedule->day)?->label() ?? 'Senin') : 'Senin';
                    $origRoom = $r->schedule?->room?->name ?? 'Ruangan';

                    return [
                        'id' => $r->id,
                        'requestCode' => $r->request_code,
                        'courseId' => $r->schedule_id,
                        'courseTitle' => $r->schedule?->subject?->name ?? 'Matakuliah',
                        'lecturerName' => $r->schedule?->lecturer?->username ?? '',
                        'requesterRole' => $r->requester?->role?->name ?? 'Mahasiswa',
                        'requesterName' => $r->requester?->name ?? 'Pengaju',
                        'originalSchedule' => "{$originalDay} ({$origRoom})",
                        'proposedSchedule' => "{$r->target_day}, {$r->target_start_time} - {$r->target_end_time} (" . ($r->targetRoom?->name ?? '') . ')',
                        'reason' => $r->reason,
                        'status' => $r->status === 'approved' ? 'Disetujui' : ($r->status === 'rejected' ? 'Ditolak' : 'Menunggu Persetujuan Dosen'),
                        'submittedAt' => $r->created_at?->format('d M Y H:i') ?? now()->format('d M Y H:i'),
                    ];
                })
                ->toArray();
        } catch (\Throwable) {
            return $this->defaultRescheduleRequests();
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function studentRoster(): array
    {
        if (! $this->isDatabaseReady()) {
            return $this->defaultStudentRoster();
        }

        try {
            $students = Student::where('class', '3 D4 IT A')->orderBy('username')->get();
            if ($students->isEmpty()) {
                $students = Student::take(20)->get();
            }

            return $students->values()->map(fn (Student $s, int $idx) => [
                'no' => $idx + 1,
                'name' => $s->username,
                'nrp' => $s->nrp ?? "1234567{$idx}",
                'class' => $s->class,
            ])->toArray();
        } catch (\Throwable) {
            return $this->defaultStudentRoster();
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function systemLogs(): array
    {
        if (! $this->isDatabaseReady()) {
            return $this->defaultSystemLogs();
        }

        try {
            $logs = SystemAuditLog::orderByDesc('timestamp')->take(50)->get();
            if ($logs->isEmpty()) {
                return $this->defaultSystemLogs();
            }

            return $logs->map(fn (SystemAuditLog $log) => [
                'id' => $log->id,
                'timestamp' => $log->timestamp?->format('d M Y H:i') ?? now()->format('d M Y H:i'),
                'level' => $log->level,
                'levelLabel' => match ($log->level) {
                    'shift' => 'Perubahan Jadwal',
                    'sync' => 'Sinkronisasi CSV',
                    'warning' => 'Peringatan',
                    'error' => 'Error',
                    default => 'Info',
                },
                'actor' => $log->actor_name,
                'module' => $log->module,
                'detail' => $log->details ?? $log->action,
                'status' => $log->status,
            ])->toArray();
        } catch (\Throwable) {
            return $this->defaultSystemLogs();
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function recommendationSlots(): array
    {
        return [
            [
                'day' => 'Rabu',
                'time' => '13:00 - 16:00',
                'room' => 'Lab C 103',
                'note' => 'Bebas Sesi Praktikum Lain',
            ],
            [
                'day' => 'Kamis',
                'time' => '13:00 - 15:00',
                'room' => 'SAW-06.10',
                'note' => 'Kapasitas 40 Kursi Mahasiswa',
            ],
            [
                'day' => 'Jumat',
                'time' => '08:00 - 11:00',
                'room' => 'Lab C 102',
                'note' => 'Slot Pagi Kosong & Workstation Siap',
            ],
            [
                'day' => 'Selasa',
                'time' => '15:00 - 17:00',
                'room' => 'Lab Software SAW-08',
                'note' => 'Bebas Bentrok Dosen',
            ],
            [
                'day' => 'Senin',
                'time' => '13:00 - 15:00',
                'room' => 'Lab Sinyal B 204',
                'note' => 'Laboratorium Tersedia Penuh',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'appData' => $this->profiles(),
            'masterRooms' => $this->rooms(),
            'masterSubjects' => $this->subjects(),
            'masterLecturers' => $this->lecturers(),
            'masterStudents' => $this->students(),
            'rescheduleRequests' => $this->rescheduleRequests(),
            'studentRoster' => $this->studentRoster(),
            'systemLogs' => $this->systemLogs(),
            'recommendationSlots' => $this->recommendationSlots(),
        ];
    }

    private function databaseProfiles(): array
    {
        $slots = TimeSlotGrid::generate(
            (int) Setting::get('slot_duration_minutes', 50),
            (int) Setting::get('min_start_hour', 7),
            (int) Setting::get('max_end_hour', 20),
            (string) Setting::get('lunch_break_start', '12:00'),
            (string) Setting::get('lunch_break_end', '13:00'),
        );

        $allSchedules = Schedule::with(['subject', 'lecturer', 'room', 'bookings'])
            ->whereNull('deleted_at')
            ->orderBy('day')
            ->orderBy('startSlot')
            ->get();

        $pendingRequests = RescheduleRequest::with(['requester', 'targetRoom'])
            ->where('status', 'pending')
            ->get()
            ->keyBy('schedule_id');

        $formatSchedule = function (Schedule $s) use ($slots, $pendingRequests) {
            $start = $slots[$s->startSlot]['start_time'] ?? sprintf('%02d:00', 8 + $s->startSlot);
            $end = $slots[$s->endSlot]['end_time'] ?? sprintf('%02d:00', 10 + $s->endSlot);
            $startH = (int) explode(':', $start)[0];
            $endH = (int) explode(':', $end)[0];
            $duration = max(1, $endH - $startH);

            $hasShift = $s->bookings->isNotEmpty();
            $shiftedSchedule = '';
            if ($hasShift) {
                $b = $s->bookings->first();
                $bDay = DayOfWeek::tryFrom($b->day)?->label() ?? 'Rabu';
                $bStart = $slots[$b->startSlot]['start_time'] ?? '13:00';
                $bEnd = $slots[$b->endSlot]['end_time'] ?? '16:00';
                $bRoom = $b->room?->name ?? 'Lab C 103';
                $shiftedSchedule = "{$bDay}, {$bStart} - {$bEnd} di {$bRoom} (Jadwal Pengganti)";
            }

            $pending = $pendingRequests->get($s->id);
            $hasPending = $pending !== null;
            $pendingDetail = null;
            if ($hasPending) {
                $pendingDetail = [
                    'id' => $pending->id,
                    'requestCode' => $pending->request_code,
                    'requester' => $pending->requester?->name ?? 'Mahasiswa',
                    'targetDay' => $pending->target_day,
                    'targetTime' => "{$pending->target_start_time} - {$pending->target_end_time}",
                    'targetRoom' => $pending->targetRoom?->name ?? 'Ruang',
                    'reason' => $pending->reason,
                ];
            }

            return [
                'id' => $s->id,
                'code' => $s->subject?->code ?? 'MK',
                'title' => $s->subject?->name ?? 'Matakuliah',
                'lecturer' => $s->lecturer?->username ?? '',
                'day' => DayOfWeek::tryFrom($s->day)?->label() ?? 'Senin',
                'time' => "{$start} - {$end}",
                'startHour' => $startH,
                'durationHours' => $duration,
                'room' => $s->room?->name ?? '',
                'sks' => "{$s->subject?->credits} SKS",
                'hasShift' => $hasShift,
                'shiftedSchedule' => $shiftedSchedule,
                'hasPendingRequest' => $hasPending,
                'pendingRequestDetail' => $pendingDetail,
            ];
        };

        // Student schedules (enrolled or 3 D4 IT A)
        $mhsStudent = Student::where('email', 'mhs.alpha@student.pens.ac.id')->first()
            ?? Student::where('class', '3 D4 IT A')->first()
            ?? Student::first();

        $enrolledSubjectIds = $mhsStudent
            ? $mhsStudent->subjects()->pluck('subject.id')->all()
            : [];

        $mhsClasses = $allSchedules
            ->filter(fn (Schedule $s) => in_array($s->subjectId, $enrolledSubjectIds, true))
            ->map($formatSchedule)
            ->values()
            ->all();

        if (empty($mhsClasses)) {
            $mhsClasses = $allSchedules->take(9)->map($formatSchedule)->values()->all();
        }

        // Lecturer schedules (Dosen Alpha or matching)
        $dosen = Lecturer::where('email', 'dosen.alpha@pens.ac.id')->first()
            ?? Lecturer::first();

        $dosenClasses = $allSchedules
            ->filter(fn (Schedule $s) => $dosen && $s->lecturerId === $dosen->id)
            ->map($formatSchedule)
            ->values()
            ->all();

        if (empty($dosenClasses)) {
            $dosenClasses = $allSchedules->take(7)->map($formatSchedule)->values()->all();
        }

        // BAAK stats & all classes
        $baakClasses = $allSchedules->map($formatSchedule)->values()->all();

        return [
            'mahasiswa' => [
                'id' => 'mahasiswa',
                'name' => $mhsStudent?->username ?? 'Mahasiswa Alpha',
                'roleLabel' => 'Mahasiswa',
                'departmentClass' => $mhsStudent ? "{$mhsStudent->class}" : '3 D4 IT A',
                'idType' => 'NRP',
                'idNumber' => $mhsStudent?->nrp ?? '3122000001',
                'email' => $mhsStudent?->email ?? 'mhs.alpha@student.pens.ac.id',
                'avatarChar' => strtoupper(substr($mhsStudent?->username ?? 'M', 0, 1)),
                'classes' => $mhsClasses,
            ],
            'dosen' => [
                'id' => 'dosen',
                'name' => $dosen?->username ?? 'Dosen Alpha, S.Kom., M.T.',
                'roleLabel' => 'Dosen',
                'departmentClass' => $dosen?->department ?? 'Departemen Teknik Informatika',
                'idType' => 'NIP',
                'idNumber' => $dosen?->nip ?? '198001012005011001',
                'email' => $dosen?->email ?? 'dosen.alpha@pens.ac.id',
                'avatarChar' => strtoupper(substr($dosen?->username ?? 'D', 0, 1)),
                'classes' => $dosenClasses,
            ],
            'baak' => [
                'id' => 'baak',
                'name' => 'Biro Administrasi Akademik',
                'roleLabel' => 'BAAK',
                'departmentClass' => 'Pusat Pelayanan & Penjadwalan',
                'idType' => 'Unit',
                'idNumber' => 'BAAK-PENS-01',
                'email' => 'baak@pens.ac.id',
                'avatarChar' => 'B',
                'stats' => [
                    'mahasiswa' => Student::count(),
                    'dosen' => Lecturer::count(),
                    'matakuliah' => Subject::count(),
                    'ruangan' => Room::where('is_active', true)->count(),
                ],
                'classes' => $baakClasses,
            ],
        ];
    }

    private function defaultProfiles(): array
    {
        return [
            'mahasiswa' => [
                'id' => 'mahasiswa',
                'name' => 'Mahasiswa Alpha',
                'roleLabel' => 'Mahasiswa',
                'departmentClass' => '3 D4 IT A',
                'idType' => 'NRP',
                'idNumber' => '3122000001',
                'email' => 'mhs.alpha@student.pens.ac.id',
                'avatarChar' => 'M',
                'classes' => [
                    [
                        'id' => 'm-wm',
                        'code' => 'WM',
                        'title' => 'Workshop Mesin Pembelajaran',
                        'lecturer' => 'Dosen Alpha, S.Kom., M.T.',
                        'day' => 'Senin',
                        'time' => '08:00 - 11:00',
                        'startHour' => 8,
                        'durationHours' => 3,
                        'room' => 'Lab C 102',
                        'sks' => '3 SKS',
                        'hasShift' => true,
                        'shiftedSchedule' => 'Rabu, 13:00 - 16:00 di Lab C 103 (Jadwal Pengganti)',
                    ],
                    [
                        'id' => 'm-pmj',
                        'code' => 'PMJ',
                        'title' => 'Pemrograman Jaringan Lanjut',
                        'lecturer' => 'Dosen Beta, S.Kom., M.T.',
                        'day' => 'Senin',
                        'time' => '13:00 - 16:00',
                        'startHour' => 13,
                        'durationHours' => 3,
                        'room' => 'Lab C 105',
                        'sks' => '3 SKS',
                        'hasShift' => false,
                    ],
                    [
                        'id' => 'm-met',
                        'code' => 'MET',
                        'title' => 'Metodologi Penelitian Rekayasa',
                        'lecturer' => 'Dosen Zeta, Ph.D.',
                        'day' => 'Selasa',
                        'time' => '08:00 - 10:00',
                        'startHour' => 8,
                        'durationHours' => 2,
                        'room' => 'SAW-05.02',
                        'sks' => '2 SKS',
                        'hasShift' => false,
                    ],
                    [
                        'id' => 'm-kw',
                        'code' => 'KW',
                        'title' => 'Kewirausahaan Teknologi',
                        'lecturer' => 'Dosen Gamma, S.Kom., M.T.',
                        'day' => 'Selasa',
                        'time' => '11:00 - 13:00',
                        'startHour' => 11,
                        'durationHours' => 2,
                        'room' => 'SAW-06.10',
                        'sks' => '2 SKS',
                        'hasShift' => false,
                    ],
                    [
                        'id' => 'm-k3l',
                        'code' => 'K3L',
                        'title' => 'Keamanan, Keselamatan & K3L',
                        'lecturer' => 'Dosen Epsilon, S.Kom., M.Kom.',
                        'day' => 'Rabu',
                        'time' => '15:00 - 17:00',
                        'startHour' => 15,
                        'durationHours' => 2,
                        'room' => 'SAW-06.10',
                        'sks' => '2 SKS',
                        'hasShift' => false,
                    ],
                    [
                        'id' => 'm-pa1',
                        'code' => 'PA1',
                        'title' => 'Proyek Akhir Tahap 1',
                        'lecturer' => 'Dosen Delta, S.ST., M.Tr.Kom.',
                        'day' => 'Kamis',
                        'time' => '09:00 - 12:00',
                        'startHour' => 9,
                        'durationHours' => 3,
                        'room' => 'Lab Software SAW-08',
                        'sks' => '4 SKS',
                        'hasShift' => false,
                    ],
                    [
                        'id' => 'm-pcd',
                        'code' => 'PCD',
                        'title' => 'Pengolahan Citra Digital',
                        'lecturer' => 'Dosen Epsilon, S.Kom., M.Kom.',
                        'day' => 'Kamis',
                        'time' => '13:00 - 15:00',
                        'startHour' => 13,
                        'durationHours' => 2,
                        'room' => 'D4-201',
                        'sks' => '2 SKS',
                        'hasShift' => false,
                    ],
                    [
                        'id' => 'm-kp',
                        'code' => 'KP',
                        'title' => 'Kerja Praktek Industri',
                        'lecturer' => 'Dosen Delta, S.ST., M.Tr.Kom.',
                        'day' => 'Jumat',
                        'time' => '08:00 - 10:00',
                        'startHour' => 8,
                        'durationHours' => 2,
                        'room' => 'Lab Sinyal B 204',
                        'sks' => '2 SKS',
                        'hasShift' => false,
                    ],
                    [
                        'id' => 'm-bik',
                        'code' => 'BIK',
                        'title' => 'Bahasa Inggris Komunikasi Profesi',
                        'lecturer' => 'Dosen Eta, Ph.D.',
                        'day' => 'Jumat',
                        'time' => '10:00 - 12:00',
                        'startHour' => 10,
                        'durationHours' => 2,
                        'room' => 'B-101',
                        'sks' => '2 SKS',
                        'hasShift' => false,
                    ],
                ],
            ],
            'dosen' => [
                'id' => 'dosen',
                'name' => 'Dosen Alpha, S.Kom., M.T.',
                'roleLabel' => 'Dosen',
                'departmentClass' => 'Departemen Teknik Informatika',
                'idType' => 'NIP',
                'idNumber' => '198001012005011001',
                'email' => 'dosen.alpha@pens.ac.id',
                'avatarChar' => 'D',
                'classes' => [
                    [
                        'id' => 'd-wma',
                        'code' => 'WM-A',
                        'title' => 'Workshop Mesin Pembelajaran (3 D4 IT A)',
                        'lecturer' => 'Dosen Alpha, S.Kom., M.T.',
                        'day' => 'Senin',
                        'time' => '08:00 - 11:00',
                        'startHour' => 8,
                        'durationHours' => 3,
                        'room' => 'Lab C 102',
                        'sks' => '3 SKS',
                        'hasShift' => false,
                        'hasPendingRequest' => true,
                        'pendingRequestDetail' => [
                            'id' => 'REQ-01',
                            'requester' => 'Mahasiswa Alpha (3 D4 IT A)',
                            'targetDay' => 'Rabu',
                            'targetTime' => '13:00 - 16:00',
                            'targetRoom' => 'Lab C 103',
                            'reason' => 'Tabrakan jadwal ujian sertifikasi internasional',
                        ],
                    ],
                    [
                        'id' => 'd-wmb',
                        'code' => 'WM-B',
                        'title' => 'Workshop Mesin Pembelajaran (3 D4 IT B)',
                        'lecturer' => 'Dosen Alpha, S.Kom., M.T.',
                        'day' => 'Selasa',
                        'time' => '08:00 - 11:00',
                        'startHour' => 8,
                        'durationHours' => 3,
                        'room' => 'Lab C 102',
                        'sks' => '3 SKS',
                        'hasShift' => false,
                    ],
                    [
                        'id' => 'd-pba',
                        'code' => 'PBA',
                        'title' => 'Pengolahan Bahasa Alami (3 D4 IT A)',
                        'lecturer' => 'Dosen Alpha, S.Kom., M.T.',
                        'day' => 'Selasa',
                        'time' => '13:00 - 15:00',
                        'startHour' => 13,
                        'durationHours' => 2,
                        'room' => 'SAW-06.10',
                        'sks' => '2 SKS',
                        'hasShift' => false,
                    ],
                    [
                        'id' => 'd-kcka',
                        'code' => 'KCK-A',
                        'title' => 'Kecerdasan Komputasional (4 D4 IT A)',
                        'lecturer' => 'Dosen Alpha, S.Kom., M.T.',
                        'day' => 'Rabu',
                        'time' => '08:00 - 11:00',
                        'startHour' => 8,
                        'durationHours' => 3,
                        'room' => 'Lab C 104',
                        'sks' => '3 SKS',
                        'hasShift' => false,
                    ],
                    [
                        'id' => 'd-kckb',
                        'code' => 'KCK-B',
                        'title' => 'Kecerdasan Komputasional (4 D4 IT B)',
                        'lecturer' => 'Dosen Alpha, S.Kom., M.T.',
                        'day' => 'Kamis',
                        'time' => '08:00 - 11:00',
                        'startHour' => 8,
                        'durationHours' => 3,
                        'room' => 'Lab C 104',
                        'sks' => '3 SKS',
                        'hasShift' => true,
                        'shiftedSchedule' => 'Jumat, 13:00 - 16:00 di Lab C 105 (Jadwal Pengganti)',
                    ],
                    [
                        'id' => 'd-pms',
                        'code' => 'PMS',
                        'title' => 'Pemodelan & Simulasi Sistem (2 D4 IT A)',
                        'lecturer' => 'Dosen Alpha, S.Kom., M.T.',
                        'day' => 'Kamis',
                        'time' => '13:00 - 15:00',
                        'startHour' => 13,
                        'durationHours' => 2,
                        'room' => 'SAW-05.02',
                        'sks' => '2 SKS',
                        'hasShift' => false,
                        'hasPendingRequest' => true,
                        'pendingRequestDetail' => [
                            'id' => 'REQ-11',
                            'requester' => 'Kadet Alpha (2 D4 IT A)',
                            'targetDay' => 'Selasa',
                            'targetTime' => '15:00 - 17:00',
                            'targetRoom' => 'SAW-05.02',
                            'reason' => 'Kegiatan perlombaan robotika nasional',
                        ],
                    ],
                    [
                        'id' => 'd-pm',
                        'code' => 'PM',
                        'title' => 'Pembelajaran Mendalam (Pascasarjana)',
                        'lecturer' => 'Dosen Alpha, S.Kom., M.T.',
                        'day' => 'Jumat',
                        'time' => '08:00 - 11:00',
                        'startHour' => 8,
                        'durationHours' => 3,
                        'room' => 'Lab Riset Lt 3',
                        'sks' => '3 SKS',
                        'hasShift' => false,
                    ],
                ],
            ],
            'baak' => [
                'id' => 'baak',
                'name' => 'Biro Administrasi Akademik',
                'roleLabel' => 'BAAK',
                'departmentClass' => 'Pusat Pelayanan & Penjadwalan',
                'idType' => 'Unit',
                'idNumber' => 'BAAK-PENS-01',
                'email' => 'baak@pens.ac.id',
                'avatarChar' => 'B',
                'stats' => [
                    'mahasiswa' => 1420,
                    'dosen' => 86,
                    'matakuliah' => 248,
                    'ruangan' => 42,
                ],
                'classes' => [
                    [
                        'id' => 'b-wm-a',
                        'code' => 'WM-A',
                        'title' => 'Workshop Mesin Pembelajaran (3 D4 IT A)',
                        'lecturer' => 'Dosen Alpha, S.Kom., M.T.',
                        'day' => 'Senin',
                        'time' => '08:00 - 11:00',
                        'startHour' => 8,
                        'durationHours' => 3,
                        'room' => 'Lab C 102',
                        'sks' => '3 SKS',
                        'hasShift' => true,
                        'shiftedSchedule' => 'Rabu, 13:00 - 16:00 di Lab C 103',
                    ],
                    [
                        'id' => 'b-pmj-a',
                        'code' => 'PMJ-A',
                        'title' => 'Pemrograman Jaringan Lanjut (2 D4 IT A)',
                        'lecturer' => 'Dosen Beta, S.Kom., M.T.',
                        'day' => 'Senin',
                        'time' => '13:00 - 16:00',
                        'startHour' => 13,
                        'durationHours' => 3,
                        'room' => 'Lab C 105',
                        'sks' => '3 SKS',
                        'hasShift' => false,
                    ],
                ],
            ],
        ];
    }

    private function defaultRooms(): array
    {
        return [
            ['code' => 'C-102', 'name' => 'Ruang Workshop Komputer C-102', 'building' => 'Gedung D4'],
            ['code' => 'C-103', 'name' => 'Laboratorium Jaringan & IoT', 'building' => 'Gedung D4'],
            ['code' => 'C-104', 'name' => 'Laboratorium Data Science & AI', 'building' => 'Gedung D4'],
            ['code' => 'C-105', 'name' => 'Laboratorium Rekayasa Perangkat Lunak', 'building' => 'Gedung D4'],
            ['code' => 'SAW-06.10', 'name' => 'Ruang Kuliah Teori Pascasarjana SAW-06.10', 'building' => 'Gedung SAW'],
            ['code' => 'SAW-08', 'name' => 'Laboratorium Software Terpadu SAW-08', 'building' => 'Gedung SAW'],
            ['code' => 'SAW-05.02', 'name' => 'Ruang Diskusi & Seminar SAW-05.02', 'building' => 'Gedung SAW'],
            ['code' => 'B-101', 'name' => 'Ruang Laboratorium Bahasa B-101', 'building' => 'Gedung D3'],
            ['code' => 'B-204', 'name' => 'Laboratorium Pemrosesan Sinyal B-204', 'building' => 'Gedung D3'],
            ['code' => 'D4-201', 'name' => 'Ruang Teori Multimedia D4-201', 'building' => 'Gedung D4'],
            ['code' => 'Lab Riset Lt 3', 'name' => 'Laboratorium Riset Terapan', 'building' => 'Gedung Pasca'],
        ];
    }

    private function defaultSubjects(): array
    {
        return [
            ['code' => 'WMP301', 'name' => 'Workshop Mesin Pembelajaran', 'sks' => 3],
            ['code' => 'PMJ301', 'name' => 'Pemrograman Jaringan Lanjut', 'sks' => 3],
            ['code' => 'KCK301', 'name' => 'Kecerdasan Komputasional', 'sks' => 3],
            ['code' => 'PRO401', 'name' => 'Proyek Akhir Tahap 1', 'sks' => 4],
            ['code' => 'KPR201', 'name' => 'Kerja Praktek Industri', 'sks' => 2],
            ['code' => 'KWR201', 'name' => 'Kewirausahaan Teknologi', 'sks' => 2],
            ['code' => 'K3L201', 'name' => 'Keamanan, Keselamatan & K3L', 'sks' => 2],
            ['code' => 'PCD201', 'name' => 'Pengolahan Citra Digital', 'sks' => 2],
            ['code' => 'PBA201', 'name' => 'Pengolahan Bahasa Alami', 'sks' => 2],
            ['code' => 'MET201', 'name' => 'Metodologi Penelitian Rekayasa', 'sks' => 2],
            ['code' => 'PMS201', 'name' => 'Pemodelan & Simulasi Sistem', 'sks' => 2],
            ['code' => 'BIK201', 'name' => 'Bahasa Inggris Komunikasi Profesi', 'sks' => 2],
        ];
    }

    private function defaultLecturers(): array
    {
        return [
            ['name' => 'Dosen Alpha, S.Kom., M.T.', 'nip' => '198001012005011001'],
            ['name' => 'Dosen Beta, S.Kom., M.T.', 'nip' => '198202022006021002'],
            ['name' => 'Dosen Gamma, S.Kom., M.T.', 'nip' => '197903032005011003'],
            ['name' => 'Dosen Delta, S.ST., M.Tr.Kom.', 'nip' => '198804042015042004'],
            ['name' => 'Dosen Epsilon, S.Kom., M.Kom.', 'nip' => '198505052010121005'],
            ['name' => 'Dosen Zeta, Ph.D.', 'nip' => '197006061995121006'],
            ['name' => 'Dosen Eta, Ph.D.', 'nip' => '197207071999031007'],
        ];
    }

    private function defaultStudents(): array
    {
        return [
            ['name' => 'Mahasiswa Alpha', 'nrp' => '3122000001', 'cohort' => '2022', 'major' => 'D4 Teknik Informatika'],
            ['name' => 'Mahasiswa Beta', 'nrp' => '3122000002', 'cohort' => '2022', 'major' => 'D4 Teknik Informatika'],
            ['name' => 'Mahasiswa Gamma', 'nrp' => '3122000003', 'cohort' => '2022', 'major' => 'D4 Teknik Informatika'],
            ['name' => 'Mahasiswa Delta', 'nrp' => '3122000004', 'cohort' => '2022', 'major' => 'D4 Teknik Informatika'],
            ['name' => 'Mahasiswa Epsilon', 'nrp' => '3122000005', 'cohort' => '2022', 'major' => 'D4 Teknik Informatika'],
            ['name' => 'Mahasiswa Zeta', 'nrp' => '3122000006', 'cohort' => '2022', 'major' => 'D4 Teknik Informatika'],
            ['name' => 'Mahasiswa Eta', 'nrp' => '3122000007', 'cohort' => '2022', 'major' => 'D4 Teknik Informatika'],
            ['name' => 'Kadet Alpha', 'nrp' => '3123000201', 'cohort' => '2023', 'major' => 'D4 Teknik Informatika'],
            ['name' => 'Mahasiswa Theta', 'nrp' => '3122000008', 'cohort' => '2022', 'major' => 'D4 Teknik Informatika'],
            ['name' => 'Mahasiswa Iota', 'nrp' => '3122000009', 'cohort' => '2022', 'major' => 'D4 Teknik Informatika'],
            ['name' => 'Mahasiswa Kappa', 'nrp' => '3122000010', 'cohort' => '2022', 'major' => 'D4 Teknik Informatika'],
            ['name' => 'Kadet Beta', 'nrp' => '3123000202', 'cohort' => '2023', 'major' => 'D4 Teknik Informatika'],
        ];
    }

    private function defaultRescheduleRequests(): array
    {
        return [
            [
                'id' => 'REQ-01',
                'courseId' => 'm-wm',
                'courseTitle' => 'Workshop Mesin Pembelajaran (3 D4 IT A)',
                'lecturerName' => 'Dosen Alpha, S.Kom., M.T.',
                'requesterRole' => 'Mahasiswa',
                'requesterName' => 'Mahasiswa Alpha',
                'originalSchedule' => 'Senin, 08:00 - 11:00 (Lab C 102)',
                'proposedSchedule' => 'Rabu, 13:00 - 16:00 (Lab C 103)',
                'reason' => 'Tabrakan jadwal ujian sertifikasi internasional',
                'status' => 'Menunggu Persetujuan Dosen',
                'submittedAt' => '09 Okt 2026 08:30',
            ],
            [
                'id' => 'REQ-02',
                'courseId' => 'd-kckb',
                'courseTitle' => 'Kecerdasan Komputasional (4 D4 IT B)',
                'lecturerName' => 'Dosen Alpha, S.Kom., M.T.',
                'requesterRole' => 'Dosen',
                'requesterName' => 'Dosen Alpha, S.Kom., M.T.',
                'originalSchedule' => 'Kamis, 08:00 - 11:00 (Lab C 104)',
                'proposedSchedule' => 'Jumat, 13:00 - 16:00 (Lab C 105)',
                'reason' => 'Penugasan dewan riset vokasi nasional',
                'status' => 'Disetujui',
                'submittedAt' => '08 Okt 2026 14:15',
            ],
            [
                'id' => 'REQ-03',
                'courseId' => 'b-pmj-a',
                'courseTitle' => 'Pemrograman Jaringan Lanjut (2 D4 IT A)',
                'lecturerName' => 'Dosen Beta, S.Kom., M.T.',
                'requesterRole' => 'Dosen',
                'requesterName' => 'Dosen Beta, S.Kom., M.T.',
                'originalSchedule' => 'Senin, 13:00 - 16:00 (Lab C 105)',
                'proposedSchedule' => 'Selasa, 08:00 - 11:00 (Lab C 105)',
                'reason' => 'Pemeliharaan berkala server workstation lab',
                'status' => 'Disetujui',
                'submittedAt' => '08 Okt 2026 11:20',
            ],
            [
                'id' => 'REQ-04',
                'courseId' => 'b-pcd-a',
                'courseTitle' => 'Pengolahan Citra Digital (3 D4 IT B)',
                'lecturerName' => 'Dosen Epsilon, S.Kom., M.Kom.',
                'requesterRole' => 'Mahasiswa',
                'requesterName' => 'Pelajar Alpha B',
                'originalSchedule' => 'Kamis, 13:00 - 15:00 (D4-201)',
                'proposedSchedule' => 'Jumat, 08:00 - 10:00 (D4-201)',
                'reason' => 'Kunjungan supervisi industri mahasiswa magang',
                'status' => 'Menunggu Persetujuan Dosen',
                'submittedAt' => '07 Okt 2026 16:40',
            ],
            [
                'id' => 'REQ-05',
                'courseId' => 'b-met-a',
                'courseTitle' => 'Metodologi Penelitian Rekayasa (3 D4 IT A)',
                'lecturerName' => 'Dosen Zeta, Ph.D.',
                'requesterRole' => 'Dosen',
                'requesterName' => 'Dosen Zeta, Ph.D.',
                'originalSchedule' => 'Selasa, 08:00 - 10:00 (SAW-05.02)',
                'proposedSchedule' => 'Kamis, 15:00 - 17:00 (SAW-05.02)',
                'reason' => 'Rapat koordinasi senat akademik politeknik',
                'status' => 'Disetujui',
                'submittedAt' => '07 Okt 2026 09:10',
            ],
            [
                'id' => 'REQ-06',
                'courseId' => 'b-kw-a',
                'courseTitle' => 'Kewirausahaan Teknologi (3 D4 IT A)',
                'lecturerName' => 'Dosen Gamma, S.Kom., M.T.',
                'requesterRole' => 'Mahasiswa',
                'requesterName' => 'Mahasiswa Beta',
                'originalSchedule' => 'Selasa, 11:00 - 13:00 (SAW-06.10)',
                'proposedSchedule' => 'Rabu, 09:00 - 11:00 (SAW-06.10)',
                'reason' => 'Jadwal kuliah tamu inkubator bisnis',
                'status' => 'Ditolak',
                'submittedAt' => '06 Okt 2026 15:00',
            ],
        ];
    }

    private function defaultStudentRoster(): array
    {
        return [
            ['no' => 1, 'name' => 'Mahasiswa Alpha', 'nrp' => '3122000001', 'class' => '3 D4 IT A'],
            ['no' => 2, 'name' => 'Mahasiswa Beta', 'nrp' => '3122000002', 'class' => '3 D4 IT A'],
            ['no' => 3, 'name' => 'Mahasiswa Gamma', 'nrp' => '3122000003', 'class' => '3 D4 IT A'],
            ['no' => 4, 'name' => 'Mahasiswa Delta', 'nrp' => '3122000004', 'class' => '3 D4 IT A'],
            ['no' => 5, 'name' => 'Mahasiswa Epsilon', 'nrp' => '3122000005', 'class' => '3 D4 IT A'],
            ['no' => 6, 'name' => 'Mahasiswa Zeta', 'nrp' => '3122000006', 'class' => '3 D4 IT A'],
            ['no' => 7, 'name' => 'Mahasiswa Eta', 'nrp' => '3122000007', 'class' => '3 D4 IT A'],
            ['no' => 8, 'name' => 'Mahasiswa Theta', 'nrp' => '3122000008', 'class' => '3 D4 IT A'],
            ['no' => 9, 'name' => 'Mahasiswa Iota', 'nrp' => '3122000009', 'class' => '3 D4 IT A'],
            ['no' => 10, 'name' => 'Mahasiswa Kappa', 'nrp' => '3122000010', 'class' => '3 D4 IT A'],
            ['no' => 11, 'name' => 'Mahasiswa Lambda', 'nrp' => '3122000011', 'class' => '3 D4 IT A'],
            ['no' => 12, 'name' => 'Mahasiswa Mu', 'nrp' => '3122000012', 'class' => '3 D4 IT A'],
            ['no' => 13, 'name' => 'Mahasiswa Nu', 'nrp' => '3122000013', 'class' => '3 D4 IT A'],
            ['no' => 14, 'name' => 'Mahasiswa Xi', 'nrp' => '3122000014', 'class' => '3 D4 IT A'],
            ['no' => 15, 'name' => 'Mahasiswa Omicron', 'nrp' => '3122000015', 'class' => '3 D4 IT A'],
        ];
    }

    private function defaultSystemLogs(): array
    {
        return [
            [
                'id' => 'LOG-1001',
                'timestamp' => '10 Okt 2026 08:42',
                'level' => 'shift',
                'levelLabel' => 'Perubahan Jadwal',
                'actor' => 'Dosen Alpha, S.Kom., M.T.',
                'module' => 'Scheduler Engine',
                'detail' => 'Jadwal Workshop Mesin Pembelajaran dipindahkan ke Rabu, 13:00 - 16:00 (Lab C 103) secara otomatis.',
                'status' => 'Sukses',
            ],
            [
                'id' => 'LOG-1002',
                'timestamp' => '10 Okt 2026 08:30',
                'level' => 'info',
                'levelLabel' => 'Info',
                'actor' => 'Mahasiswa Alpha (3122000001)',
                'module' => 'Pengajuan Mahasiswa',
                'detail' => 'Pengajuan baru permohonan perpindahan jadwal untuk Workshop Mesin Pembelajaran diajukan.',
                'status' => 'Terkirim',
            ],
            [
                'id' => 'LOG-1003',
                'timestamp' => '10 Okt 2026 07:15',
                'level' => 'sync',
                'levelLabel' => 'Sinkronisasi CSV',
                'actor' => 'BAAK Admin (Biro Akademik)',
                'module' => 'Data Ingestion',
                'detail' => 'Sinkronisasi berkas jadwal_semester_ganjil_2026.csv berhasil memvalidasi 48 baris perkuliahan.',
                'status' => 'Selesai',
            ],
            [
                'id' => 'LOG-1004',
                'timestamp' => '10 Okt 2026 06:00',
                'level' => 'warning',
                'levelLabel' => 'Peringatan',
                'actor' => 'Petugas Sarpras',
                'module' => 'Manajemen Fasilitas',
                'detail' => 'Pemeliharaan berkala AC di Ruang Workshop Komputer C-102 dijadwalkan akhir pekan.',
                'status' => 'Perhatian',
            ],
            [
                'id' => 'LOG-1005',
                'timestamp' => '09 Okt 2026 11:20',
                'level' => 'error',
                'levelLabel' => 'Error',
                'actor' => 'Parser CSV',
                'module' => 'Validasi Kurikulum',
                'detail' => 'Ditemukan kode matakuliah tidak valid (XYZ999) pada baris ke-3 berkas CSV impor. Baris ditolak.',
                'status' => 'Dicegah',
            ],
        ];
    }
}
