<?php

namespace App\Livewire;

use App\Models\Attendee;
use App\Models\Conference;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class ConferenceSignUpPage extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Conference $conference;
    public int $price = 500;

    public function mount()
    {
        $this->conference = Route::current()->parameters['conference'];
    }

    public function signUpAction(): Action
    {
        return Action::make('signup')
            ->slideOver()
            ->form([
                Placeholder::make('total_price')
                    ->hiddenLabel()
                    ->content(fn (Get $get) => '$' . count($get('attendees')) * $this->price),
                Repeater::make('attendees')
                    ->schema(Attendee::getForm())
            ])
            ->action(function (array $data) {
                collect($data['attendees'])->each(function ($attendee) {
                    Attendee::create([
                        'conference_id' => $this->conference->id,
                        'name' => $attendee['name'],
                        'email' => $attendee['email'],
                        'ticket_cost' => $this->price,
                        'is_paid' => true,
                    ]);
                });
            })->after(function () {
                Notification::make()
                    ->success()
                    ->title('Success!')
                    ->body('Attendees signed up successfully!')
                    ->send();
            });
    }

    public function render()
    {
        return view('livewire.conference-sign-up-page');
    }
}
