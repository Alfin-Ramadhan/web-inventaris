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

        $qrPng = (new QRCode($qrOptions))->render($url);

        $html = sprintf(
            '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <style>
                    body { font-family: Helvetica, Arial, sans-serif; text-align: center; padding: 40px; color: #1f2937; }
                    .card { border: 2px solid #374151; border-radius: 12px; padding: 30px; display: inline-block; width: 320px; background: #ffffff; }
                    .title { font-size: 22px; font-weight: bold; margin-bottom: 8px; }
                    .subtitle { font-size: 14px; color: #4b5563; margin-bottom: 20px; font-family: monospace; }
                    .qr img { width: 220px; height: 220px; }
                    .footer { margin-top: 20px; font-size: 11px; color: #6b7280; font-family: monospace; word-break: break-all; }
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
