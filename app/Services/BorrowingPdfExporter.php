<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Borrowing;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Database\Eloquent\Collection;

final class BorrowingPdfExporter
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

        $rowsHtml = '';
        foreach ($borrowings as $index => $borrowing) {
            $status = $borrowing->returned_at !== null
                ? 'Dikembalikan'
                : ($borrowing->due_at !== null && $borrowing->due_at->isPast() ? 'Terlambat' : 'Aktif');

            $rowsHtml .= sprintf(
                '<tr>
                    <td style="text-align: center;">%d</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td style="text-align: center; font-family: monospace;">%s</td>
                    <td style="text-align: center;">%s</td>
                    <td style="text-align: center;">%s</td>
                    <td style="text-align: center;">%s</td>
                </tr>',
                $index + 1,
                e($borrowing->user->name ?? '-'),
                e($borrowing->item->name ?? '-'),
                e($borrowing->item->inventory_number ?? '-'),
                $borrowing->borrowed_at ? $borrowing->borrowed_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') : '-',
                $borrowing->due_at ? $borrowing->due_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') : '-',
                $status
            );
        }

        $html = sprintf(
            '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <style>
                    body { font-family: Helvetica, Arial, sans-serif; color: #1f2937; padding: 20px; }
                    .header { text-align: center; margin-bottom: 24px; }
                    .title { font-size: 20px; font-weight: bold; margin-bottom: 4px; }
                    .date { font-size: 12px; color: #6b7280; }
                    table { width: 100%%; border-collapse: collapse; margin-top: 12px; }
                    th, td { border: 1px solid #d1d5db; padding: 8px 10px; font-size: 12px; }
                    th { background-color: #f3f4f6; font-weight: bold; text-align: left; }
                </style>
            </head>
            <body>
                <div class="header">
                    <div class="title">Laporan Data Peminjaman Barang</div>
                    <div class="date">Tanggal: %s</div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 30px; text-align: center;">No</th>
                            <th>Peminjam</th>
                            <th>Nama Barang</th>
                            <th style="text-align: center;">Nomor Inventaris</th>
                            <th style="text-align: center;">Tgl Pinjam</th>
                            <th style="text-align: center;">Batas Kembali</th>
                            <th style="text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        %s
                    </tbody>
                </table>
            </body>
            </html>',
            now()->setTimezone('Asia/Jakarta')->format('d-m-Y H:i').' WIB',
            $rowsHtml
        );

        $options = new Options;
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return (string) $dompdf->output();
    }
}
