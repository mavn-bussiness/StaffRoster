<?php

namespace App\Filament\Resources\LeaveRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nurse.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'sick' => 'danger',
                        'maternity' => 'pink',
                        'compassionate' => 'purple',
                        'other' => 'gray',
                        default => 'info',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('start_date')->date('d M Y')->sortable(),
                TextColumn::make('end_date')->date('d M Y'),
                TextColumn::make('return_date')->date('d M Y')->placeholder('—'),
            ])
            ->defaultSort('start_date', 'desc')
            ->filters([])
            ->recordActions([
                DeleteBulkAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
