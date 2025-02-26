<?php

namespace Database\Seeders;

use App\Models\Municipality;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class MunicipalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = public_path('municipalities.json');
        $fileData = File::get($filePath);
        $officessData = json_decode(json: $fileData, associative: true);
        
        foreach ($officessData as $od) {
            Municipality::create($od);
        }

    }
}
