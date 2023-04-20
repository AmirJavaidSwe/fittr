<?php

namespace App\Jobs;

use App\Enums\ExportStatus;
use App\Enums\ExportType;
use App\Models\Partner\Export;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class ProcessExport implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Export $export;
    private User $partner;
    private array $exportData;
    private bool $hasDataToExport = false;
    private string $statusMessage = '';
    private string $exportPath = 'exports/{export_type}/{role}/{file_name}';

    public function __construct(Export $export)
    {
        $this->export = $export;
    }

    public function uniqueId(): int
    {
        return $this->export->id;
    }

    public function middleware(): array
    {
        return [(new WithoutOverlapping($this->export->id))->expireAfter(300)];
    }

    public function handle(): void
    {
        $this->export->setStatusProcessing();
        $this->setPartnerConnection();

        $this->exportData = ExportType::from($this->export->export_type)->get($this->export->filters);

        $this->hasDataToExport = count($this->exportData) > 0;

        if (!$this->hasDataToExport) {
            $this->statusMessage = "No data to export";

            $this->export->update([
                'status' => ExportStatus::completed,
                'message' => $this->statusMessage,
            ]);
        } else {
            $this->exportToCSV();
        }
    }

    public function setPartnerConnection(): void
    {
        $this->partner = User::partner()->select(
            'name', 'role', 'db_host', 'db_port', 'db_name', 'db_user', 'db_password'
        )->where('id', $this->export->created_by)->first();

        Config::set('database.connections.mysql_partner', [
            'driver' => 'mysql',
            'host' => $this->partner->db_host,
            'port' => $this->partner->db_port,
            'database' => $this->partner->db_name,
            'username' => $this->partner->db_user,
            'password' => Crypt::decryptString($this->partner->db_password),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
        ]);
    }

    public function exportToCSV(): void
    {
        $headers = $this->hasDataToExport ? array_keys($this->exportData[0]) : [];

        $file = fopen('php://temp', 'rw');

        // Insert headers
        fputcsv($file, $headers);

        // Insert data
        foreach ($this->exportData as $record) {
            fputcsv($file, $record);
        }

        rewind($file);
        $csv = stream_get_contents($file);
        fclose($file);

        // Create file name
        $fileName = $this->partner->name.'_'.$this->export->export_type.'_'.date('d-m-y H:i').'_export.csv';

        // Replace spaces with underscores
        $filename = str_replace(' ', '_', $fileName);

        // Replace special characters with underscores
        $storagePath = str_replace(['{export_type}', '{role}', '{file_name}'], [
            $this->export->export_type, $this->partner->role, $filename
        ], $this->exportPath);

        // Store CSV file
        Storage::put($storagePath, $csv);

        if (!isset($this->statusMessage)) {
            $this->statusMessage = "CSV file has been stored as " . $filename;
        }

        // Update Export model
        $this->export->update([
            'csv_file_name' => $filename,
            'file_rows' => count($this->exportData),
            'file_size' => strlen($csv),
            'completed_at' => Carbon::now(),
            'status' => ExportStatus::completed->name,
            'message' => $this->statusMessage
        ]);
    }
}
