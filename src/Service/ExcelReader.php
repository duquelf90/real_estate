<?php
namespace App\Service;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelReader
{
    public function readExcelFile(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        return $sheetData;
    }
}