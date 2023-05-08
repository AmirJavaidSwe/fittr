<?php

namespace App\Abstracts;

use App\Enums\ExportFileType;
use App\Models\Partner\Export;
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
use Maatwebsite\Excel\Excel;

abstract class Exporter implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, Responsable, WithColumnFormatting
{
    use Exportable;

    protected array $filters;
    protected array $fields;
    protected object $model;
    protected Export $export;
    protected string $statusMessage = '';
    protected string $fileType;
    protected string $exportPath = 'exports/{type}/{role}/{file_name}';
    protected function __construct(Export $export, object $model) {
        $this->export = $export;
        $this->filters = $export->filters;
        $this->model = $model;
    }
    public abstract function __invoke() : array;
    public abstract function query() : Builder;
    protected abstract function applyFilters() : array;
    protected abstract function relationShips() : array;
    protected abstract function export() : array;
    public function getWriterType()
    {
        $fileType = [
            ExportFileType::csv->name => Excel::CSV,
            ExportFileType::xls->name => Excel::XLS,
            ExportFileType::xlsx->name => Excel::XLSX
        ];

        return $fileType[$this->export->file_type];
    }
}
