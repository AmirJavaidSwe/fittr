<?php

namespace App\Abstracts;

abstract class Export
{
    protected array $filters;
    protected function __construct($filters) {
        $this->filters = $filters;
    }
    protected abstract function fields() : array;
    protected abstract function modifyFilters() : array;
    protected abstract function query() : array;
}
