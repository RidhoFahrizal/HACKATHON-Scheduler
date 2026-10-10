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
            [
                'day' => 1, 'startSlot' => 0, 'endSlot' => 2,
                'subjectId' => $subjects['Algoritma dan Pemrograman']->id,
                'lecturerId' => $subjects['Algoritma dan Pemrograman']->lecturerId,
                'roomId' => $rooms['GA-101']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 1, 'startSlot' => 3, 'endSlot' => 5,
                'subjectId' => $subjects['Struktur Data']->id,
                'lecturerId' => $subjects['Struktur Data']->lecturerId,
                'roomId' => $rooms['GB-101']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 1, 'startSlot' => 6, 'endSlot' => 8,
                'subjectId' => $subjects['Basis Data']->id,
                'lecturerId' => $subjects['Basis Data']->lecturerId,
                'roomId' => $rooms['GB-201']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 2, 'startSlot' => 0, 'endSlot' => 2,
                'subjectId' => $subjects['Pemrograman Web']->id,
                'lecturerId' => $subjects['Pemrograman Web']->lecturerId,
                'roomId' => $rooms['GB-202']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 2, 'startSlot' => 3, 'endSlot' => 5,
                'subjectId' => $subjects['Kecerdasan Buatan']->id,
                'lecturerId' => $subjects['Kecerdasan Buatan']->lecturerId,
                'roomId' => $rooms['GA-201']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 2, 'startSlot' => 6, 'endSlot' => 9,
                'subjectId' => $subjects['Machine Learning']->id,
                'lecturerId' => $subjects['Machine Learning']->lecturerId,
                'roomId' => $rooms['GB-201']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 3, 'startSlot' => 0, 'endSlot' => 2,
                'subjectId' => $subjects['Jaringan Komputer']->id,
                'lecturerId' => $subjects['Jaringan Komputer']->lecturerId,
                'roomId' => $rooms['GB-301']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 3, 'startSlot' => 3, 'endSlot' => 5,
                'subjectId' => $subjects['Sistem Operasi']->id,
                'lecturerId' => $subjects['Sistem Operasi']->lecturerId,
                'roomId' => $rooms['GA-102']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 3, 'startSlot' => 6, 'endSlot' => 8,
                'subjectId' => $subjects['Rekayasa Perangkat Lunak']->id,
                'lecturerId' => $subjects['Rekayasa Perangkat Lunak']->lecturerId,
                'roomId' => $rooms['GA-202']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 4, 'startSlot' => 0, 'endSlot' => 2,
                'subjectId' => $subjects['Interaksi Manusia dan Komputer']->id,
                'lecturerId' => $subjects['Interaksi Manusia dan Komputer']->lecturerId,
                'roomId' => $rooms['GD-201']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 4, 'startSlot' => 3, 'endSlot' => 5,
                'subjectId' => $subjects['Keamanan Informasi']->id,
                'lecturerId' => $subjects['Keamanan Informasi']->lecturerId,
                'roomId' => $rooms['GA-301']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 4, 'startSlot' => 6, 'endSlot' => 8,
                'subjectId' => $subjects['Kriptografi']->id,
                'lecturerId' => $subjects['Kriptografi']->lecturerId,
                'roomId' => $rooms['GB-102']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 5, 'startSlot' => 0, 'endSlot' => 2,
                'subjectId' => $subjects['Matematika Diskrit']->id,
                'lecturerId' => $subjects['Matematika Diskrit']->lecturerId,
                'roomId' => $rooms['GC-101']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 5, 'startSlot' => 3, 'endSlot' => 5,
                'subjectId' => $subjects['Aljabar Linear']->id,
                'lecturerId' => $subjects['Aljabar Linear']->lecturerId,
                'roomId' => $rooms['GC-102']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 5, 'startSlot' => 6, 'endSlot' => 8,
                'subjectId' => $subjects['Statistika dan Probabilitas']->id,
                'lecturerId' => $subjects['Statistika dan Probabilitas']->lecturerId,
                'roomId' => $rooms['GC-101']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 1, 'startSlot' => 9, 'endSlot' => 10,
                'subjectId' => $subjects['Pengantar Teknologi Informasi']->id,
                'lecturerId' => $subjects['Pengantar Teknologi Informasi']->lecturerId,
                'roomId' => $rooms['GD-101']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 2, 'startSlot' => 10, 'endSlot' => 11,
                'subjectId' => $subjects['Etika Profesi']->id,
                'lecturerId' => $subjects['Etika Profesi']->lecturerId,
                'roomId' => $rooms['GD-101']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 3, 'startSlot' => 9, 'endSlot' => 10,
                'subjectId' => $subjects['Bahasa Inggris Teknik']->id,
                'lecturerId' => $subjects['Bahasa Inggris Teknik']->lecturerId,
                'roomId' => $rooms['GA-101']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 4, 'startSlot' => 9, 'endSlot' => 10,
                'subjectId' => $subjects['Pancasila']->id,
                'lecturerId' => $subjects['Pancasila']->lecturerId,
                'roomId' => $rooms['GD-102']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 4, 'startSlot' => 11, 'endSlot' => 11,
                'subjectId' => $subjects['Olahraga']->id,
                'lecturerId' => $subjects['Olahraga']->lecturerId,
                'roomId' => $rooms['GD-102']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 1, 'startSlot' => 6, 'endSlot' => 9,
                'subjectId' => $subjects['Sistem Terdistribusi']->id,
                'lecturerId' => $subjects['Sistem Terdistribusi']->lecturerId,
                'roomId' => $rooms['GB-302']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 2, 'startSlot' => 6, 'endSlot' => 8,
                'subjectId' => $subjects['Cloud Computing']->id,
                'lecturerId' => $subjects['Cloud Computing']->lecturerId,
                'roomId' => $rooms['GB-302']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 3, 'startSlot' => 6, 'endSlot' => 9,
                'subjectId' => $subjects['Deep Learning']->id,
                'lecturerId' => $subjects['Deep Learning']->lecturerId,
                'roomId' => $rooms['GB-202']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 5, 'startSlot' => 0, 'endSlot' => 2,
                'subjectId' => $subjects['Pengolahan Citra Digital']->id,
                'lecturerId' => $subjects['Pengolahan Citra Digital']->lecturerId,
                'roomId' => $rooms['GB-201']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 5, 'startSlot' => 3, 'endSlot' => 5,
                'subjectId' => $subjects['Internet of Things']->id,
                'lecturerId' => $subjects['Internet of Things']->lecturerId,
                'roomId' => $rooms['GB-302']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 5, 'startSlot' => 6, 'endSlot' => 11,
                'subjectId' => $subjects['Proyek Akhir']->id,
                'lecturerId' => $subjects['Proyek Akhir']->lecturerId,
                'roomId' => $rooms['GA-401']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 1, 'startSlot' => 3, 'endSlot' => 5,
                'subjectId' => $subjects['Kerja Praktek']->id,
                'lecturerId' => $subjects['Kerja Praktek']->lecturerId,
                'roomId' => $rooms['GA-401']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 3, 'startSlot' => 9, 'endSlot' => 10,
                'subjectId' => $subjects['Pemrograman Mobile']->id,
                'lecturerId' => $subjects['Pemrograman Mobile']->lecturerId,
                'roomId' => $rooms['GB-202']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 1, 'startSlot' => 9, 'endSlot' => 10,
                'subjectId' => $subjects['Pendidikan Agama']->id,
                'lecturerId' => $subjects['Pendidikan Agama']->lecturerId,
                'roomId' => $rooms['GD-102']->id,
                'semesterType' => 'ganjil',
            ],
            [
                'day' => 2, 'startSlot' => 10, 'endSlot' => 12,
                'subjectId' => $subjects['Kalkulus']->id,
                'lecturerId' => $subjects['Kalkulus']->lecturerId,
                'roomId' => $rooms['GC-102']->id,
                'semesterType' => 'ganjil',
            ],
        ];

        foreach ($schedules as $schedule) {
            Schedule::updateOrCreate(
                [
                    'day' => $schedule['day'],
                    'startSlot' => $schedule['startSlot'],
                    'subjectId' => $schedule['subjectId'],
                ],
                $schedule
            );
        }

        $this->seedStudentSubjects($subjects);
    }

    private function seedStudentSubjects($subjects): void
    {
        $students = Student::all();

        $classSubjects = [
            'IF-4A-01' => ['Algoritma dan Pemrograman', 'Struktur Data', 'Basis Data', 'Kecerdasan Buatan', 'Machine Learning', 'Jaringan Komputer'],
            'IF-4A-02' => ['Algoritma dan Pemrograman', 'Struktur Data', 'Basis Data', 'Kecerdasan Buatan', 'Machine Learning', 'Jaringan Komputer'],
            'IF-4B-01' => ['Algoritma dan Pemrograman', 'Struktur Data', 'Basis Data', 'Sistem Operasi', 'Rekayasa Perangkat Lunak', 'Keamanan Informasi'],
            'IF-4B-02' => ['Algoritma dan Pemrograman', 'Struktur Data', 'Basis Data', 'Sistem Operasi', 'Rekayasa Perangkat Lunak', 'Keamanan Informasi'],
            'IF-3A-01' => ['Pemrograman Web', 'Pemrograman Mobile', 'Interaksi Manusia dan Komputer', 'Kriptografi', 'Matematika Diskrit', 'Etika Profesi'],
            'IF-3B-01' => ['Pemrograman Web', 'Pemrograman Mobile', 'Interaksi Manusia dan Komputer', 'Kriptografi', 'Matematika Diskrit', 'Etika Profesi'],
            'IF-2A-01' => ['Aljabar Linear', 'Statistika dan Probabilitas', 'Pengantar Teknologi Informasi', 'Bahasa Inggris Teknik', 'Pancasila', 'Olahraga'],
            'IF-2B-01' => ['Aljabar Linear', 'Statistika dan Probabilitas', 'Pengantar Teknologi Informasi', 'Bahasa Inggris Teknik', 'Pancasila', 'Olahraga'],
            'IF-1A-01' => ['Kalkulus', 'Pendidikan Agama', 'Pengantar Teknologi Informasi', 'Bahasa Inggris Teknik', 'Olahraga'],
            'IF-1B-01' => ['Kalkulus', 'Pendidikan Agama', 'Pengantar Teknologi Informasi', 'Bahasa Inggris Teknik', 'Olahraga'],
        ];

        foreach ($students as $student) {
            $subjectNames = $classSubjects[$student->class] ?? [];

            foreach ($subjectNames as $subjectName) {
                $subject = $subjects[$subjectName] ?? null;
                if (!$subject) continue;

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
