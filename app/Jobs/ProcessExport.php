<?php

namespace App\Jobs;

use App\Enums\ExportType;
use App\Models\Partner\Export;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;

class ProcessExport implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Export $export;

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

    public function setPartnerConnection(): void
    {
        $business = User::partner()
                        ->select('name', 'business_id')
                        ->where('id', $this->export->created_by)
                        ->first()->business;

        Config::set('database.connections.mysql_partner', [
            'driver' => 'mysql',
            'host' => $business->db_host,
            'port' => $business->db_port,
            'database' => $business->db_name,
            'username' => $business->db_user,
            'password' => Crypt::decryptString($business->db_password),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
        ]);
    }

    public function handle(): void
    {
        $this->export->setStatusProcessing();

        $this->setPartnerConnection();

        $response = ExportType::from($this->export->type)->get($this->export);

        $this->export->update($response);

        $this->export->setStatusCompleted();
    }
}
