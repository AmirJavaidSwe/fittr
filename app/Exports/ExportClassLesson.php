<?php

namespace App\Exports;

use App\Abstracts\Export;
use App\Enums\ExportStatus;
use App\Enums\ExportType;
use App\Models\Partner\ClassLesson;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class ExportClassLesson extends Export
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

    public function __construct(\App\Models\Partner\Export $export, string $fileType = 'csv')
    {
        parent::__construct($export, new ClassLesson(), $fileType);
    }

    public function __invoke(): array
    {
        $this->map();

        return $this->exportToFile();
    }

    protected function map(): array
    {
        if (in_array('start_date', array_keys($this->filters)))
            $this->filters['start_date'] = Carbon::parse($this->filters['start_date'])->format('Y-m-d');

        if (in_array('end_date', array_keys($this->filters)))
            $this->filters['end_date'] = Carbon::parse($this->filters['end_date'])->format('Y-m-d');

        return $this->filters;
    }

    protected function query(): Builder
    {
        $query = $this->model->query();

        foreach ($this->filters as $key => $value) {
            $operator = ExportType::operator($key);
            $query->{$operator[0]}($key, $operator[1], $value);
        }

        return $query->select($this->fields);
    }

    protected function exportToFile(): array
    {
        $headers = $this->fields;

        $file = fopen('php://temp', 'rw');

        // Insert headers
        fputcsv($file, $headers);

        // Insert data
        $exportData = $this->query()->get();

        foreach ($exportData as $record) {
            fputcsv($file, $record->toArray());
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
            'csv_file_name' => $filename,
            'file_rows' => count($exportData),
            'file_size' => strlen($csv),
            'completed_at' => Carbon::now(),
            'status' => ExportStatus::completed->name,
            'message' => $this->statusMessage
        ];
    }
}
