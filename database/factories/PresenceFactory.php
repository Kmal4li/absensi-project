<?php

namespace Database\Factories;

use App\Models\Presence;
use Illuminate\Database\Eloquent\Factories\Factory;

class PresenceFactory extends Factory
{
    protected $model = Presence::class;

    public function definition()
    {
        return [
            'attendance_id' => $this->faker->randomDigitNotNull(),
            'user_id' => $this->faker->randomDigitNotNull(),
            'presence_date' => $this->faker->date(),
            'presence_enter_time' => $this->faker->time(),
            'presence_out_time' => $this->faker->optional()->time(),
            'photo' => $this->faker->imageUrl(),
            'is_permission' => $this->faker->boolean(),
        ];
    }
}
