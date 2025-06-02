<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'book_id' => null,
            'user_id' => null,
            'content' => $this->faker->paragraph(),
            'approved' => false,
        ];
    }
}
