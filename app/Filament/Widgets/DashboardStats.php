<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Item;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

final class DashboardStats extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        $driver = Item::query()->getConnection()->getDriverName();
        $monthExpr = $driver === 'sqlite' ? "cast(strftime('%m', created_at) as integer)" : 'MONTH(created_at)';

        /** @var array<int, int> $itemCounts */
        $itemCounts = Item::query()
            ->selectRaw("{$monthExpr} as month, COUNT(*) as count")
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->all();

        /** @var array<int, float> $quantitySums */
        $quantitySums = Item::query()
            ->selectRaw("{$monthExpr} as month, SUM(quantity) as total")
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->all();

        /** @var array<int, int> $itemChart */
        $itemChart = [];
        /** @var array<int, float> $quantityChart */
        $quantityChart = [];

        for ($i = 1; $i <= 12; $i++) {
            $itemChart[] = $itemCounts[$i] ?? 0;
            $quantityChart[] = $quantitySums[$i] ?? 0.0;
        }

        $activeCount = Borrowing::query()
            ->where('user_id', auth()->id())
            ->whereNull('returned_at')
            ->count();

        $overdueCount = Borrowing::query()
            ->where('user_id', auth()->id())
            ->whereNull('returned_at')
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->count();

        return [
            Stat::make('Total Barang', Item::query()->count())
                ->description('Semua unit barang inventaris')
                ->descriptionIcon('heroicon-m-cube')
                ->chart($itemChart)
                ->color('primary'),

            Stat::make('Total Kategori', Category::query()->count())
                ->description('Kategori barang inventaris')
                ->descriptionIcon('heroicon-m-tag')
                ->chart($quantityChart)
                ->color('warning'),

            Stat::make('Peminjaman Aktif', $activeCount)
                ->description('Barang yang sedang Anda pinjam')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),

            Stat::make('Terlambat Kembali', $overdueCount)
                ->description('Barang yang melewati batas waktu')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($overdueCount > 0 ? 'danger' : 'success'),
        ];
    }
}
