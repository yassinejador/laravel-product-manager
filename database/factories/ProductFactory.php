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
        // Skip image generation if GD extension is not available
        if (!extension_loaded('gd')) {
            return null;
        }

        $imageName = 'products/' . $this->faker->uuid() . '.jpg';
        $fakeImage = File::fake()->image($imageName, 640, 480);
        Storage::disk('public')->put($imageName, $fakeImage->getContent());

        return $imageName;
    }
}
