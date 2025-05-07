<?php

use PhpOffice\PhpSpreadsheet\Cell\CellAddress as Cell_CellAddress;
use PhpOffice\PhpSpreadsheet\Cell\CellRange as Cell_CellRange;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate as Cell_Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType as Cell_DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as Shared_Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat as Style_NumberFormat;

include_once EZ2_3RD_PLUGIN_DIR . 'composer/vendor/autoload.php';

final class Ez2_3rd_Spreadsheet
{
    protected $spreadsheet;

    protected $sheet;

    protected $sheet_idx;

    public function __construct()
    {
        if (!defined('FS_CHMOD_FILE')) {
            define('FS_CHMOD_FILE', (fileperms(ABSPATH . 'index.php') & 0777 | 0644));
        }
    }

    public function make_new(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->getDefaultStyle()->getFont()->setSize(12);
    }

    public function set_sheet(int $sheet_idx): void
    {
        try {
            $this->spreadsheet->getSheet($sheet_idx);
        } catch (Exception $e) {
            $this->spreadsheet->createSheet();
            $sheet_idx = $this->spreadsheet->getSheetCount() - 1;
        }
        $this->sheet_idx = $sheet_idx;
        $this->sheet = $this->spreadsheet->setActiveSheetIndex($this->sheet_idx);
    }

    public function set_row($data, int $row_idx = 1, array $args = []): void
    {
        foreach ($data as $col_idx => $value) {
            $this->set_cell($value, $col_idx + 1, $row_idx, $args[$col_idx] ?? []);
        }
    }

    public function add_row($data, ?int $row_idx = null, array $args = []): void
    {
        if (null === $row_idx) {
            $row_idx = $this->sheet->getHighestRow() + 1;
        } else {
            $this->sheet->insertNewRowBefore($row_idx);
        }

        foreach ($data as $col_idx => $value) {
            $this->set_cell($value, $col_idx + 1, $row_idx, $args[$col_idx] ?? []);
        }
    }

    public function set_cell($value, int $col_idx, int $row_idx = 1, array $args = []): void
    {
        if (null === $value) {
            return;
        }

        $cell = $this->sheet->getCell(Cell_Coordinate::stringFromColumnIndex($col_idx) . $row_idx);
        switch ($args['type'] ?? '') {
            case 'number':
                $cell->setValueExplicit($value, Cell_DataType::TYPE_NUMERIC);
                break;
            case 'date':
                $cell->setValue(Shared_Date::PHPToExcel($value));
                break;
            case 'string':
            default:
                $cell->setValueExplicit($value, Cell_DataType::TYPE_STRING);
                break;
        }
    }

    public function set_col_style(int $col_idx, string $type): void
    {
        $highestRow = $this->sheet->getHighestRow();
        if ($highestRow > 1) {
            switch ($type) {
                case 'date':
                    $cellRange = new Cell_CellRange(
                        Cell_CellAddress::fromColumnAndRow($col_idx + 1, 2),
                        Cell_CellAddress::fromColumnAndRow($col_idx + 1, $highestRow),
                    );
                    $this->sheet->getStyle($cellRange)
                        ->getNumberFormat()
                        ->setFormatCode(Style_NumberFormat::FORMAT_DATE_YYYYMMDD);
                    break;
                case 'percentage':
                    $cellRange = new Cell_CellRange(
                        Cell_CellAddress::fromColumnAndRow($col_idx + 1, 2),
                        Cell_CellAddress::fromColumnAndRow($col_idx + 1, $highestRow),
                    );
                    $this->sheet->getStyle($cellRange)
                        ->getNumberFormat()
                        ->setFormatCode(Style_NumberFormat::FORMAT_PERCENTAGE);
                    break;
            }
        }
    }

    public function set_title(string $title): void
    {
        $this->sheet->setTitle($title);
    }

    public function set_width(array $width = []): void
    {
        $max_idx = Cell_Coordinate::columnIndexFromString($this->sheet->getHighestColumn());
        for ($i = 0; $i < $max_idx; ++$i) {
            if (isset($width[$i]) && $width[$i] > 0) {
                $this->sheet->getColumnDimensionByColumn($i + 1)
                    ->setWidth($width[$i]);
            }
        }
    }

    public function remove_row(int $row_idx): void
    {
        $this->sheet->removeRow($row_idx);
    }

    public function get_cell(int $col_idx, ?int $row_idx = null)
    {
        if ($row_idx > $this->sheet->getHighestRow()) {
            return;
        }
        $cell = $this->sheet->getCell(Cell_Coordinate::stringFromColumnIndex($col_idx) . $row_idx);

        return $cell->getValue();
    }

    public function get_total_rows(): int
    {
        return $this->sheet->getHighestRow();
    }

    public function build(string $file_path, string $type, array $writer_callback = []): void
    {
        $sheetCount = $this->spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            $this->spreadsheet->getSheet($i)->setSelectedCell('A1');
        }
        $this->spreadsheet->setActiveSheetIndex(0);

        $writer = IOFactory::createWriter($this->spreadsheet, $type);
        foreach ($writer_callback as $callback => $args) {
            call_user_func_array([$writer, $callback], $args);
        }
        $writer->save($file_path);

        if (is_file($file_path)) {
            @chmod($file_path, FS_CHMOD_FILE); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_chmod
        }
    }

    public function read_file(string $file): void
    {
        $this->spreadsheet = IOFactory::load($file);
    }

    public function sheet_to_array(int $sheet_idx)
    {
        $this->sheet = $this->spreadsheet->setActiveSheetIndex($sheet_idx);

        return $this->sheet->toArray('', false, false, false);
    }

    public function value_to_datetime($value): DateTime
    {
        return Shared_Date::excelToDateTimeObject($value);
    }
}
