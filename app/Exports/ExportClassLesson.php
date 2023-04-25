<?php

namespace App\Exports;

use App\Abstracts\Export;
use App\Enums\ExportType;
use App\Models\Partner\ClassLesson;
use Carbon\Carbon;

class ExportClassLesson extends Export
{
    public function __construct(array $filters, string $fileType = 'csv')
    {
        parent::__construct($filters, new ClassLesson(), $fileType);
    }

    public function __invoke(): array
    {
        $this->modifyFilters();

        return $this->query();
    }

    public function fields(): array
    {
        return [
            'id',
            'start_date',
            'status',
            'end_date',
            'studio_id',
            'instructor_id',
            'created_at',
            'updated_at'
        ];
    }

    protected function modifyFilters(): array
    {
        if (in_array('start_date', array_keys($this->filters)))
            $this->filters['start_date'] = Carbon::parse($this->filters['start_date'])->format('Y-m-d');

        if (in_array('end_date', array_keys($this->filters)))
            $this->filters['end_date'] = Carbon::parse($this->filters['end_date'])->format('Y-m-d');

        return $this->filters;
    }

    protected function query(): array
    {
        foreach ($this->filters as $key => $value) {
            $operator = ExportType::operator($key);
            $this->query->{$operator[0]}($key, $operator[1], $value);
        }

        return $this->query->select($this->fields())->get()->toArray();
    }
}
