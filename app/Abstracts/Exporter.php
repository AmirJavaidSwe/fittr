<?php

namespace App\Abstracts;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

abstract class Exporter implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, Responsable, WithColumnFormatting
{
    use Exportable;

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
    public abstract function query() : Builder;
    protected abstract function applyFilters() : array;
    protected abstract function getExportData() : Collection;
    protected abstract function relationShips() : array;
    protected abstract function exportToFile() : array;
}
