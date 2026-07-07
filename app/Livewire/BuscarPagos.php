<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class BuscarPagos extends Component
{
    public $dni = '';
    public $searchTupa = ''; 
    public $alumno = null;
    public $recibos = [];
    public $listaTupas = []; 
    public $id_dependencia; 

    public function mount($id_dependencia)
    {
        $this->id_dependencia = $id_dependencia;

        // 1. Consultamos a 'ver_pagos' qué conceptos de texto tiene permitidos esta oficina
        $conceptosPermitidos = DB::connection('mysql_pagos')->table('ver_pagos')
            ->where('id_dependencia', $this->id_dependencia)
            ->pluck('nombre_tupa')
            ->toArray();

        // 2. Cargamos el catálogo del datalist filtrando únicamente por esos nombres permitidos
        $this->listaTupas = DB::connection('mysql_pagos')->table('tupas')
            ->select('tupCod', 'tupDes')
            ->whereIn('tupDes', $conceptosPermitidos)
            ->orderBy('tupCod', 'asc')
            ->get()
            ->toArray();
    }

    public function updatedDni()
    {
        if (strlen($this->dni) < 8) {
            $this->alumno = null;
            $this->recibos = [];
            $this->searchTupa = ''; 
            return;
        }

        $this->alumno = DB::connection('mysql_pagos')->table('alumnos')
            ->where('aluDni', $this->dni)
            ->first();

        $this->obtenerRecibos();
    }

    public function updatedSearchTupa()
    {
        $this->obtenerRecibos();
    }

    public function obtenerRecibos()
    {
        if (!$this->alumno) {
            $this->recibos = [];
            return;
        }

        // 1. Volvemos a jalar los conceptos permitidos para asegurar la consulta de recibos
        $conceptosPermitidos = DB::connection('mysql_pagos')->table('ver_pagos')
            ->where('id_dependencia', $this->id_dependencia)
            ->pluck('nombre_tupa')
            ->toArray();

        $query = DB::connection('mysql_pagos')->table('recibos')
            ->join('tupas', 'recibos.tupa_tupId', '=', 'tupas.tupId')
            ->where('recibos.alumno_aluId', $this->alumno->aluId)
            ->whereIn('tupas.tupDes', $conceptosPermitidos); // Security Filter 🔐

        // Filtro manual por el buscador/selector del TUPA
        if (!empty($this->searchTupa)) {
            $query->where(function($q) {
                $q->where('tupas.tupCod', 'like', "%{$this->searchTupa}%")
                  ->orWhere('tupas.tupDes', 'like', "%{$this->searchTupa}%");
            });
        }

        $this->recibos = $query->select(
                'recibos.resId',
                'recibos.resCod',
                'recibos.resFec',
                'recibos.resMonUni',
                'recibos.resCan',
                'recibos.resObs',
                'tupas.tupCod',
                'tupas.tupDes',
                'tupas.tupMon'
            )
            ->orderBy('recibos.resFec', 'desc')
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.buscar-pagos');
    }
}