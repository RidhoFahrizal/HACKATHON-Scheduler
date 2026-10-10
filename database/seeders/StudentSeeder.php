<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Fictional synthetic students (no real names or real NRPs)
        $greekNames = [
            'Alpha', 'Beta', 'Gamma', 'Delta', 'Epsilon',
            'Zeta', 'Eta', 'Theta', 'Iota', 'Kappa',
            'Lambda', 'Mu', 'Nu', 'Xi', 'Omicron',
            'Pi', 'Rho', 'Sigma', 'Tau', 'Upsilon',
        ];

        // 3 D4 IT A cohort (20 students)
        $students3A = [];
        foreach ($greekNames as $idx => $label) {
            $seq = sprintf('%04d', $idx + 1);
            $nrp = "312200{$seq}";
            $username = "Mahasiswa {$label}";
            $slug = strtolower($label);

            $students3A[] = [
                'username' => $username,
                'nrp' => $nrp,
                'class' => '3 D4 IT A',
                'cohort_year' => 2022,
                'department' => 'D4 Teknik Informatika',
                'email' => "mhs.{$slug}@student.pens.ac.id",
            ];
        }

        // 3 D4 IT B cohort (20 students)
        $students3B = [];
        foreach ($greekNames as $idx => $label) {
            $seq = sprintf('%04d', $idx + 101);
            $nrp = "312200{$seq}";
            $username = "Pelajar {$label} B";
            $slug = strtolower($label);

            $students3B[] = [
                'username' => $username,
                'nrp' => $nrp,
                'class' => '3 D4 IT B',
                'cohort_year' => 2022,
                'department' => 'D4 Teknik Informatika',
                'email' => "pelajar.{$slug}.b@student.pens.ac.id",
            ];
        }

        // 2 D4 IT A cohort (12 students)
        $students2A = [];
        for ($idx = 0; $idx < 12; $idx++) {
            $label = $greekNames[$idx];
            $seq = sprintf('%04d', $idx + 201);
            $nrp = "312300{$seq}";
            $username = "Kadet {$label}";
            $slug = strtolower($label);

            $students2A[] = [
                'username' => $username,
                'nrp' => $nrp,
                'class' => '2 D4 IT A',
                'cohort_year' => 2023,
                'department' => 'D4 Teknik Informatika',
                'email' => "kadet.{$slug}@student.pens.ac.id",
            ];
        }

        // 4 D4 IT A cohort (10 students)
        $students4A = [];
        for ($idx = 0; $idx < 10; $idx++) {
            $label = $greekNames[$idx];
            $seq = sprintf('%04d', $idx + 301);
            $nrp = "312100{$seq}";
            $username = "Senior {$label}";
            $slug = strtolower($label);

            $students4A[] = [
                'username' => $username,
                'nrp' => $nrp,
                'class' => '4 D4 IT A',
                'cohort_year' => 2021,
                'department' => 'D4 Teknik Informatika',
                'email' => "senior.{$slug}@student.pens.ac.id",
            ];
        }

        $allStudents = array_merge($students3A, $students3B, $students2A, $students4A);

        foreach ($allStudents as $student) {
            Student::updateOrCreate(
                ['nrp' => $student['nrp']],
                $student
            );
        }
    }
}
