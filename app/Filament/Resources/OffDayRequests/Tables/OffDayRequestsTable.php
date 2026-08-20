<?php

namespace App\Filament\Resources\OffDayRequests\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OffDayRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nurse.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('requested_date')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('rosterPeriod.period_start')
                    ->label('Roster Period')
                    ->formatStateUsing(fn ($record) => $record->rosterPeriod->period_start->format('d M').' – '.
                        $record->rosterPeriod->period_end->format('d M Y')
                    ),
                TextColumn::make('written_request_date')
                    ->date('d M Y')
                    ->placeholder('—'),
                TextColumn::make('reason')
                    ->limit(40)
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'granted' => 'success',
                        'denied' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('requested_date', 'desc')
            ->filters([])
            ->recordActions([
                Action::make('updateStatus')
                    ->label('Update Status')
                    ->icon('heroicon-o-pencil-square')
                    ->form([
                        Select::make('status')
                            ->options(['requested' => 'Requested', 'granted' => 'Granted', 'denied' => 'Denied'])
                            ->required(),
                    ])
                    ->fillForm(fn ($record) => ['status' => $record->status])
                    ->action(fn ($record, array $data) => $record->update($data)),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
