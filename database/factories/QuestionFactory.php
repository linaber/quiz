<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'question_text' => fake()->sentence() . '?',
            'content_type' => 'text',
            'answer_type' => 'options',
            'options' => json_encode([
                ['text' => fake()->word(), 'is_correct' => false],
                ['text' => fake()->word(), 'is_correct' => true]
            ]),
            'hint' => fake()->boolean(70) ? fake()->sentence() : null,
            'difficulty_rating' => fake()->randomFloat(1, 1, 5)
        ];
    }
}
