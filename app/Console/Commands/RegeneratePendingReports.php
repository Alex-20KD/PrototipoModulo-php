<?php

namespace App\Console\Commands;

use App\Exceptions\StorageUnavailableException;
use App\Modules\Triage\Models\Appointment;
use App\Services\ClinicalReportStorage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

#[Signature('reports:regenerate-pending')]
#[Description('Regenerate and upload PDF reports for completed appointments that lack a report_path')]
class RegeneratePendingReports extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(ClinicalReportStorage $storage)
    {
        $appointments = Appointment::with(['user', 'doctor', 'vitalSigns', 'prescriptions', 'diagnoses'])
            ->where('status', 'completed')
            ->whereNull('report_path')
            ->get();

        if ($appointments->isEmpty()) {
            $this->info('No pending reports to regenerate.');

            return 0;
        }

        $this->withProgressBar($appointments, function ($appointment) use ($storage) {
            try {
                $pdfContent = Pdf::loadView('triage.pdf.formulario002', compact('appointment'))->output();
                $path = $storage->path($appointment);

                $storage->put($path, $pdfContent);
                $appointment->update(['report_path' => $path]);
            } catch (StorageUnavailableException $e) {
                Log::error("RegeneratePendingReports: SFTP upload failed for appointment {$appointment->id}: ".$e->getMessage());
            } catch (\Throwable $e) {
                Log::error("RegeneratePendingReports: PDF generation or SFTP upload failed for appointment {$appointment->id}: ".$e->getMessage());
            }
        });

        $this->newLine();
        $this->info('Process completed.');

        return 0;
    }
}
