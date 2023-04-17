<?php

namespace App\Exports;

abstract class Export
{
    protected abstract function fields() : array;

    protected abstract function filters() : array;

    protected abstract function query() : array;
}
