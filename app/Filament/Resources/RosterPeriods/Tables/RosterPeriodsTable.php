<?php

namespace App\Filament\Resources\RosterPeriods\Tables;

use App\Filament\Resources\RosterPeriods\RosterPeriodResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RosterPeriodsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('unit.name')
                    ->label('Unit')
                    ->sortable()
                    ->searchable()
                    ->visible(fn () => auth()->user()?->seesAllUnits() ?? false),
                TextColumn::make('period_start')
                    ->label('Period')
                    ->formatStateUsing(fn ($record) => $record->period_start->format('d M Y').' – '.$record->period_end->format('d M Y')
                    )
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($record) => $record->statusLabel())
                    ->color(fn ($record) => $record->statusColor()),
                TextColumn::make('minimum_duty_days')
                    ->label('Min. Days'),
                TextColumn::make('agreed_at')
                    ->date('d M Y')
                    ->placeholder('—'),
                TextColumn::make('approved_at')
                    ->date('d M Y')
                    ->placeholder('—'),
                TextColumn::make('submitted_to_moh_at')
                    ->label('Submitted to MoH')
                    ->date('d M Y')
                    ->placeholder('—'),
            ])
            ->defaultSort('period_start', 'desc')
            ->filters([])
            ->recordActions([
                Action::make('grid')
                    ->label('Open Grid')
                    ->icon('heroicon-o-table-cells')
                    ->url(fn ($record) => RosterPeriodResource::getUrl('grid', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
