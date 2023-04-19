<?php

namespace App\Jobs;

use App\Enums\ExportType;
use App\Models\Partner\Export;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class ProcessExport implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Export $export;
    private array $exportData;

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
        $partner = User::partner()
            ->select(
                'name',
                'role',
                'db_host',
                'db_port',
                'db_name',
                'db_user',
                'db_password',
            )
            ->where('id', $this->export->created_by)
            ->first();

        Config::set('database.connections.mysql_partner', [
            'driver' => 'mysql',
            'host' => $partner->db_host,
            'port' => $partner->db_port,
            'database' => $partner->db_name,
            'username' => $partner->db_user,
            'password' => Crypt::decryptString($partner->db_password),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
        ]);

        $this->exportData = ExportType::from($this->export->export_type)->get($this->export->filters);

        if (count($this->exportData) > 0) {
            $headers = array_keys($this->exportData[0]);
        } else {
            $headers = [];
            $statusMessage = "No records found";
        }

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

        // Save the CSV content to a file
        $filename = $partner->name.'_'.$partner->role.'_'.date('d/m/y H:i').'_export.csv';
        Storage::put($filename, $csv);

        if (!isset($statusMessage)) {
            $statusMessage = "CSV file has been stored as " . $filename;
        }

        // Update Export model
        $this->export->update([
            'csv_file_name' => $filename,
            'file_rows' => count($this->exportData),
            'file_size' => strlen($csv),
            'completed_at' => now(),
            'status' => 'completed',
            'message' => $statusMessage,
        ]);
    }
}
