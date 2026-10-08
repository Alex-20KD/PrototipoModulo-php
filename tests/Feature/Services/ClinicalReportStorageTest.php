<?php

namespace Tests\Feature\Services;

use App\Exceptions\StorageUnavailableException;
use App\Modules\Triage\Models\Appointment;
use App\Services\ClinicalReportStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToWriteFile;
use Tests\TestCase;

class ClinicalReportStorageTest extends TestCase
{
    use RefreshDatabase;

    protected ClinicalReportStorage $storageService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storageService = new ClinicalReportStorage;
        Storage::fake('pc5');
    }

    public function test_it_generates_correct_path_for_appointment()
    {
        // Avoid touching other models, just create a basic appointment
        $appointment = new Appointment([
            'id' => 1234,
            'appointment_date' => '2025-05-15 10:00:00',
        ]);
        // Since we are not saving it, we can force the ID for path checking
        $appointment->id = 1234;

        $path = $this->storageService->path($appointment);

        $this->assertEquals('reports/2025/05/appointment-1234.pdf', $path);
    }

    public function test_it_can_put_file_to_sftp()
    {
        $path = 'reports/2025/05/appointment-1.pdf';
        $content = 'dummy pdf content';

        $result = $this->storageService->put($path, $content);

        $this->assertTrue($result);
        Storage::disk('pc5')->assertExists($path);
        $this->assertEquals($content, Storage::disk('pc5')->get($path));
    }

    public function test_it_can_check_if_file_exists()
    {
        $path = 'reports/2025/05/appointment-2.pdf';

        $this->assertFalse($this->storageService->exists($path));

        Storage::disk('pc5')->put($path, 'content');

        $this->assertTrue($this->storageService->exists($path));
    }

    public function test_it_can_read_stream()
    {
        $path = 'reports/2025/05/appointment-3.pdf';
        Storage::disk('pc5')->put($path, 'stream content');

        $stream = $this->storageService->readStream($path);

        $this->assertIsResource($stream);
        $this->assertEquals('stream content', stream_get_contents($stream));
    }

    public function test_put_throws_storage_unavailable_exception_on_failure()
    {
        Storage::shouldReceive('disk')
            ->with('pc5')
            ->andThrow(new UnableToWriteFile('Simulated failure'));

        $this->expectException(StorageUnavailableException::class);
        $this->expectExceptionMessage('Could not write to SFTP storage: Simulated failure');

        $this->storageService->put('path.pdf', 'content');
    }
}
