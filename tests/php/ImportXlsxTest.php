<?php

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\Framework\TestCase;

final class ImportXlsxTest extends TestCase
{
    private string $file;

    protected function setUp(): void
    {
        $this->file = sys_get_temp_dir().'/andylib-'.bin2hex(random_bytes(6)).'.xlsx';
    }

    protected function tearDown(): void
    {
        @unlink($this->file);
    }

    public function testReadsFirstSheetByRowAndColumn(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'name');
        $sheet->setCellValue('B1', 'count');
        $sheet->setCellValue('A2', 'apples');
        $sheet->setCellValue('B2', 0);
        $rich = new RichText();
        $rich->createTextRun('bold')->getFont()->setBold(true);
        $rich->createText(' text');
        $sheet->setCellValue('A3', $rich);
        $spreadsheet->createSheet()->setCellValue('A1', 'second sheet');
        (new Xlsx($spreadsheet))->save($this->file);

        $this->assertSame([
            1 => ['A' => 'name', 'B' => 'count'],
            2 => ['A' => 'apples', 'B' => 0],
            3 => ['A' => 'bold text', 'B' => ''],
        ], (new AndyLib())->importXlsx($this->file));
    }
}
