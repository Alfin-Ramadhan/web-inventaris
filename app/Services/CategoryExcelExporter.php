<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

final class CategoryExcelExporter
{
    /**
     * @param  Collection<int, Category>|null  $categories
     */
    public static function generate(?Collection $categories = null): string
    {
        $categories ??= Category::query()->orderBy('name')->get();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kategori Barang');

        // Header Row
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Kategori');
        $sheet->setCellValue('C1', 'Total Stok');
        $sheet->setCellValue('D1', 'Stok Tersedia');

        // Bold header styling
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        $row = 2;
        foreach ($categories as $index => $category) {
            $sheet->setCellValue('A'.$row, $index + 1);
            $sheet->setCellValue('B'.$row, $category->name);
            $sheet->setCellValue('C'.$row, $category->total_quantity);
            $sheet->setCellValue('D'.$row, $category->available_quantity);
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');

        return (string) ob_get_clean();
    }
}
