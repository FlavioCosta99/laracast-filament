<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Speaker;
use App\Models\Talk;

class SpeakerFactory extends Factory
{
    protected $model = Speaker::class;

    public function definition(): array
    {
        $qualificationsCount = $this->faker->numberBetween(0, count(Speaker::QUALIFICATIONS));
        $qualifications = $this->faker->randomElements(array_keys(Speaker::QUALIFICATIONS), $qualificationsCount);

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'bio' => $this->faker->text(),
            'qualifications' => $qualifications,
            'twitter_handle' => $this->faker->word(),
        ];
    }

    public function withTalk(int $count = 1): self
    {
        return $this->has(Talk::factory()->count($count), 'talks');
    }
}
