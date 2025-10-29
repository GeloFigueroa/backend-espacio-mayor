<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cupon;

class CuponesSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/seeders/data/codigos_starbucks.json'); // colócalo ahí
        $items = json_decode(file_get_contents($path), true);

        foreach ($items as $row) {
            Cupon::firstOrCreate(
                ['codigo' => $row['codigo']],
                [
                    'descripcion' => $row['descripcion'] ?? null,
                    'lote'        => $row['lote'] ?? null,
                    'usado'       => false,
                ]
            );
        }
    }
}
