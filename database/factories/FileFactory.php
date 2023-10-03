<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = File::class;

    public function definition()
    {
        return [
            'file_name' => $this->faker->word,
            'file_type' => $this->faker->mimeType,
            'file_extension' => $this->faker->fileExtension,
            'file_size' => $this->faker->numberBetween(1000, 1000000), // Random file size between 1KB and 1MB
            'file_description' => $this->faker->paragraph,
            'file_path' => $this->faker->url,
        ];
    }
}
