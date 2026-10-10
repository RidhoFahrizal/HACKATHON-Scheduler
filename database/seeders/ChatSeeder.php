<?php

namespace Database\Seeders;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@univ.ac.id')->first();
        $kaprodi = User::where('email', 'kaprodi@univ.ac.id')->first();

        if (!$admin || !$kaprodi) return;

        ChatSession::query()->delete();

        $session1 = ChatSession::create([
            'user_id' => $admin->id,
            'title' => 'Konsultasi Jadwal Senin',
            'is_active' => true,
        ]);

        $session2 = ChatSession::create([
            'user_id' => $kaprodi->id,
            'title' => 'Permintaan Reschedule Mata Kuliah AI',
            'is_active' => true,
        ]);

        $session3 = ChatSession::create([
            'user_id' => $admin->id,
            'title' => 'Cek Ketersediaan Ruangan Lab',
            'is_active' => false,
        ]);

        $messages = [
            ['session_id' => $session1->id, 'sender_type' => 'user', 'message' => 'Selamat pagi, saya ingin mengecek jadwal hari Senin untuk kelas IF-4A-01.'],
            ['session_id' => $session1->id, 'sender_type' => 'assistant', 'message' => "Selamat pagi! Berikut jadwal hari Senin untuk IF-4A-01:\n1. 07:00-08:30 - Algoritma dan Pemrograman (GA-101)\n2. 08:30-10:00 - Struktur Data (GB-101)\n3. 10:00-11:30 - Basis Data (GB-201)\nApakah ada yang ingin ditanyakan?"],
            ['session_id' => $session1->id, 'sender_type' => 'user', 'message' => 'Apakah ruangan GA-101 tersedia untuk booking di hari Rabu jam 10:00?'],
            ['session_id' => $session1->id, 'sender_type' => 'assistant', 'message' => 'Ya, ruangan GA-101 tersedia di hari Rabu slot 10:00-11:30. Apakah Anda ingin saya buatkan booking?', 'suggested_slots_payload' => [
                ['day' => 3, 'startSlot' => 6, 'endSlot' => 8, 'room' => 'GA-101', 'score' => 85],
                ['day' => 3, 'startSlot' => 9, 'endSlot' => 11, 'room' => 'GA-201', 'score' => 72],
            ]],
            ['session_id' => $session1->id, 'sender_type' => 'user', 'message' => 'Ya, tolong buatkan booking untuk opsi pertama.'],
            ['session_id' => $session1->id, 'sender_type' => 'assistant', 'message' => "Booking berhasil dibuat! Detail:\n- Hari: Rabu\n- Waktu: 10:00 - 11:30\n- Ruangan: GA-101\n- Kapasitas: 40 orang\n\nTerima kasih!"],

            ['session_id' => $session2->id, 'sender_type' => 'user', 'message' => 'Saya ingin mengajukan reschedule untuk mata kuliah Kecerdasan Buatan pada hari Selasa karena dosen berhalangan hadir.'],
            ['session_id' => $session2->id, 'sender_type' => 'assistant', 'message' => "Baik, saya akan membantu proses reschedule. Berikut beberapa opsi slot yang tersedia:\n1. Kamis, 08:00-09:30, GA-201 (skor: 88)\n2. Kamis, 10:00-11:30, GA-102 (skor: 75)\n3. Jumat, 08:00-09:30, GA-301 (skor: 70)\n\nSilakan pilih opsi yang diinginkan.", 'suggested_slots_payload' => [
                ['day' => 4, 'startSlot' => 0, 'endSlot' => 2, 'room' => 'GA-201', 'score' => 88],
                ['day' => 4, 'startSlot' => 3, 'endSlot' => 5, 'room' => 'GA-102', 'score' => 75],
                ['day' => 5, 'startSlot' => 0, 'endSlot' => 2, 'room' => 'GA-301', 'score' => 70],
            ]],
            ['session_id' => $session2->id, 'sender_type' => 'user', 'message' => 'Saya pilih opsi 1, Kamis jam 08:00 di GA-201.'],
            ['session_id' => $session2->id, 'sender_type' => 'assistant', 'message' => 'Permintaan reschedule telah diajukan dengan kode RSQ-2026-001. Status: Menunggu persetujuan Kaprodi. Anda akan mendapat notifikasi setelah ada keputusan.'],

            ['session_id' => $session3->id, 'sender_type' => 'user', 'message' => 'Cek ketersediaan lab komputer untuk praktikum minggu depan.'],
            ['session_id' => $session3->id, 'sender_type' => 'assistant', 'message' => "Berikut ketersediaan lab komputer minggu depan:\n- GB-201 (Lab Komputer 1): Senin penuh, Selasa tersedia slot 3-5, Rabu tersedia penuh\n- GB-202 (Lab Komputer 2): Senin tersedia slot 6-8, Selasa penuh, Rabu tersedia slot 0-2\n- GB-301 (Lab Jaringan): Seluruh minggu tersedia\n\nMau booking yang mana?"],
        ];

        foreach ($messages as $msg) {
            ChatMessage::create($msg);
        }
    }
}
