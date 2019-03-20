<?php

namespace App\Support;

use Illuminate\Support\Str;
use InvalidArgumentException;

class Position
{
    public const ALPHABET = [
        "a",
        "b",
        "c",
        "d",
        "e",
        "f",
        "g",
        "h",
        "i",
        "j",
        "k",
        "l",
        "m",
        "n",
        "o",
        "p",
        "q",
        "r",
        "s",
        "t",
        "u",
        "v",
        "w",
        "x",
        "y",
        "z",
    ];

    protected $position;

    protected $label;

    protected $rowLabel;

    protected $rows;

    protected $columnLabel;

    protected $columns;

    protected $showPlates;

    protected $startWithZero = false;

    public function __construct($position, $isLabel = false)
    {
        if ($isLabel) {
            $this->label = $position;
        } else {
            $this->position = $position;
        }

        $this->columnLabel = 'ABC';
        $this->rowLabel = '123';
    }

    public static function fromPosition($position)
    {
        return new static($position);
    }

    public static function fromLabel($label)
    {
        return new static($label, true);
    }

    public function startWithZero($startWithZero = true)
    {
        $this->startWithZero = $startWithZero;

        return $this;
    }

    public function withRows($rows)
    {
        $this->rows = $rows;

        return $this;
    }

    public function withColumns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    public function showPlates($showPlates = true)
    {
        $this->showPlates = $showPlates;

        return $this;
    }

    public function toLabel()
    {
        if (! $this->label) {
            if (! $this->rows || ! $this->columns) {
                throw new InvalidArgumentException('Rows and Columns need to be defined');
            }
            if (! $this->startWithZero) {
                $position = ($this->position - 1) % ($this->rows * $this->columns) + 1;
            } else {
                $position = $this->position % ($this->rows * $this->columns) + 1;
            }

            $column = $position % $this->columns;
            $column = $column == 0 ? $this->columns : $column;
            $row = (($position - $column) / $this->columns) + 1;

            $this->label = sprintf("%s%'.02d", strtoupper($this->numberToAlpha($column)), $row);

            if ($this->showPlates) {
                $plate = (int) ($this->position / ($this->columns * $this->rows)) + 1;
                $this->label = sprintf("P%'.03d %s", $plate, $this->label);
            }
        }

        return $this->label;
    }

    public function numberToAlpha($number)
    {
        if ($number > count(self::ALPHABET)) {
            throw new InvalidArgumentException('Number is not convertable to character');
        }

        return self::ALPHABET[$number - 1];
    }

    public function toPosition()
    {
        if (! $this->position) {
            if (! $this->rows || ! $this->columns) {
                throw new InvalidArgumentException('Rows and Columns need to be defined');
            }

            preg_match_all('/([a-z])([0-9]+)/i', $this->label, $matches);

            [$all, $column, $row] = $matches;

            $column = count($column) == 2 ? $column[1] : $column[0];
            $plate = 0;

            if (count($row) == 2) {
                $plate = ((int) $row[0]) - 1;
                $row = $row[1];
            } else {
                $row = $row[0];
            }

            $column = array_search(strtolower($column), self::ALPHABET);

            if (! $this->startWithZero) {
                $column = $column + 1;
            }
            $this->position = (((int) $row) - 1) * $this->columns + $column;
            $this->position += $plate * $this->columns * $this->rows;
        }

        return $this->position;
    }
}
