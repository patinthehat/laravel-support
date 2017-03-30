<?php

namespace LaravelSupport\Support;

class TableColumnReference
{

    public $table;
    public $column;

    public function __construct($table, $column)
    {
        $this->table = $table;
        $this->column = $column;
    }

    public static function parse($reference)
    {
        $parts = explode('.', $reference);
        if (count($parts) < 2)
            throw new \Exception('Invalid table.column reference.');

        $table = $parts[0];
        $column = $parts[1];
        $result = new static($table, $column);
        return $result;
    }

    public static function guessForeignReference($columnName)
    {
        if (ends_with($columnName, '_id')) {
            $table = str_plural(substr($columnName, 0, strlen($columnName)-3));
            $column = 'id';
            return new static($table, $column);
        }

        $parts = explode('_', $columnName);
        if (count($parts) < 2)
            throw new \Exception('Invalid table_column reference.');

        $column = $parts[count($parts)-1];
        unset($parts[count($parts)-1]);
        return new static(implode('_', $parts), $column);
    }

    public function isValid()
    {
        return ( (strlen(trim($this->table)) > 0) && (strlen(trim($this->column)) > 0) );
    }

    public function asReferenceString()
    {
        return $this->table . '.' . $this->column;
    }
}
