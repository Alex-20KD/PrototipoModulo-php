<?php

namespace Tests\Feature;

use App\Enums\StaffRole;
use App\Models\Staff;
use App\Modules\Triage\Models\Doctor;
use Database\Seeders\StaffSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class StaffAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_nurse_staff_without_doctor_id(): void
    {
        $nurse = Staff::create([
            'name' => 'Enfermera Test',
            'email' => 'nurse@test.local',
            'password' => 'secret123',
            'role' => StaffRole::Nurse,
            'doctor_id' => null,
        ]);

        $this->assertDatabaseHas('staff', [
            'id' => $nurse->id,
            'email' => 'nurse@test.local',
            'role' => 'nurse',
            'doctor_id' => null,
        ]);
        $this->assertTrue($nurse->isNurse());
        $this->assertFalse($nurse->isDoctor());
    }

    public function test_can_create_reception_staff_without_doctor_id(): void
    {
        $reception = Staff::create([
            'name' => 'Recepción Test',
            'email' => 'reception@test.local',
            'password' => 'secret123',
            'role' => StaffRole::Reception,
            'doctor_id' => null,
        ]);

        $this->assertDatabaseHas('staff', [
            'id' => $reception->id,
            'email' => 'reception@test.local',
            'role' => 'reception',
            'doctor_id' => null,
        ]);
        $this->assertTrue($reception->isReception());
        $this->assertFalse($reception->isDoctor());
    }

    public function test_can_create_doctor_staff_with_valid_doctor_id(): void
    {
        $doctor = Doctor::create([
            'nombres' => 'Dr. Fernando',
            'especialidad' => 'Pediatría',
        ]);

        $staff = Staff::create([
            'name' => 'Dr. Fernando',
            'email' => 'doctor@test.local',
            'password' => 'secret123',
            'role' => StaffRole::Doctor,
            'doctor_id' => $doctor->id,
        ]);

        $this->assertDatabaseHas('staff', [
            'id' => $staff->id,
            'email' => 'doctor@test.local',
            'role' => 'doctor',
            'doctor_id' => $doctor->id,
        ]);
        $this->assertTrue($staff->isDoctor());
        $this->assertEquals($doctor->id, $staff->doctor->id);
    }

    public function test_doctor_staff_requires_doctor_id(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Staff::create([
            'name' => 'Doctor Sin DoctorId',
            'email' => 'nodoctor@test.local',
            'password' => 'secret123',
            'role' => StaffRole::Doctor,
            'doctor_id' => null,
        ]);
    }

    public function test_staff_can_generate_personal_access_token(): void
    {
        $staff = Staff::create([
            'name' => 'Staff Token Test',
            'email' => 'token@test.local',
            'password' => 'secret123',
            'role' => StaffRole::Nurse,
            'doctor_id' => null,
        ]);

        $tokenResult = $staff->createToken('auth_token');

        $this->assertNotEmpty($tokenResult->plainTextToken);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => Staff::class,
            'tokenable_id' => $staff->id,
            'name' => 'auth_token',
        ]);
    }

    public function test_staff_seeder_creates_all_three_roles_correctly(): void
    {
        $this->seed(StaffSeeder::class);

        $this->assertDatabaseCount('staff', 3);
        $this->assertDatabaseHas('staff', [
            'email' => 'enfermera@medtriaje.test',
            'role' => 'nurse',
        ]);
        $this->assertDatabaseHas('staff', [
            'email' => 'recepcion@medtriaje.test',
            'role' => 'reception',
        ]);

        $doctorStaff = Staff::where('email', 'medico@medtriaje.test')->first();
        $this->assertNotNull($doctorStaff);
        $this->assertEquals(StaffRole::Doctor, $doctorStaff->role);
        $this->assertNotNull($doctorStaff->doctor_id);
    }
}
