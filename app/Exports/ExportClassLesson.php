<?php

namespace App\Exports;

use App\Abstracts\Exporter;
use App\Enums\ExportStatus;
use App\Enums\ExportType;
use App\Models\Partner\ClassLesson;
use App\Models\Partner\Export;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Str;

class ExportClassLesson extends Exporter
{
    protected array $fields = [
        'id',
        'title',
        'status',
        'start_date',
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
            'ID',
            'Title',
            'Status',
            'Start Date',
            'End Date',
            'Studio',
            'Studio ID',
            'Instructor',
            'Instructor ID',
            'Updated At'
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->title,
            $row->status,
            $row->start_date->format('Y-m-d h:i A'),
            $row->end_date->format('Y-m-d h:i A'),
            $row->studio->title,
            $row->studio_id,
            $row?->instructor?->email,
            $row?->instructor_id,
            $row->updated_at
        ];
    }

    public function title(): string
    {
        return 'Fittr Class Lesson Export';
    }

    private function exportTo(): array
    {
        $fileName = $this->export->type.'_'.date('y_m_d').'_'.Str::random(5).'.'.$this->export->file_type;

        $storagePath = str_replace(['{type}', '{role}', '{file_name}'], [
            $this->export->type, $this->export->created_by, $fileName
        ], $this->exportPath);

        $disk = app()->environment('local') ? 'local' : 's3';

        $this->store($storagePath, $disk, $this->getWriterType());

        return [
            'file_path' => $storagePath,
            'file_name' => $fileName,
            'file_rows' => $this->query()->count(),
            'file_size' => Storage::size($storagePath),
            'completed_at' => Carbon::now(),
            'status' => ExportStatus::completed->name,
            'storage_disk' => $disk,
            'message' => $this->statusMessage
        ];
    }
}
