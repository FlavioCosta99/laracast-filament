<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AttendeeResource;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class TestWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.test-widget';

    public function callNotification(): Action
    {
        return Action::make('callNotification')
            ->button()
            ->color('warning')
            ->label('Send a notification')
            ->action(
                fn () => Notification::make()
                    ->warning()
                    ->title('You sent a notification')
                    ->body('This is a test')
                    ->duration(5000)
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('goToAttendees')
                            ->button()
                            ->color('primary')
                            ->url(AttendeeResource::getUrl('index'))
                    ])
                    ->send()
            );
    }
}
