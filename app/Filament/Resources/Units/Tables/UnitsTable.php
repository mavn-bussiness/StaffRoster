<?php

namespace App\Filament\Resources\Units\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UnitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('code')
                    ->searchable(),
                IconColumn::make('has_evening_shift')
                    ->boolean(),
                IconColumn::make('has_night_shift')
                    ->boolean(),
                TextColumn::make('nurses_count')
                    ->counts('nurses')
                    ->label('Nurses'),
                TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Accounts'),
            ])
            ->defaultSort('name')
            ->filters([])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (Model $record, DeleteAction $action) {
                        if ($record->nurses()->exists() || $record->users()->exists()) {
                            $action->cancel();
                            $action->failureNotificationTitle('Cannot delete a unit that still has nurses or accounts assigned.');
                            $action->sendFailureNotification();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
