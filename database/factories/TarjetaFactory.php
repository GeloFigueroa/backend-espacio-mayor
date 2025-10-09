<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tarjeta>
 */
class TarjetaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
{
    return [
        'titulo' => $this->faker->sentence(),
        'fecha_expiracion' => now()->addDay(),
        'tipo_contenido' => 'basico',
        'diseno_tarjeta' => 'basica',
        // demás campos obligatorios
    ];
}

}
