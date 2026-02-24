<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $id_admin = User::create([
            'dni' => 10101010,
            'password' => bcrypt('12345678')
        ])->assignRole('admin');

        DB::connection('mysql_segunda')->table('userProfile')->insert([
            'nombre' => 'Administrador',
            'num_celualr' => '000000000',
            'correo' => 'administrador@admin.com',
            'id_users' => $id_admin->id,
        ]);

        User::factory(0)->create();
    }
}
