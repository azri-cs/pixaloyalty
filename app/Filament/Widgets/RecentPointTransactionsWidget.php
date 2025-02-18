<?php

namespace App\Filament\Widgets;

use App\Enums\PointTransactionType;
use App\Models\PointTransaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentPointTransactionsWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Point Transactions';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PointTransaction::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('customer.first_name')
                    ->label('Customer')
                    ->formatStateUsing(fn ($record) => $record->customer->first_name . ' ' . $record->customer->last_name),
                Tables\Columns\TextColumn::make('transaction_type')
                    ->formatStateUsing(fn (string $state) => PointTransactionType::from($state)->getLabel())
                    ->badge()
                    ->color(fn (string $state): string => PointTransactionType::from($state)->getColor()),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => number_format($state)),
                Tables\Columns\TextColumn::make('description')
                    ->limit(30),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->poll('15s')
            ->deferLoading();
    }
}
