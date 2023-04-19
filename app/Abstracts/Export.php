<?php

namespace App\Abstracts;

abstract class Export
{
    protected array $filters;
    protected object $model;
    protected object $query;
    protected function __construct($filters, $model) {
        $this->filters = $filters;
        $this->model = $model;
        $this->query = $this->model->query();
    }
    public abstract function __invoke() : array;
    protected abstract function fields() : array;
    protected abstract function modifyFilters() : array;
    protected abstract function query() : array;
}
