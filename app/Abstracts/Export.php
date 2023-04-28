<?php

namespace App\Abstracts;

use Illuminate\Database\Eloquent\Builder;

abstract class Export
{
    protected array $filters;
    protected array $fields;
    protected object $model;
    protected \App\Models\Partner\Export $export;
    protected string $statusMessage = '';
    protected string $fileType;
    protected string $exportPath = 'exports/{type}/{role}/{file_name}';
    protected function __construct(\App\Models\Partner\Export $export, object $model, string $fileType) {
        $this->export = $export;
        $this->filters = $export->filters;
        $this->model = $model;
        $this->fileType = $fileType;
    }
    public abstract function __invoke() : array;
    protected abstract function map() : array;
    protected abstract function query() : Builder;
    protected abstract function exportToFile() : array;
}
