<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cupon;

class CuponesSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/seeders/data/codigos_starbucks.json');
        $data = json_decode(file_get_contents($path), true);

        foreach ($data as $item) {
            Cupon::firstOrCreate(
                ['codigo' => $item['codigo']],
                [
                    'descripcion' => $item['descripcion'] ?? null,
                    'lote' => 'Inicial',
                    'usado' => false,
                ]
            );
        }

        $count = Cupon::count();
        $this->command->info("✅ Se cargaron $count cupones de Starbucks correctamente.");
    }
}
