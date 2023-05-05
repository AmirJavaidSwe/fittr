<?php

namespace App\Exports;

use App\Abstracts\Exporter;
use App\Enums\ExportStatus;
use App\Enums\ExportType;
use App\Models\Partner\ClassLesson;
use App\Models\Partner\Export;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;
use Str;

class ExportClassLesson extends Exporter
{
    protected array $fields = [
        'id',
        'start_date',
        'status',
        'end_date',
        'studio_id',
        'instructor_id',
        'created_at',
        'updated_at'
    ];

    public function __construct(Export $export)
    {
        parent::__construct($export, new ClassLesson());
    }

    public function __invoke(): array
    {
        $this->applyFilters();

        return $this->exportTo();
    }

    protected function applyFilters(): array
    {
        if (in_array('start_date', array_keys($this->filters)))
            $this->filters['start_date'] = Carbon::parse($this->filters['start_date'])->format('Y-m-d');

        if (in_array('end_date', array_keys($this->filters)))
            $this->filters['end_date'] = Carbon::parse($this->filters['end_date'])->format('Y-m-d');

        return $this->filters;
    }

    protected function relationShips() : array
    {
        return [
            'studio:id,title',
            'instructor:id,name,email,role'
        ];
    }

    public function query(): Builder
    {
        $query = $this->model->query();

        foreach ($this->filters as $key => $value) {
            $operator = ExportType::operator($key);
            $query->{$operator[0]}($key, $operator[1], $value);
        }

        $query->with($this->relationShips());

        return $query->select($this->fields);
    }

    protected function getExportData() : Collection
    {
        return $this->query()->chunkMap(function ($query){

            $query->start_date = $query->start_date->setTimeZone('Europe/London')->format('Y-m-d H:i:s A');
            $query->end_date = $query->end_date->setTimeZone('Europe/London')->format('Y-m-d H:i:s A');

            $query->studio = $query->id.':'.$query->studio->title;
            $query->instructor = $query?->instructor?->email;

            return $query;

        }, 500);
    }

    protected function exportToFile(): array
    {
        $headers = $this->fields;

        $file = fopen('php://temp', 'rw');

        // Insert headers
        fputcsv($file, $headers);

        // Insert data
        $exportData = $this->getExportData();

        foreach ($exportData as $record) {

            $record = $record->toArray();

            foreach ($record as $key => $value) {
                if (str_contains($key, '_date') || str_contains($key, '_at')) {
                    $record[$key] = Carbon::parse($value)->format('Y-m-d H:i:s A');
                }

                if ($key == 'studio') {
                    $record['studio'] = $value['id'].':'.$value['title'];
                }
            }

            fputcsv($file, $record);
        }

        rewind($file);
        $csv = stream_get_contents($file);
        fclose($file);

        // Create file name
        $fileName = $this->export->type.'_'.date('d-m-y_H:i').'_export.csv';

        // Replace spaces with underscores
        $filename = str_replace(' ', '_', $fileName);

        // Replace special characters with underscores
        $storagePath = str_replace(['{type}', '{role}', '{file_name}'], [
            $this->export->type, $this->export->created_by, $filename
        ], $this->exportPath);

        // Store CSV file
        Storage::put($storagePath, $csv);

        if (!isset($this->statusMessage)) {
            $this->statusMessage = "CSV file has been stored as " . $filename;
        }

        // Update Export model
        return [
            'file_path' => $storagePath,
            'file_name' => $filename,
            'file_rows' => count($exportData),
            'file_size' => strlen($csv),
            'completed_at' => Carbon::now(),
            'status' => ExportStatus::completed->name,
            'message' => $this->statusMessage
        ];
    }

    public function columnFormats(): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [];
    }

    public function headings(): array
    {
        return [
            'User ID',
            'Start Date',
            'Status',
            'End Date',
            'Studio',
            'Instructor',
            'Created At',
            'Updated At'
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->start_date,
            $row->status,
            $row->end_date,
            $row->studio->title,
            $row?->instructor?->email,
            $row->created_at,
            $row->updated_at
        ];
    }

    public function title(): string
    {
        return 'class-lessons';
    }

    private function exportTo(): array
    {
        $fileName = $this->export->type.'_'.date('y_m_d').'_'.Str::random(5).'.'.$this->export->file_type;

        $storagePath = str_replace(['{type}', '{role}', '{file_name}'], [
            $this->export->type, $this->export->created_by, $fileName
        ], $this->export->file_type);

        $disk = app()->environment('local') ? 'local' : 's3';

        $this->store($storagePath, $disk, $this->getWriterType());

        return [
            'file_path' => $storagePath,
            'file_name' => $fileName,
            'file_rows' => $this->getExportData()->count(),
            'file_size' => Storage::size($storagePath),
            'completed_at' => Carbon::now(),
            'status' => ExportStatus::completed->name,
            'message' => $this->statusMessage
        ];
    }
}
