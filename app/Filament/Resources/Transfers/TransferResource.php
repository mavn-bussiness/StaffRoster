<?php

namespace App\Filament\Resources\Transfers;

use App\Filament\Resources\Transfers\Pages\CreateTransfer;
use App\Filament\Resources\Transfers\Pages\ListTransfers;
use App\Filament\Resources\Transfers\Schemas\TransferForm;
use App\Models\Transfer;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransferResource extends Resource
{
    protected static ?string $model = Transfer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string|\UnitEnum|null $navigationGroup = 'Unit Management';

    protected static ?string $navigationLabel = 'Transfers';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['in_charge', 'admin', 'head_nursing']);
    }

    public static function canCreate(): bool
    {
        return in_array(auth()->user()?->role, ['in_charge', 'admin']);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery()->with(['nurse', 'fromUnit', 'toUnit'])->latest();

        if ($user->role === 'in_charge') {
            $query->whereHas('nurse', fn ($q) => $q->where('current_unit_id', $user->unit_id))
                ->orWhere(fn ($q) => $q->where('status', Transfer::STATUS_PENDING)
                    ->whereHas('nurse', fn ($q2) => $q2->where('home_unit_id', $user->unit_id)));
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return TransferForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        $isHN = auth()->user()?->role === 'head_nursing';

        return $table
            ->columns([
                TextColumn::make('nurse.name')->sortable()->searchable(),
                TextColumn::make('fromUnit.name')->label('From'),
                TextColumn::make('toUnit.name')->label('To'),
                TextColumn::make('start_date')->date('d M Y'),
                TextColumn::make('end_date')->date('d M Y')->placeholder('—'),
                TextColumn::make('reason')->placeholder('—')->limit(40),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        Transfer::STATUS_APPROVED => 'success',
                        Transfer::STATUS_REJECTED => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('approvedBy.name')->label('Actioned By')->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->recordActions(array_filter([
                in_array(auth()->user()?->role, ['head_nursing', 'admin'])
                    ? Action::make('approve')
                        ->label('Approve')
                        ->color('success')
                        ->icon('heroicon-o-check')
                        ->visible(fn (Transfer $record) => $record->status === Transfer::STATUS_PENDING)
                        ->requiresConfirmation()
                        ->action(function (Transfer $record) {
                            $record->update([
                                'status' => Transfer::STATUS_APPROVED,
                                'approved_by' => auth()->id(),
                                'approved_at' => now(),
                            ]);
                            $record->nurse->update(['current_unit_id' => $record->to_unit_id]);
                        })
                    : null,
                in_array(auth()->user()?->role, ['head_nursing', 'admin'])
                    ? Action::make('reject')
                        ->label('Reject')
                        ->color('danger')
                        ->icon('heroicon-o-x-mark')
                        ->visible(fn (Transfer $record) => $record->status === Transfer::STATUS_PENDING)
                        ->requiresConfirmation()
                        ->action(fn (Transfer $record) => $record->update([
                            'status' => Transfer::STATUS_REJECTED,
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]))
                    : null,
                in_array(auth()->user()?->role, ['admin'])
                    ? Action::make('rollbackTransfer')
                        ->label('Rollback to Pending')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('danger')
                        ->visible(fn (Transfer $record) => in_array($record->status, [Transfer::STATUS_APPROVED, Transfer::STATUS_REJECTED]))
                        ->requiresConfirmation()
                        ->action(fn (Transfer $record) => $record->update([
                            'status' => Transfer::STATUS_PENDING,
                            'approved_by' => null,
                            'approved_at' => null,
                        ]))
                    : null,
            ]))
            ->toolbarActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransfers::route('/'),
            'create' => CreateTransfer::route('/create'),
        ];
    }
}
