<?php

namespace App\Filament\Widgets;

use App\Models\House;
use App\Models\Installment;
use App\Models\MortgageRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class PortfolioStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $houseQuery = House::query();
        $mortgageQuery = MortgageRequest::query();
        $installmentQuery = Installment::query();

        if (auth()->user()?->hasRole('developer') && ! auth()->user()?->hasRole('admin')) {
            $developerId = auth()->id();

            $houseQuery->where('developer_id', $developerId);
            $mortgageQuery->whereHas('house', fn (Builder $query) => $query->where('developer_id', $developerId));
            $installmentQuery->whereHas('mortgageRequest.house', fn (Builder $query) => $query->where('developer_id', $developerId));
        }

        $totalHouses = (clone $houseQuery)->count();
        $totalRequests = (clone $mortgageQuery)->count();
        $approvedRequests = (clone $mortgageQuery)->where('status', 'Approved')->count();
        $paidInstallments = (clone $installmentQuery)->where('is_paid', true)->sum('grand_total_amount');

        return [
            Stat::make('Managed Houses', number_format($totalHouses))
                ->description('Active property inventory')
                ->icon('heroicon-o-home-modern')
                ->color('primary'),

            Stat::make('Mortgage Requests', number_format($totalRequests))
                ->description('Submitted customer applications')
                ->icon('heroicon-o-document-text')
                ->color('info'),

            Stat::make('Approved Requests', number_format($approvedRequests))
                ->description('Ready for installment flow')
                ->icon('heroicon-o-check-badge')
                ->color('success'),

            Stat::make('Paid Installments', 'Rp '.number_format($paidInstallments, 0, ',', '.'))
                ->description('Collected through payment records')
                ->icon('heroicon-o-banknotes')
                ->color('warning'),
        ];
    }
}
