<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $team = Team::where('is_store', true)->first();

        if (! $team) {
            $this->command->warn('No store team found. Skipping category seeding.');

            return;
        }

        $names = ['Brinco', 'Colar'];

        foreach ($names as $name) {
            $team->categories()->firstOrCreate(['name' => $name]);
        }

        $this->command->info('Categories seeded.');
        $this->command->table(['Name'], array_map(fn ($n) => [$n], $names));
    }
}
