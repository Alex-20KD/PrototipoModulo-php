<?php

namespace App\Services;

use App\Exceptions\StorageUnavailableException;
use App\Modules\Triage\Models\Appointment;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;
use Throwable;

class ClinicalReportStorage
{
    protected string $disk = 'pc5';

    /**
     * Generate the path for the appointment's clinical report.
     * Path format: reports/YYYY/MM/appointment-{id}.pdf
     */
    public function path(Appointment $appointment): string
    {
        $date = $appointment->appointment_date ?? $appointment->created_at ?? now();

        return sprintf(
            'reports/%s/%s/appointment-%s.pdf',
            $date->format('Y'),
            $date->format('m'),
            $appointment->id
        );
    }

    /**
     * Save the PDF content to the SFTP disk.
     *
     * @throws StorageUnavailableException
     */
    public function put(string $path, string $content): bool
    {
        try {
            return Storage::disk($this->disk)->put($path, $content);
        } catch (UnableToWriteFile $e) {
            throw new StorageUnavailableException('Could not write to SFTP storage: '.$e->getMessage(), 0, $e);
        } catch (Throwable $e) {
            throw new StorageUnavailableException('SFTP storage is unavailable: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Check if the PDF file exists on the SFTP disk.
     *
     * @throws StorageUnavailableException
     */
    public function exists(string $path): bool
    {
        try {
            return Storage::disk($this->disk)->exists($path);
        } catch (UnableToCheckExistence $e) {
            throw new StorageUnavailableException('Could not check file existence on SFTP storage: '.$e->getMessage(), 0, $e);
        } catch (Throwable $e) {
            throw new StorageUnavailableException('SFTP storage is unavailable: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Read the file stream from the SFTP disk.
     *
     * @return resource|null
     *
     * @throws StorageUnavailableException
     */
    public function readStream(string $path)
    {
        try {
            return Storage::disk($this->disk)->readStream($path);
        } catch (UnableToReadFile $e) {
            throw new StorageUnavailableException('Could not read from SFTP storage: '.$e->getMessage(), 0, $e);
        } catch (Throwable $e) {
            throw new StorageUnavailableException('SFTP storage is unavailable: '.$e->getMessage(), 0, $e);
        }
    }
}
