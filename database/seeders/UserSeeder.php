<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

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
            'nombre' => 'Administrador SISTEMA',
            'num_celualr' => '000000000',
            'correo' => 'administrador@admin.com',
            'id_users' => $id_admin->id,
        ]);

        $fechaHoraPeru = Carbon::now('America/Lima');
        $fechayhora = $fechaHoraPeru->toDateTimeString();

        DB::connection('mysql_documentario')->table('dependencia_user')->insert([
            'user_id' => $id_admin->id,
            'dependencia_id' => 1,
            'created_at' => $fechayhora,
            'estado' => 1
        ]);
    }
}
