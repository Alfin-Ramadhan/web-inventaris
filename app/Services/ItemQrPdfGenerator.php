<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Item;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\URL;

final class ItemQrPdfGenerator
{
    public static function generate(Item $item): string
    {
        $url = URL::to('/admin/items/'.$item->inventory_number);

        $qrOptions = new QROptions([
            'outputType' => QROutputInterface::GDIMAGE_PNG,
            'scale' => 10,
        ]);

        /** @var string $qrPng */
        $qrPng = (new QRCode)->render($url);

        $html = sprintf(
            '<!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: sans-serif; text-align: center; padding: 20px; }
                    .card { border: 1px solid #ddd; border-radius: 8px; padding: 20px; display: inline-block; }
                    .title { font-size: 18px; font-weight: bold; margin-bottom: 5px; }
                    .subtitle { font-size: 14px; color: #666; margin-bottom: 15px; }
                    .qr img { width: 200px; height: 200px; }
                    .footer { font-size: 12px; color: #888; margin-top: 15px; }
                </style>
            </head>
            <body>
                <div class="card">
                    <div class="title">%s</div>
                    <div class="subtitle">Nomor Inventaris: %s</div>
                    <div class="qr"><img src="%s" alt="QR Code" /></div>
                    <div class="footer">%s</div>
                </div>
            </body>
            </html>',
            e($item->name),
            e($item->inventory_number ?? '-'),
            $qrPng,
            e($url)
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
