<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DesactivarSemestresExpirados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:desactivar-semestres-expirados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::connection('mysql_segunda')->table('semestre_academico')
            ->where('estado', 1)
            ->whereDate('fecha_fin', '<=', Carbon::now())
            ->update(['estado' => 0]);
        $this->info('Semestre desactivado por fecha limite');
        \Log::info('Semestres desactivados automáticamente el ' . now());
    }
}
