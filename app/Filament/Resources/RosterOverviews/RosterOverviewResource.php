<?php

namespace App\Filament\Resources\RosterOverviews;

use App\Filament\Resources\RosterOverviews\Pages\ListRosterOverview;
use App\Filament\Resources\RosterPeriods\RosterPeriodResource;
use App\Models\RosterPeriod;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RosterOverviewResource extends Resource
{
    protected static ?string $model = RosterPeriod::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected static string|\UnitEnum|null $navigationGroup = 'Overview';

    protected static ?string $navigationLabel = 'All Rosters';

    protected static ?string $slug = 'roster-overview';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'area_manager', 'head_nursing']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('unit.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('period_start')
                    ->label('Period')
                    ->formatStateUsing(fn ($record) => $record->period_start->format('d M Y').' – '.$record->period_end->format('d M Y')
                    )
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($record) => $record->statusLabel())
                    ->color(fn ($record) => $record->statusColor()),
                TextColumn::make('agreed_at')->date('d M Y')->placeholder('—'),
                TextColumn::make('submitted_to_moh_at')->label('Submitted to MoH')->date('d M Y')->placeholder('—'),
            ])
            ->defaultSort('period_start', 'desc')
            ->filters([
                SelectFilter::make('unit_id')
                    ->label('Unit')
                    ->relationship('unit', 'name'),
            ])
            ->recordActions([
                Action::make('grid')
                    ->label('Open Grid')
                    ->icon('heroicon-o-table-cells')
                    ->url(fn ($record) => RosterPeriodResource::getUrl('grid', ['record' => $record])),
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('rosters.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRosterOverview::route('/'),
        ];
    }
}
