<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Borrowing;
use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

final class BorrowingExcelExporter
{
    /**
     * @param  Collection<int, Borrowing>|null  $borrowings
     */
    public static function generate(?Collection $borrowings = null): string
    {
        $borrowings ??= Borrowing::query()
            ->with(['user', 'item'])
            ->latest('borrowed_at')
            ->get();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Peminjaman');

        // Header Row
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Peminjam');
        $sheet->setCellValue('C1', 'Nama Barang');
        $sheet->setCellValue('D1', 'Nomor Inventaris');
        $sheet->setCellValue('E1', 'Tanggal Pinjam');
        $sheet->setCellValue('F1', 'Batas Kembali');
        $sheet->setCellValue('G1', 'Tanggal Dikembalikan');
        $sheet->setCellValue('H1', 'Status');

        // Bold header styling
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        $row = 2;
        foreach ($borrowings as $index => $borrowing) {
            $status = $borrowing->returned_at !== null
                ? 'Dikembalikan'
                : ($borrowing->due_at !== null && $borrowing->due_at->isPast() ? 'Terlambat' : 'Aktif');

            $sheet->setCellValue('A'.$row, $index + 1);
            $sheet->setCellValue('B'.$row, $borrowing->user->name ?? '-');
            $sheet->setCellValue('C'.$row, $borrowing->item->name ?? '-');
            $sheet->setCellValue('D'.$row, $borrowing->item->inventory_number ?? '-');
            $sheet->setCellValue('E'.$row, $borrowing->borrowed_at ? $borrowing->borrowed_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') : '-');
            $sheet->setCellValue('F'.$row, $borrowing->due_at ? $borrowing->due_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') : '-');
            $sheet->setCellValue('G'.$row, $borrowing->returned_at ? $borrowing->returned_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') : '-');
            $sheet->setCellValue('H'.$row, $status);
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');

        return (string) ob_get_clean();
    }
}
