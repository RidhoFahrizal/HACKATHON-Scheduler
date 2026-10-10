<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            ['username' => '220010001', 'class' => 'IF-4A-01'],
            ['username' => '220010002', 'class' => 'IF-4A-01'],
            ['username' => '220010003', 'class' => 'IF-4A-01'],
            ['username' => '220010004', 'class' => 'IF-4A-01'],
            ['username' => '220010005', 'class' => 'IF-4A-01'],
            ['username' => '220010006', 'class' => 'IF-4A-01'],
            ['username' => '220010007', 'class' => 'IF-4A-01'],
            ['username' => '220010008', 'class' => 'IF-4A-01'],
            ['username' => '220010009', 'class' => 'IF-4A-01'],
            ['username' => '220010010', 'class' => 'IF-4A-01'],
            ['username' => '220010011', 'class' => 'IF-4A-02'],
            ['username' => '220010012', 'class' => 'IF-4A-02'],
            ['username' => '220010013', 'class' => 'IF-4A-02'],
            ['username' => '220010014', 'class' => 'IF-4A-02'],
            ['username' => '220010015', 'class' => 'IF-4A-02'],
            ['username' => '220010016', 'class' => 'IF-4A-02'],
            ['username' => '220010017', 'class' => 'IF-4A-02'],
            ['username' => '220010018', 'class' => 'IF-4A-02'],
            ['username' => '220010019', 'class' => 'IF-4B-01'],
            ['username' => '220010020', 'class' => 'IF-4B-01'],
            ['username' => '220010021', 'class' => 'IF-4B-01'],
            ['username' => '220010022', 'class' => 'IF-4B-01'],
            ['username' => '220010023', 'class' => 'IF-4B-01'],
            ['username' => '220010024', 'class' => 'IF-4B-01'],
            ['username' => '220010025', 'class' => 'IF-4B-01'],
            ['username' => '220010026', 'class' => 'IF-4B-02'],
            ['username' => '220010027', 'class' => 'IF-4B-02'],
            ['username' => '220010028', 'class' => 'IF-4B-02'],
            ['username' => '220010029', 'class' => 'IF-4B-02'],
            ['username' => '220010030', 'class' => 'IF-4B-02'],
            ['username' => '230010001', 'class' => 'IF-3A-01'],
            ['username' => '230010002', 'class' => 'IF-3A-01'],
            ['username' => '230010003', 'class' => 'IF-3A-01'],
            ['username' => '230010004', 'class' => 'IF-3A-01'],
            ['username' => '230010005', 'class' => 'IF-3A-01'],
            ['username' => '230010006', 'class' => 'IF-3A-01'],
            ['username' => '230010007', 'class' => 'IF-3A-01'],
            ['username' => '230010008', 'class' => 'IF-3A-01'],
            ['username' => '230010009', 'class' => 'IF-3B-01'],
            ['username' => '230010010', 'class' => 'IF-3B-01'],
            ['username' => '230010011', 'class' => 'IF-3B-01'],
            ['username' => '230010012', 'class' => 'IF-3B-01'],
            ['username' => '230010013', 'class' => 'IF-3B-01'],
            ['username' => '230010014', 'class' => 'IF-3B-01'],
            ['username' => '240010001', 'class' => 'IF-2A-01'],
            ['username' => '240010002', 'class' => 'IF-2A-01'],
            ['username' => '240010003', 'class' => 'IF-2A-01'],
            ['username' => '240010004', 'class' => 'IF-2A-01'],
            ['username' => '240010005', 'class' => 'IF-2A-01'],
            ['username' => '240010006', 'class' => 'IF-2A-01'],
            ['username' => '240010007', 'class' => 'IF-2B-01'],
            ['username' => '240010008', 'class' => 'IF-2B-01'],
            ['username' => '240010009', 'class' => 'IF-2B-01'],
            ['username' => '240010010', 'class' => 'IF-2B-01'],
            ['username' => '240010011', 'class' => 'IF-2B-01'],
            ['username' => '240010012', 'class' => 'IF-2B-01'],
            ['username' => '250010001', 'class' => 'IF-1A-01'],
            ['username' => '250010002', 'class' => 'IF-1A-01'],
            ['username' => '250010003', 'class' => 'IF-1A-01'],
            ['username' => '250010004', 'class' => 'IF-1A-01'],
            ['username' => '250010005', 'class' => 'IF-1A-01'],
            ['username' => '250010006', 'class' => 'IF-1B-01'],
            ['username' => '250010007', 'class' => 'IF-1B-01'],
            ['username' => '250010008', 'class' => 'IF-1B-01'],
            ['username' => '250010009', 'class' => 'IF-1B-01'],
            ['username' => '250010010', 'class' => 'IF-1B-01'],
        ];

        foreach ($students as $student) {
            Student::updateOrCreate(
                ['username' => $student['username']],
                $student
            );
        }
    }
}
