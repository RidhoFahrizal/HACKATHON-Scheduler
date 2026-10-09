<?php

namespace Tests\Unit;

use App\Services\MockAcademicService;
use PHPUnit\Framework\TestCase;

class MockAcademicServiceTest extends TestCase
{
    private MockAcademicService $service;

    protected function setUp(): void
    {
        $this->service = new MockAcademicService;
    }

    public function test_payload_exposes_every_dataset_the_client_script_reads(): void
    {
        $this->assertSame([
            'appData',
            'masterRooms',
            'masterSubjects',
            'masterLecturers',
            'masterStudents',
            'rescheduleRequests',
            'studentRoster',
            'systemLogs',
            'recommendationSlots',
        ], array_keys($this->service->payload()));
    }

    public function test_profiles_cover_the_three_roles(): void
    {
        $this->assertSame(['mahasiswa', 'dosen', 'baak'], array_keys($this->service->profiles()));
    }

    public function test_system_logs_use_exactly_the_filterable_levels(): void
    {
        $levels = array_unique(array_column($this->service->systemLogs(), 'level'));
        sort($levels);

        $this->assertSame(['error', 'info', 'shift', 'sync', 'warning'], $levels);
    }

    public function test_rooms_belong_to_the_buildings_offered_by_the_crud_dropdown(): void
    {
        $buildings = array_unique(array_column($this->service->rooms(), 'building'));
        sort($buildings);

        $this->assertSame(['Gedung D3', 'Gedung D4', 'Gedung Pasca', 'Gedung SAW'], $buildings);
    }

    public function test_recommendation_slots_offer_five_complete_options(): void
    {
        $slots = $this->service->recommendationSlots();

        $this->assertCount(5, $slots);
        foreach ($slots as $slot) {
            $this->assertSame(['day', 'time', 'room', 'note'], array_keys($slot));
        }
    }
}
