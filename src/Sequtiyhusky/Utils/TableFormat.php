<?php

namespace Sequtiyhusky\Fania\Utils;

use ConsoleFormat as CF;

/**
 * Something TODO:
 *  - Separator option for each row
 *  
 */
class TableFormat
{

    private $chars = array(
        'top'          => '═',
        'top-mid'      => '╤',
        'top-left'     => '╔',
        'top-right'    => '╗',
        'bottom'       => '═',
        'bottom-mid'   => '╧',
        'bottom-left'  => '╚',
        'bottom-right' => '╝',
        'left'         => '║',
        'left-mid'     => '╟',
        'mid'          => '─',
        'mid-mid'      => '┼',
        'right'        => '║',
        'right-mid'    => '╢',
        'middle'       => '│ ',
    );

    private $headers = [];

    private $fields = [];

    private $align;

    private $usingHeader;

    private $columnLengths = [];

    /**
     * Constructor for the TableFormat class.
     *
     * @param bool   $usingHeader Whether to use headers in the table.
     * @param array  $data The data to be formatted into a table.
     * @param string $align The alignment of the table content (default is 'left').
     */
    public function __construct(bool $usingHeader = true, array $data, string $align = 'right')
    {
        $this->usingHeader = $usingHeader;
        $this->align  = $align;
        $this->seperateRows($data);
    }

    /**
     * Separates the rows from the data and sets the headers if usingHeader is true.
     *
     * @param array $data The data to be separated into rows.
     */
    private function seperateRows(array $data): void
    {
        if ($this->usingHeader) {
            $this->headers = array_shift($data);
        } else {
            $this->headers = [];
        }

        $this->fields = $data;
        // $this->formatTable();
    }

    /**
     * Formats the table by calculating the lengths of each column and adjusting the rows accordingly.
     */
    private function formatTable()
    {
        $rowCounter = 0;
        $columnLengths = [];

        foreach ($this->fields as $fields) {
            $counter = 0;
            foreach ($fields as $row) {
                $row = trim($row);
                if (strstr($row, "\n")) {
                    $rowExplode = explode("\n", $row);
                    unset($this->fields[$rowCounter][$counter]);

                    foreach ($rowExplode as $rowItem) {
                        $rowItem = trim($rowItem);
                        $this->fields[$rowCounter][$counter][] = $rowItem;
                    }
                }
                $counter++;
            }
            $rowCounter++;
        }

        unset($rowCounter);

        for ($i = 0; $i < count($this->fields[0]); $i++) {
            for ($j = 0; $j < count($this->fields); $j++) {
                if (is_array($this->fields[$j][$i])) {
                    foreach ($this->fields[$j][$i] as $item) {
                        $length = mb_strlen($item);
                        if (empty($columnLengths[$i]) || $length > $columnLengths[$i]) {
                            $columnLengths[$i] = $length;
                        }
                    }
                } else {
                    $length = mb_strlen($this->fields[$j][$i]);
                    if (empty($columnLengths[$i]) || $length > $columnLengths[$i]) {
                        $columnLengths[$i] = $length;
                    }
                }
            }
        }

        if ($this->usingHeader) {
            foreach ($this->headers as $i => $header) {
                $length = mb_strlen($header);
                if (empty($columnLengths[$i]) || $length > $columnLengths[$i]) {
                    $columnLengths[$i] = $length;
                }
            }
        }

        $this->columnLengths = $columnLengths;
    }

    /**
     * Prints the formatted table to the console.
     *
     * @param array $columnLengths The lengths of each column.
     */
    public function printTable(): void
    {
        if ($this->usingHeader) {
            // print head
            echo $this->chars['top-left'];
            for ($i = 0; $i < count($this->headers); $i++) {
                echo str_repeat($this->chars["top"], $this->columnLengths[$i] + 2) . ($i < count($this->headers) - 1 ? $this->chars["top-mid"] : '');
            }
            echo $this->chars['top-right'] . "\n";


            echo $this->chars['left'] . " ";
            foreach ($this->headers as $i => $header) {
                switch ($this->align) {
                    case 'left':
                        echo trim($header) . \str_repeat(" ", $this->columnLengths[$i] - mb_strlen(trim($header))) . " " . ($i < count($this->columnLengths) - 1 ? $this->chars['middle'] : $this->chars['right']);
                        break;
                }
            }
            echo "\n";

            echo $this->chars['left-mid'];
            for ($i = 0; $i < count($this->headers); $i++) {
                echo str_repeat($this->chars["mid"], $this->columnLengths[$i] + 2) . ($i < count($this->headers) - 1 ? $this->chars["mid-mid"] : '');
            }
            echo $this->chars['right-mid'] . "\n";
        }

        foreach ($this->fields as $fields) {
            echo $this->chars['left'] . " ";
            foreach ($fields as $i => $field) {
                if (is_array($field)) {
                    foreach ($field as $k => $item) {
                        switch ($this->align) {
                            case 'left':
                                echo trim($item) . \str_repeat(" ", $this->columnLengths[$i] - mb_strlen(trim($item))) . " " . ($i < count($fields) - 1 ? $this->chars['middle'] : $this->chars['right']);
                                echo ($k < count($field) - 1 ? "\n" . $this->chars['left'] . " " : '');
                                for ($j = 0; $j < count($this->columnLengths) - 1; $j++) {
                                    if ($k < count($field) - 1) {
                                        echo str_repeat(" ", $this->columnLengths[$j] + 1) . $this->chars['middle'];
                                    }
                                }
                                break;
                        }
                    }
                } else {
                    switch ($this->align) {
                        case 'left':
                            echo trim($field) . \str_repeat(" ", $this->columnLengths[$i] - mb_strlen(trim($field))) . " " . ($i < count($fields) - 1 ? $this->chars['middle'] : $this->chars['right']);
                            break;
                    }
                }
            }
            echo "\n";

            if ($this->usingHeader && ($fields !== end($this->fields))) {
                echo $this->chars['left-mid'];
                for ($i = 0; $i < count($fields); $i++) {
                    echo str_repeat($this->chars["mid"], $this->columnLengths[$i] + 2) . ($i < count($fields) - 1 ? $this->chars["mid-mid"] : '');
                }
                echo $this->chars['right-mid'] . "\n";
            }
        }

        echo $this->chars['bottom-left'];
        for ($i = 0; $i < count($this->columnLengths); $i++) {
            echo str_repeat($this->chars["bottom"], $this->columnLengths[$i] + 2) . ($i < count($this->columnLengths) - 1 ? $this->chars["bottom-mid"] : '');
        }
        echo $this->chars['bottom-right'] . "\n";
    }


    /**
     * Adds a field to the table.
     *
     * @param array $data The data to be added as a field.
     */
    public function addField(array $data): void
    {
        if ($this->usingHeader && (empty($this->fields) && empty($this->headers))) {
            $this->headers[] = $data;
        }

        if (!empty($this->fields)) {
            if (count($data) !== count($this->fields[0])) {
                throw new \InvalidArgumentException("Jumlah kolom tidak sesuai dengan tabel yang ada.");
            }
        }

        $this->fields[] = $data;
        $this->formatTable();
    }
}