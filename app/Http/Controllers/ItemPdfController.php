<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Item;
use App\Services\ItemQrPdfGenerator;
use Symfony\Component\HttpFoundation\Response;

final class ItemPdfController
{
    public function __invoke(Item $item): Response
    {
        $pdfContent = ItemQrPdfGenerator::generate($item);

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('attachment; filename="QR-Code-%s.pdf"', $item->inventory_number ?? $item->id),
        ]);
    }
}
