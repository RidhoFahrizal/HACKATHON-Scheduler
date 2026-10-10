<?php

namespace App\Domain\Import;

use App\Domain\Scheduling\Enums\DayOfWeek;
use App\Domain\Scheduling\Support\ScheduleConflictChecker;
use App\Domain\Scheduling\Support\SlotMapper;
use App\Models\Building;
use App\Models\Lecturer;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CsvImporter
{
    public function __construct(
        private readonly SlotMapper $slotMapper,
        private readonly ScheduleConflictChecker $conflictChecker,
    ) {}

    public function import(string $entity, array|UploadedFile $input): array
    {
        $rows = is_array($input) ? $input : $this->parseFile($input);
        if (empty($rows)) {
            return [
                'entity' => $entity,
                'total' => 0,
                'imported' => 0,
                'failed' => 0,
                'errors' => [],
            ];
        }

        return match ($entity) {
            'rooms' => $this->importRooms($rows),
            'subjects' => $this->importSubjects($rows),
            'lecturers' => $this->importLecturers($rows),
            'students' => $this->importStudents($rows),
            'schedules' => $this->importSchedules($rows),
            default => throw new InvalidArgumentException("Entity {$entity} tidak didukung."),
        };
    }

    private function parseFile(UploadedFile $file): array
    {
        $content = file_get_contents($file->getRealPath());
        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        if (empty($lines)) return [];

        $rawHeader = str_getcsv(array_shift($lines));
        $headers = array_map(fn ($h) => strtolower(trim($h)), $rawHeader);

        $records = [];
        foreach ($lines as $line) {
            if (trim($line) === '') continue;
            $row = str_getcsv($line);
            $record = [];
            foreach ($headers as $idx => $header) {
                $record[$header] = trim($row[$idx] ?? '');
            }
            $records[] = $record;
        }

        return $records;
    }

    private function importRooms(array $rows): array
    {
        $total = count($rows);
        $imported = 0;
        $failed = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $rowNum = $index + 1;
            $code = trim($row['code'] ?? '');
            $name = trim($row['name'] ?? '');
            $bCode = trim($row['building_code'] ?? 'D4');
            $capacity = (int) ($row['capacity'] ?? 30);

            if (! $code || ! $name) {
                $failed++;
                $errors[] = ['row' => $rowNum, 'field' => 'code/name', 'message' => 'Kode dan nama ruangan wajib diisi.'];
                continue;
            }

            $building = Building::firstOrCreate(
                ['code' => strtoupper($bCode)],
                ['name' => "Gedung {$bCode}", 'floors_count' => 4]
            );

            Room::updateOrCreate(
                ['code' => $code],
                [
                    'building_id' => $building->id,
                    'name' => $name,
                    'capacity' => $capacity ?: 30,
                    'type' => trim($row['type'] ?? 'teori') ?: 'teori',
                    'floor' => (int) ($row['floor'] ?? 1) ?: 1,
                    'is_active' => true,
                ]
            );
            $imported++;
        }

        return compact('total', 'imported', 'failed', 'errors') + ['entity' => 'rooms'];
    }

    private function importSubjects(array $rows): array
    {
        $total = count($rows);
        $imported = 0;
        $failed = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $rowNum = $index + 1;
            $code = trim($row['code'] ?? '');
            $name = trim($row['name'] ?? '');
            $sks = (int) ($row['sks'] ?? $row['credits'] ?? 0);

            if (! $code || ! $name || $sks < 1) {
                $failed++;
                $errors[] = ['row' => $rowNum, 'field' => 'code/sks', 'message' => 'Kode, nama, dan SKS minimal 1 wajib diisi.'];
                continue;
            }

            Subject::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'credits' => $sks,
                    'semester' => ! empty($row['semester']) ? (int) $row['semester'] : null,
                    'department' => trim($row['department'] ?? ''),
                ]
            );
            $imported++;
        }

        return compact('total', 'imported', 'failed', 'errors') + ['entity' => 'subjects'];
    }

    private function importLecturers(array $rows): array
    {
        $total = count($rows);
        $imported = 0;
        $failed = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $rowNum = $index + 1;
            $nip = trim($row['nip'] ?? '');
            $name = trim($row['name'] ?? $row['username'] ?? '');

            if (! $nip || ! $name) {
                $failed++;
                $errors[] = ['row' => $rowNum, 'field' => 'nip/name', 'message' => 'NIP dan nama dosen wajib diisi.'];
                continue;
            }

            $email = trim($row['email'] ?? '');
            if (! $email) {
                $email = "{$nip}@pens.ac.id";
            }

            Lecturer::updateOrCreate(
                ['nip' => $nip],
                [
                    'username' => $name,
                    'email' => $email,
                    'code' => trim($row['code'] ?? '') ?: null,
                    'academic_title' => trim($row['academic_title'] ?? '') ?: null,
                    'department' => trim($row['department'] ?? ''),
                ]
            );
            $imported++;
        }

        return compact('total', 'imported', 'failed', 'errors') + ['entity' => 'lecturers'];
    }

    private function importStudents(array $rows): array
    {
        $total = count($rows);
        $imported = 0;
        $failed = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $rowNum = $index + 1;
            $nrp = trim($row['nrp'] ?? '');
            $name = trim($row['name'] ?? $row['username'] ?? '');
            $class = trim($row['class'] ?? '');

            if (! $nrp || ! $name || ! $class) {
                $failed++;
                $errors[] = ['row' => $rowNum, 'field' => 'nrp/class', 'message' => 'NRP, nama, dan kelas mahasiswa wajib diisi.'];
                continue;
            }

            Student::updateOrCreate(
                ['nrp' => $nrp],
                [
                    'username' => $name,
                    'class' => $class,
                    'cohort_year' => ! empty($row['cohort_year']) ? (int) $row['cohort_year'] : 2023,
                    'department' => trim($row['department'] ?? ''),
                    'email' => trim($row['email'] ?? '') ?: null,
                ]
            );
            $imported++;
        }

        return compact('total', 'imported', 'failed', 'errors') + ['entity' => 'students'];
    }

    private function importSchedules(array $rows): array
    {
        $total = count($rows);
        $imported = 0;
        $failed = 0;
        $errors = [];

        $dayMap = [
            'senin' => DayOfWeek::SENIN->value,
            'selasa' => DayOfWeek::SELASA->value,
            'rabu' => DayOfWeek::RABU->value,
            'kamis' => DayOfWeek::KAMIS->value,
            'jumat' => DayOfWeek::JUMAT->value,
            'sabtu' => DayOfWeek::SABTU->value,
            'minggu' => DayOfWeek::MINGGU->value,
        ];

        foreach ($rows as $index => $row) {
            $rowNum = $index + 1;
            $subCode = trim($row['kode_mk'] ?? '');
            $subName = trim($row['nama_mk'] ?? '');
            $lectName = trim($row['dosen_pengampu'] ?? '');
            $dayStr = strtolower(trim($row['hari'] ?? ''));
            $timeStr = trim($row['jam'] ?? '');
            $roomName = trim($row['ruang'] ?? '');

            $subject = Subject::where('code', $subCode)->orWhere('name', $subName)->first();
            if (! $subject) {
                $failed++;
                $errors[] = ['row' => $rowNum, 'field' => 'kode_mk', 'message' => "Kelas/Subjek '{$subName}' tidak ditemukan di kurikulum."];
                continue;
            }

            $lecturer = Lecturer::where('username', $lectName)->orWhere('code', $lectName)->first();
            if (! $lecturer) {
                $failed++;
                $errors[] = ['row' => $rowNum, 'field' => 'dosen_pengampu', 'message' => "Dosen '{$lectName}' tidak terdaftar di direktori dosen."];
                continue;
            }

            $room = Room::where('name', $roomName)->orWhere('code', $roomName)->first();
            if (! $room) {
                $failed++;
                $errors[] = ['row' => $rowNum, 'field' => 'ruang', 'message' => "Ruangan '{$roomName}' tidak terdaftar di sistem master ruang."];
                continue;
            }

            if (! isset($dayMap[$dayStr])) {
                $failed++;
                $errors[] = ['row' => $rowNum, 'field' => 'hari', 'message' => "Hari '{$dayStr}' tidak valid."];
                continue;
            }
            $dayInt = $dayMap[$dayStr];

            $timeParts = explode('-', $timeStr);
            if (count($timeParts) !== 2) {
                $failed++;
                $errors[] = ['row' => $rowNum, 'field' => 'jam', 'message' => "Format jam '{$timeStr}' harus 'HH:MM - HH:MM'."];
                continue;
            }
            $slotRange = $this->slotMapper->rangeFor(trim($timeParts[0]), trim($timeParts[1]));
            if (! $slotRange) {
                $failed++;
                $errors[] = ['row' => $rowNum, 'field' => 'jam', 'message' => "Waktu '{$timeStr}' tidak selaras dengan kisi slot perkuliahan."];
                continue;
            }
            [$startSlot, $endSlot] = $slotRange;

            $conflicts = $this->conflictChecker->conflicts(
                day: $dayInt,
                startSlot: $startSlot,
                endSlot: $endSlot,
                roomId: $room->id,
                lecturerId: $lecturer->id,
                semesterType: 'ganjil',
                lock: false,
            );
            if (! empty($conflicts)) {
                $failed++;
                $errors[] = ['row' => $rowNum, 'field' => 'jadwal', 'message' => implode(' ', $conflicts)];
                continue;
            }

            Schedule::updateOrCreate(
                [
                    'subjectId' => $subject->id,
                    'lecturerId' => $lecturer->id,
                    'day' => $dayInt,
                    'startSlot' => $startSlot,
                ],
                [
                    'endSlot' => $endSlot,
                    'roomId' => $room->id,
                    'semesterType' => 'ganjil',
                ]
            );
            $imported++;
        }

        return compact('total', 'imported', 'failed', 'errors') + ['entity' => 'schedules'];
    }
}
