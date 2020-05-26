<?php

namespace App\Model;

class DataTableOrder
{
    /**
     * @var string
     */
    public string $column;

    /**
     * @var bool
     */
    public bool $ascending;

    /**
     * @param string $column
     * @param bool $ascending
     */
    public function __construct(string $column, bool $ascending)
    {
        $this->column = $column;
        $this->ascending = $ascending;
    }
}
