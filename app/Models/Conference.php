<?php

namespace App\Models;

use App\Enums\Region;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conference extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'venue_id' => 'integer',
        'region' => Region::class,
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    public static function getForm(): array
    {
        return [
            Tabs::make('tabs')
                ->columnSpanFull()
                ->tabs([
                    Tab::make('Conference Details')
                        ->schema([
                            TextInput::make('name')
                                ->columnSpanFull()
                                ->label('Conference')
                                ->required()
                                ->maxLength(60),
                            MarkdownEditor::make('description')
                                ->columnSpanFull()
                                ->required(),
                            DateTimePicker::make('start_date')
                                ->native(false)
                                ->required(),
                            DateTimePicker::make('end_date')
                                ->native(false)
                                ->required(),
                            Fieldset::make('Status')
                                ->columns(1)
                                ->schema([
                                    Select::make('status')
                                        ->required()
                                        ->options([
                                            'draft' => 'Draft',
                                            'scheduled' => 'Scheduled',
                                            'published' => 'Published',
                                            'cancelled' => 'Cancelled',
                                        ]),
                                    Toggle::make('is_published')
                                        ->default(false)
                                        ->required(),
                                ]),
                        ]),
                    Tab::make('Location')
                        ->schema([
                            Select::make('region')
                                ->live()
                                ->enum(Region::class)
                                ->options(Region::class),
                            Select::make('venue_id')
                                ->searchable()
                                ->preload()
                                ->editOptionForm(Venue::getForm())
                                ->createOptionForm(Venue::getForm())
                                ->relationship(
                                    'venue',
                                    'name',
                                    modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('region', $get('region'))
                                ),
                        ]),
                ]),
        ];
    }
}
