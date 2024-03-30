<?php

namespace App\Filament\Resources;

use App\Enums\TalkLength;
use App\Enums\TalkStatus;
use App\Filament\Resources\TalkResource\Pages;
use App\Filament\Resources\TalkResource\RelationManagers;
use App\Models\Talk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use SebastianBergmann\CodeCoverage\Report\Html\Colors;

class TalkResource extends Resource
{
    protected static ?string $model = Talk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('abstract')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('speaker_id')
                    ->relationship('speaker', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->persistFiltersInSession()
            ->filtersTriggerAction(fn ($action) => $action->button()->label('Filters'))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->description(fn (Talk $talk) => Str::of($talk->abstract)->limit(40)),
                Tables\Columns\ImageColumn::make('speaker.avatar')
                    ->label('Speaker Avatar')
                    ->defaultImageUrl(fn (Talk $talk) =>  'https://www.ui-avatars.com/api/?background=0D8AB&color=fff&name=' . urlencode($talk->speaker->name))
                    ->circular(),
                Tables\Columns\TextColumn::make('speaker.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('new_talk'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color(fn (TalkStatus $state) => $state->getColor()),
                Tables\Columns\IconColumn::make('length')
                    ->icon(fn ($state) => $state->getIcon())
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('new_talk'),
                Tables\Filters\SelectFilter::make('speaker')
                    ->relationship('speaker', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('has_avatar')
                    ->label('Show only speakers with avatars')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->whereHas('speaker', fn (Builder $query) => $query->whereNotNull('avatar')))
            ])
            ->actions([
                Tables\Actions\EditAction::make()->slideOver(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('approve')
                        ->visible(fn (Talk $talk) => $talk->status === TalkStatus::SUBMITTED)
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Talk $talk) => $talk->approve())
                        ->after(
                            fn () => Notification::make()
                                ->success()
                                ->title('Talk Approved')
                                ->body('The talk has been approved.')
                                ->send()
                        ),
                    Tables\Actions\Action::make('reject')
                        ->visible(fn (Talk $talk) => $talk->status === TalkStatus::SUBMITTED)
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation('Are you sure you want to reject this talk?')
                        ->action(fn (Talk $talk) => $talk->reject())
                        ->after(
                            fn () => Notification::make()
                                ->danger()
                                ->title('Talk rejected')
                                ->body('The talk has been rejected.')
                                ->send()
                        ),
                ]),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->action(function (Collection $records) {
                            $records->each->approve();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('Export')
                    ->tooltip('This will export all records visible in the table. Adjust the filters to export a subset of records.')
                    ->action(function ($livewire) {
                        // $livewire->getFilteredTableQuery()->count();
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTalks::route('/'),
            'create' => Pages\CreateTalk::route('/create'),
            // 'edit' => Pages\EditTalk::route('/{record}/edit'),
        ];
    }
}
