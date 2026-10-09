<?php

namespace App\Http\Controllers;

use App\Services\MockAcademicService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AcademicSchedulerController extends Controller
{
    private const ROLES = ['mahasiswa', 'dosen', 'baak'];

    public function __construct(private readonly MockAcademicService $mockAcademic) {}

    public function index(Request $request): View
    {
        $role = $request->session()->get('role', 'mahasiswa');

        return view('scheduler.index', [
            'payload' => [
                ...$this->mockAcademic->payload(),
                'activeRole' => $role,
                'switchRoleUrl' => route('scheduler.switch-role'),
            ],
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
}
