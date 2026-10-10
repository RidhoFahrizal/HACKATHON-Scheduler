<?php

namespace Database\Seeders;

use App\Models\Krs;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\StudentSubject;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = Subject::all()->keyBy('name');
        $rooms = Room::all()->keyBy('code');

        $schedules = [
            // SENIN (day: 0)
            [
                'day' => 0, 'startSlot' => 1, 'endSlot' => 3,
                'subjectId' => $subjects['Workshop Mesin Pembelajaran']->id,
                'lecturerId' => $subjects['Workshop Mesin Pembelajaran']->lecturerId,
                'roomId' => $rooms['C-102']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 0, 'startSlot' => 7, 'endSlot' => 9,
                'subjectId' => $subjects['Pemrograman Jaringan Lanjut']->id,
                'lecturerId' => $subjects['Pemrograman Jaringan Lanjut']->lecturerId,
                'roomId' => $rooms['C-105']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 0, 'startSlot' => 1, 'endSlot' => 3,
                'subjectId' => $subjects['Algoritma dan Pemrograman']->id,
                'lecturerId' => $subjects['Algoritma dan Pemrograman']->lecturerId,
                'roomId' => $rooms['GA-101']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 0, 'startSlot' => 7, 'endSlot' => 9,
                'subjectId' => $subjects['Basis Data']->id,
                'lecturerId' => $subjects['Basis Data']->lecturerId,
                'roomId' => $rooms['GB-201']->id,
                'semesterType' => 'ganjil',
            ],

            // SELASA (day: 1)
            [
                'day' => 1, 'startSlot' => 1, 'endSlot' => 3,
                'subjectId' => $subjects['Workshop Mesin Pembelajaran']->id,
                'lecturerId' => $subjects['Workshop Mesin Pembelajaran']->lecturerId,
                'roomId' => $rooms['C-102']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 1, 'startSlot' => 1, 'endSlot' => 2,
                'subjectId' => $subjects['Metodologi Penelitian Rekayasa']->id,
                'lecturerId' => $subjects['Metodologi Penelitian Rekayasa']->lecturerId,
                'roomId' => $rooms['SAW-05.02']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 1, 'startSlot' => 4, 'endSlot' => 5,
                'subjectId' => $subjects['Kewirausahaan Teknologi']->id,
                'lecturerId' => $subjects['Kewirausahaan Teknologi']->lecturerId,
                'roomId' => $rooms['SAW-06.10']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 1, 'startSlot' => 7, 'endSlot' => 8,
                'subjectId' => $subjects['Pengolahan Bahasa Alami']->id,
                'lecturerId' => $subjects['Pengolahan Bahasa Alami']->lecturerId,
                'roomId' => $rooms['SAW-06.10']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 1, 'startSlot' => 1, 'endSlot' => 3,
                'subjectId' => $subjects['Struktur Data']->id,
                'lecturerId' => $subjects['Struktur Data']->lecturerId,
                'roomId' => $rooms['GB-101']->id,
                'semesterType' => 'ganjil',
            ],

            // RABU (day: 2)
            [
                'day' => 2, 'startSlot' => 1, 'endSlot' => 3,
                'subjectId' => $subjects['Kecerdasan Komputasional']->id,
                'lecturerId' => $subjects['Kecerdasan Komputasional']->lecturerId,
                'roomId' => $rooms['C-104']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 2, 'startSlot' => 9, 'endSlot' => 10,
                'subjectId' => $subjects['Keamanan, Keselamatan & K3L']->id,
                'lecturerId' => $subjects['Keamanan, Keselamatan & K3L']->lecturerId,
                'roomId' => $rooms['SAW-06.10']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 2, 'startSlot' => 1, 'endSlot' => 3,
                'subjectId' => $subjects['Pemrograman Web']->id,
                'lecturerId' => $subjects['Pemrograman Web']->lecturerId,
                'roomId' => $rooms['GB-202']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 2, 'startSlot' => 7, 'endSlot' => 9,
                'subjectId' => $subjects['Machine Learning']->id,
                'lecturerId' => $subjects['Machine Learning']->lecturerId,
                'roomId' => $rooms['GB-201']->id,
                'semesterType' => 'ganjil',
            ],

            // KAMIS (day: 3)
            [
                'day' => 3, 'startSlot' => 1, 'endSlot' => 3,
                'subjectId' => $subjects['Kecerdasan Komputasional']->id,
                'lecturerId' => $subjects['Kecerdasan Komputasional']->lecturerId,
                'roomId' => $rooms['C-104']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 3, 'startSlot' => 2, 'endSlot' => 4,
                'subjectId' => $subjects['Proyek Akhir Tahap 1']->id,
                'lecturerId' => $subjects['Proyek Akhir Tahap 1']->lecturerId,
                'roomId' => $rooms['SAW-08']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 3, 'startSlot' => 7, 'endSlot' => 8,
                'subjectId' => $subjects['Pemodelan & Simulasi Sistem']->id,
                'lecturerId' => $subjects['Pemodelan & Simulasi Sistem']->lecturerId,
                'roomId' => $rooms['SAW-05.02']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 3, 'startSlot' => 7, 'endSlot' => 8,
                'subjectId' => $subjects['Pengolahan Citra Digital']->id,
                'lecturerId' => $subjects['Pengolahan Citra Digital']->lecturerId,
                'roomId' => $rooms['D4-201']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 3, 'startSlot' => 1, 'endSlot' => 3,
                'subjectId' => $subjects['Sistem Operasi']->id,
                'lecturerId' => $subjects['Sistem Operasi']->lecturerId,
                'roomId' => $rooms['GA-102']->id,
                'semesterType' => 'ganjil',
            ],

            // JUMAT (day: 4)
            [
                'day' => 4, 'startSlot' => 1, 'endSlot' => 2,
                'subjectId' => $subjects['Kerja Praktek Industri']->id,
                'lecturerId' => $subjects['Kerja Praktek Industri']->lecturerId,
                'roomId' => $rooms['B-204']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 4, 'startSlot' => 1, 'endSlot' => 3,
                'subjectId' => $subjects['Pembelajaran Mendalam']->id,
                'lecturerId' => $subjects['Pembelajaran Mendalam']->lecturerId,
                'roomId' => $rooms['Lab Riset Lt 3']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 4, 'startSlot' => 3, 'endSlot' => 4,
                'subjectId' => $subjects['Bahasa Inggris Komunikasi Profesi']->id,
                'lecturerId' => $subjects['Bahasa Inggris Komunikasi Profesi']->lecturerId,
                'roomId' => $rooms['B-101']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 4, 'startSlot' => 1, 'endSlot' => 3,
                'subjectId' => $subjects['Jaringan Komputer']->id,
                'lecturerId' => $subjects['Jaringan Komputer']->lecturerId,
                'roomId' => $rooms['GB-301']->id,
                'semesterType' => 'ganjil',
            ],
        ];

        foreach ($schedules as $schedule) {
            Schedule::create($schedule);
        }

        $this->seedStudentSubjects($subjects);
    }

    private function seedStudentSubjects($subjects): void
    {
        $students = Student::all();

        $classSubjects = [
            '3 D4 IT A' => [
                'Workshop Mesin Pembelajaran',
                'Pemrograman Jaringan Lanjut',
                'Metodologi Penelitian Rekayasa',
                'Kewirausahaan Teknologi',
                'Pengolahan Bahasa Alami',
                'Kerja Praktek Industri',
            ],
            '3 D4 IT B' => [
                'Workshop Mesin Pembelajaran',
                'Pengolahan Citra Digital',
                'Bahasa Inggris Komunikasi Profesi',
            ],
            '2 D4 IT A' => [
                'Pemodelan & Simulasi Sistem',
                'Basis Data',
                'Algoritma dan Pemrograman',
            ],
            '4 D4 IT A' => [
                'Kecerdasan Komputasional',
                'Proyek Akhir Tahap 1',
                'Machine Learning',
            ],
            '4 D4 IT B' => [
                'Kecerdasan Komputasional',
                'Proyek Akhir Tahap 1',
            ],
            '1 D4 IT A' => [
                'Keamanan, Keselamatan & K3L',
                'Algoritma dan Pemrograman',
            ],
        ];

        foreach ($students as $student) {
            $subjectNames = $classSubjects[$student->class] ?? ['Algoritma dan Pemrograman', 'Basis Data'];

            foreach ($subjectNames as $subjectName) {
                $subject = $subjects[$subjectName] ?? null;
                if (! $subject) {
                    continue;
                }

                $ss = StudentSubject::updateOrCreate(
                    ['studentId' => $student->id, 'subjectId' => $subject->id],
                    ['studentId' => $student->id, 'subjectId' => $subject->id]
                );

                Krs::updateOrCreate(
                    ['studentSubjectId' => $ss->id],
                    ['studentSubjectId' => $ss->id]
                );
            }
        }
    }
}
