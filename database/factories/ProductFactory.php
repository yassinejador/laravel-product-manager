<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Testing\File;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'image' => $this->generateProductImage(),
        ];
    }

    private function generateProductImage(): ?string
    {
        // Create a plain text file
        $imageName = 'products/' . $this->faker->uuid() . '.txt';
        $fakeFile = File::fake()->create($imageName, 100);
        Storage::disk('public')->put($imageName, $fakeFile->getContent());

        return $imageName;
    }
}
