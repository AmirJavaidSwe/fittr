<?php

namespace App\Exports;

use App\Abstracts\Export;
use App\Enums\ExportType;
use App\Models\Partner\ClassLesson;
use Carbon\Carbon;

class ExportClassLesson extends Export
{
    private ClassLesson $classLesson;

    public function __construct(array $filters)
    {
        parent::__construct($filters);
    }

    public function __invoke(): array
    {
        $this->modifyFilters();
        $this->query();
    }

    public function fields(): array
    {
        return [
            'id',
            'name',
            'description',
            'start_date',
            'end_date',
            'start_time',
            'end_time',
            'duration',
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
        $this->classLesson = new ClassLesson;

        foreach ($this->filters as $key => $value) {
            $operator = ExportType::operator($key);
            dump($key, $operator, $value);
//            $this->classLesson = $this->classLesson->{$operator[0]}($key, $operator[1], $value);
        }

        dd($this->filters);

        return $this->classLesson->get($this->fields())->toArray();
    }
}
