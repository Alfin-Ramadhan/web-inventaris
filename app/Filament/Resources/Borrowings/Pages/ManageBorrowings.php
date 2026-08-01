<?php

declare(strict_types=1);

namespace App\Filament\Resources\Borrowings\Pages;

use App\Filament\Resources\Borrowings\BorrowingResource;
use App\Services\BorrowingExcelExporter;
use App\Services\BorrowingPdfExporter;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ManageBorrowings extends ManageRecords
{
    protected static string $resource = BorrowingResource::class;

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
                            echo BorrowingPdfExporter::generate();
                        },
                        sprintf('Data-Peminjaman-%s.pdf', now()->format('Y-m-d')),
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
                            echo BorrowingExcelExporter::generate();
                        },
                        sprintf('Data-Peminjaman-%s.xlsx', now()->format('Y-m-d')),
                        ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
                    );
                }),
            CreateAction::make(),
        ];
    }
}
