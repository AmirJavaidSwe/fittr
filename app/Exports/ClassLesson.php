<?php

namespace App\Exports;

class ClassLesson extends Export
{
    private $classes;
    private array $filters;

    public function __construct(array $filters)
    {
        $this->classes = new \App\Models\Partner\ClassLesson;
        $this->filters = $filters;
    }

    public function fields(): array
    {
        return array(
            'id',
            'name',
            'description',
            'start_date',
            'end_date',
            'start_time',
            'end_time',
            'duration',
            'capacity',
            'price',
            'currency',
            'status',
            'studio_id',
            'instructor_id',
            'created_at',
            'updated_at',
        );
    }

    protected function filters(): array
    {
        // TODO: Implement filters() method.
    }

    protected function query(): array
    {
        // TODO: Implement query() method.
    }
}
