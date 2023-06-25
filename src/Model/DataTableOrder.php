<?php

namespace App\Model;

class DataTableOrder
{
    public string $column;

    public bool $ascending;

    public function __construct(string $column, bool $ascending)
    {
        $this->column = $column;
        $this->ascending = $ascending;
    }
}
