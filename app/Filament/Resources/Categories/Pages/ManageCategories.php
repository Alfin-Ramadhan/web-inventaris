<?php

declare(strict_types=1);

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Services\CategoryExcelExporter;
use App\Services\CategoryPdfExporter;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ManageCategories extends ManageRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-text')
                ->color('danger')
                ->action(function (): StreamedResponse {
                    return response()->streamDownload(
                        static function (): void {
                            echo CategoryPdfExporter::generate();
                        },
                        sprintf('Kategori-Barang-%s.pdf', now()->format('Y-m-d')),
                        ['Content-Type' => 'application/pdf']
                    );
                }),
            Action::make('exportExcel')
                ->label('Export Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->action(function (): StreamedResponse {
                    return response()->streamDownload(
                        static function (): void {
                            echo CategoryExcelExporter::generate();
                        },
                        sprintf('Kategori-Barang-%s.xlsx', now()->format('Y-m-d')),
                        ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
                    );
                }),
            CreateAction::make(),
        ];
    }
}
