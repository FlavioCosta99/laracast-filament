<?php

namespace Database\Factories;

use App\Models\Attendee;
use App\Models\Conference;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendeeFactory extends Factory
{
    protected $model = Attendee::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'ticket_cost' => $this->faker->numberBetween(0, 1000),
            'is_paid' => true,
            'conference_id' => Conference::factory(),
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now')
        ];
    }

    public function forConference(int|Conference $conference): self
    {
        if ($conference instanceof Conference) {
            $conference = $conference->id;
        }

        return $this->state([
            'conference_id' => $conference,
        ]);
    }
}
