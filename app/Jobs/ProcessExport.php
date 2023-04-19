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

class ProcessExport implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $export;

    /**
     * Create a new job instance.
     */
    public function __construct(Export $export)
    {
        $this->export = $export;
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): int
    {
        return $this->export->id;
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [(new WithoutOverlapping($this->export->id))->expireAfter(300)];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $export = ExportType::from($this->export->export_type)->get($this->export->filters);

        dd($export);

        // this is just a demo of export job
        // $model is ok for basic exports, however most exports will require extra processing for columns
        // We need to add abstraction and make this job a proxy calling new App/Exports/{$enum->ExportClass}

        // Idea is: create a class for each of the supported exports under App/Exports namespace
        // Make this job colling new export class, passing in it's constructor filter_params, produce data and store in the file, return results object back to the job (filename, size, ect)

        $partner = User::partner()
            ->select(
                'db_host',
                'db_port',
                'db_name',
                'db_user',
                'db_password',
            )
            ->where('id', $this->export->created_by)
            ->first();

        //Set connection to database: (run time)
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

        $results = $export->when($filters, function (Builder $query, array $filters) {
                foreach ($filters as $key => $value) {
                    $type = ExportType::operator($key);
                    $method = $type[0];
                    $operator = $type[1];
                    $cast = $type[2];

                    if($cast == 'datetime'){
                        $value = Carbon::parse($value);
                    }
                    $query->{$method}($key, $operator, $value);
                }
            })->get();

        //CREATE AND STORE FILE
        $this->export->update([
            'csv_file_name' => 'string 1024',
            'file_rows' => rand(10, 100),
            'file_size' => rand(100, 10000),
            'completed_at' => now(),
        ]);
    }
}
