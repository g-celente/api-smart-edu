<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'nome' => 'PUCPR',
            'email' => 'puc@pucpr.edu.br',
            'senha' => 123456,
            'type_id' => 3
        ]);

        User::create([
            'nome' => 'Guilherme',
            'email' => 'guilherme.celente@outlook.com',
            'senha' => 123456,
            'type_id' => 1,
            'instituicao_id' => 1
        ]);
    }
}
