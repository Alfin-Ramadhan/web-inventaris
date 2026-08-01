<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Database\Eloquent\Collection;

final class CategoryPdfExporter
{
    /**
     * @param  Collection<int, Category>|null  $categories
     */
    public static function generate(?Collection $categories = null): string
    {
        $categories ??= Category::query()->orderBy('name')->get();

        $rowsHtml = '';
        foreach ($categories as $index => $category) {
            $rowsHtml .= sprintf(
                '<tr>
                    <td style="text-align: center;">%d</td>
                    <td>%s</td>
                    <td style="text-align: center;">%d</td>
                    <td style="text-align: center;">%d</td>
                </tr>',
                $index + 1,
                e($category->name),
                $category->total_quantity,
                $category->available_quantity
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
                    th, td { border: 1px solid #d1d5db; padding: 8px 12px; font-size: 13px; }
                    th { background-color: #f3f4f6; font-weight: bold; text-align: left; }
                </style>
            </head>
            <body>
                <div class="header">
                    <div class="title">Laporan Data Kategori Barang</div>
                    <div class="date">Tanggal: %s</div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">No</th>
                            <th>Nama Kategori</th>
                            <th style="width: 100px; text-align: center;">Total Stok</th>
                            <th style="width: 110px; text-align: center;">Stok Tersedia</th>
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
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return (string) $dompdf->output();
    }
}
