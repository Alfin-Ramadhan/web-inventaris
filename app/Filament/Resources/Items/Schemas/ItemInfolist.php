<?php

declare(strict_types=1);

namespace App\Filament\Resources\Items\Schemas;

use App\Enums\ItemStatus;
use App\Filament\Resources\Items\ItemResource;
use App\Models\Item;
use chillerlan\QRCode\QRCode;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

final class ItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextEntry::make('category.name')
                    ->label('Kategori')
                    ->placeholder('Tidak ada kategori'),
                TextEntry::make('name')
                    ->label('Nama Barang'),
                TextEntry::make('inventory_number')
                    ->label('Nomor Inventaris')
                    ->placeholder('Tidak ada nomor inventaris'),
                TextEntry::make('status')
                    ->label('Status')
                    ->state(fn (Item $record): ItemStatus => $record->quantity >= 1 ? ItemStatus::Available : ItemStatus::NotAvailable)
                    ->badge()
                    ->color(fn (ItemStatus $state): string => match ($state) {
                        ItemStatus::Available => 'success',
                        ItemStatus::NotAvailable => 'danger',
                    }),
                TextEntry::make('qr_code')
                    ->label('QR Code')
                    ->state(function (Item $record): HtmlString {
                        $url = ItemResource::getUrl('view', ['record' => $record]);
                        $pdfUrl = route('items.pdf', ['item' => $record->inventory_number]);
                        $svgDataUri = (new QRCode)->render($url);
                        $recordId = e($record->id);

                        return new HtmlString(sprintf(
                            '<div style="display: inline-block; padding: 10px; background: #ffffff; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); width: 170px; text-align: center;"><img id="qr-img-%s" src="%s" alt="QR Code" style="width: 140px !important; height: 140px !important; max-width: 140px !important; max-height: 140px !important; display: block; margin: 0 auto;" /><div style="margin-top: 8px; display: flex; gap: 6px; justify-content: center;"><button type="button" onclick="copyQrImage(\'%s\', this)" style="padding: 4px 8px; font-size: 11px; font-weight: 500; color: #374151; background-color: #f3f4f6; border: 1px solid #d1d5db; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; gap: 2px;"><span>Copy</span></button><a href="%s" target="_blank" style="padding: 4px 8px; font-size: 11px; font-weight: 500; color: #ffffff; background-color: #16a34a; border: 1px solid #15803d; border-radius: 4px; text-decoration: none; display: inline-flex; align-items: center; gap: 2px;"><span>Cetak PDF</span></a></div></div><script>if (typeof window.copyQrImage !== "function") { window.copyQrImage = function(id, btn) { const img = document.getElementById("qr-img-" + id); if (!img) return; const canvas = document.createElement("canvas"); canvas.width = 300; canvas.height = 300; const ctx = canvas.getContext("2d"); const image = new Image(); image.crossOrigin = "anonymous"; image.onload = function() { ctx.fillStyle = "#ffffff"; ctx.fillRect(0, 0, canvas.width, canvas.height); ctx.drawImage(image, 0, 0, canvas.width, canvas.height); canvas.toBlob(function(blob) { if (blob && navigator.clipboard && navigator.clipboard.write) { navigator.clipboard.write([new ClipboardItem({ "image/png": blob })]).then(() => { const orig = btn.innerHTML; btn.innerHTML = "<span>Disalin!</span>"; setTimeout(() => { btn.innerHTML = orig; }, 2000); }).catch(() => alert("Gagal menyalin QR Code")); } }, "image/png"); }; image.src = img.src; }; }</script>',
                            $recordId,
                            $svgDataUri,
                            $recordId,
                            e($pdfUrl)
                        ));
                    }),
            ]);
    }
}
