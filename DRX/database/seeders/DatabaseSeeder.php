<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ServiceCatalogSeeder::class);
        $this->call(ZodHostContentSeeder::class);
    }
}
