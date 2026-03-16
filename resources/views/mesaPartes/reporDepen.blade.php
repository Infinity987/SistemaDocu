@extends('adminlte::page')

@section('title', "$rol->nombre_dependencia")

@section('content_header')
    @can('documentario.reporDepen.index')
        <div class="callout callout-danger mb-0">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1><i class="fas fa-chart-pie"></i>
                                - Reporte dependencias</h1>
                            </h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item active">inicio</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @endcan
@stop

@section('content')
    @can('documentario.reporDepen.index')
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-primary card-outline collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="far fa-chart-bar"></i>
                                Cantidad de documentos emitidos por dependencia
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="barChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-primary card-outline collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="far fa-chart-bar"></i>
                                Cantidad de documentos que ha ENVIADO cada dependencia y en qué estado se encuentra cada uno
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="barChart_cant_doc_envi" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-primary card-outline collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="far fa-chart-bar"></i>
                                Cantidad de documentos que ha RECIBIDO cada dependencia y en qué estado se encuentra cada uno
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="barChart_cant_doc_reci" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-primary card-outline collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="far fa-chart-bar"></i>
                                Tiempo promedio de atencion por dias laborables (lunes a viernes)
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="dias_promeAten_gra" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-primary card-outline collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="far fa-chart-bar"></i>
                                Tasa de cumplimiento de plazos (SLA) (Movimientos dentro del SLA / Total de movimientos) * 100
                            </h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="form-sla" action="{{ route('documentario.chart.line.tasa') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="fecha_inicio">Fecha de Inicio:</label>
                                    <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control">
                                    <small class="text-danger" id="fecha_inicio_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="fecha_fin">Fecha de Fin:</label>
                                    <input type="date" id="fecha_fin" name="fecha_fin" class="form-control">
                                    <small class="text-danger" id="fecha_fin_error"></small>
                                </div>
                                <button type="submit" class="btn btn-primary">Ver SLA</button>
                            </form>
                            <canvas id="gra_sla" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@stop
{{-- @vite(['resources/js/app.js']) --}}

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" />
    @livewireStyles
@stop

@section('js')
    <script>
        window.dependenciaId = {{ $id_depen }}; // Esto se usa dentro de app.js
    </script>
    @vite('resources/js/app.js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-Knob/1.2.13/jquery.knob.min.js"></script>
    <script src="{{ asset('chart.js/chart.bundle.min.js') }}"></script>
    @livewireScripts
    <script>
        $(function() {
            $('.knob').knob();
        });
    </script>

    <script>
        let dependenciaId = {{ $id_depen }};
        document.addEventListener("DOMContentLoaded", function() {
            Echo.private('dependencia.' + dependenciaId)
                .listen('.DocumentoRecibido', (e) => {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                            var audio = new Audio('{{ asset('sound/noti.mp3') }}'); // Ruta sonido
                            audio.play();
                        },
                        didClose: () => {
                            $('#badge-alerts').text(e.cont_estados[0].cont_estado == 0 ? '' : e
                                .cont_estados[0]
                                .cont_estado);
                        }
                    });

                    Toast.fire({
                        icon: "success",
                        title: "Nuevo documento recibido"
                    });
                });
        });

        document.addEventListener("DOMContentLoaded", function() {
            Echo.private('dependencia.' + dependenciaId)
                .listen('.noEditarDocumento', (e) => {
                    $('#datatablesSimple').DataTable().ajax.reload();
                });
        });

        document.addEventListener("DOMContentLoaded", function() {
            Echo.private('dependencia.' + dependenciaId)
                .listen('.editarDocumento', (e) => {
                    $('#badge-alerts').text(e.cont_estados[0].cont_estado == 0 ? '' : e.cont_estados[0]
                        .cont_estado);
                });
        });
    </script>

    <script>
        $(document).ready(function() {

        })
    </script>

    <script>
        //Cant de docus emitidos por dependencia
        const depenBarChar = @json($depenBarChar);
        const cantBarChar = @json($cantBarChar);
        const colores = [
            '#007bff', '#28a745', '#ffc107', '#dc3545',
            '#17a2b8', '#fd7e14', '#20c997', '#6610f2', '#e83e8c',
            '#007bff', '#28a745', '#dc3545', '#6f42c1',
            '#17a2b8', '#fd7e14', '#20c997', '#6610f2', '#e83e8c',
        ]
        const ctx = document.getElementById('barChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: depenBarChar,
                datasets: [{
                    label: 'Cantidad de documentos emitidos por dependencia',
                    data: cantBarChar,
                    backgroundColor: colores.slice(0, depenBarChar.length),
                    borderColor: '#ffffff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const valor = context.formattedValue;
                                const categoria = context.label;
                                return `${categoria}: ${valor} documento${valor !== '1' ? 's' : ''}`;
                            }
                        }
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        //Cant de que ha enviado cada depen y en qué estado se encuentra cada uno
        const cantDepenEnv_est1 = @json($cantDepenEnv_est1);
        const cantDepenEnv_est2 = @json($cantDepenEnv_est2);
        const cantDepenEnv_est3 = @json($cantDepenEnv_est3);

        const ctx_barChart_cant_doc_envi = document.getElementById('barChart_cant_doc_envi').getContext('2d');
        new Chart(ctx_barChart_cant_doc_envi, {
            type: 'bar',
            data: {
                labels: depenBarChar,
                datasets: [{
                        label: 'Recibidos',
                        data: cantDepenEnv_est1,
                        backgroundColor: '#007bff'
                    },
                    {
                        label: 'Pendientes',
                        data: cantDepenEnv_est2,
                        backgroundColor: '#ffc107'
                    },
                    {
                        label: 'Atendidos',
                        data: cantDepenEnv_est3,
                        backgroundColor: '#dc3545'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const valor = context.formattedValue;
                                const categoria = context.label;
                                return `${categoria}: ${valor} documento${valor !== '1' ? 's' : ''}`;
                            }
                        }
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        //Cant de que ha recibido cada depen y en qué estado se encuentra cada uno

        const cantDepenReci_est1 = @json($cantDepenReci_est1);
        const cantDepenReci_est2 = @json($cantDepenReci_est2);
        const cantDepenReci_est3 = @json($cantDepenReci_est3);
        new Chart(barChart_cant_doc_reci, {
            type: 'bar',
            data: {
                labels: depenBarChar,
                datasets: [{
                        label: 'Recibidos',
                        data: cantDepenReci_est1,
                        backgroundColor: '#ffc107'
                    },
                    {
                        label: 'Pendientes',
                        data: cantDepenReci_est2,
                        backgroundColor: '#0dcaf0'
                    },
                    {
                        label: 'Atendidos',
                        data: cantDepenReci_est3,
                        backgroundColor: '#198754'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const valor = context.formattedValue;
                                const categoria = context.label;
                                return `${categoria}: ${valor} documento${valor !== '1' ? 's' : ''}`;
                            }
                        }
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        //tiempo prome de aten por dias dias_promeAten
        const dias_promeAten = @json($dias_promeAten);
        new Chart(dias_promeAten_gra, {
            type: 'horizontalBar',
            data: {
                labels: depenBarChar,
                datasets: [{
                    label: 'Días promedio',
                    data: dias_promeAten,
                    backgroundColor: colores.slice(0, depenBarChar.length)
                }]
            },
            options: {
                responsive: true,
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            const label = data.labels[tooltipItem.index];
                            const valor = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                            return `${label}: ${Number(valor).toFixed(2)} días`;
                        }
                    }
                },
                legend: {
                    display: false
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) {
                                return Number(value).toFixed(1) + ' días';
                            }
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Días'
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }]
                }
            }
        });

        //sla
    </script>
    <script>
        let slaChart;

        $(document).ready(function() {
            $('#form-sla').submit(function(event) {
                console.log('se envia el modal');
                event.preventDefault();

                // Limpiar errores
                $('#fecha_inicio_error').text('');
                $('#fecha_fin_error').text('');

                let formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status === 'success') {
                            const nombre_dependencia = response.nombre_dependencia;
                            const total_movimientos = response.total_movimientos;
                            const movimientos_dentro_sla = response.movimientos_dentro_sla;
                            const tasa_cumplimiento_sla = response.tasa_cumplimiento_sla;

                            const ctx = document.getElementById('gra_sla').getContext('2d');

                            const tasas = response.tasa_cumplimiento_sla;

                            const backgroundColors = tasas.map(tasa => {
                                if (tasa <= 50)
                                    return 'rgba(255, 99, 132, 0.6)'; // Rojo
                                else if (tasa <= 80)
                                    return 'rgba(255, 206, 86, 0.6)'; // Amarillo
                                else return 'rgba(75, 192, 192, 0.6)'; // Verde
                            });

                            const borderColors = tasas.map(tasa => {
                                if (tasa <= 50) return 'rgba(255, 99, 132, 1)';
                                else if (tasa <= 80) return 'rgba(255, 206, 86, 1)';
                                else return 'rgba(75, 192, 192, 1)';
                            });

                            ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);

                            window.gra_sla = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: nombre_dependencia,
                                    datasets: [{
                                            label: 'Total movimiento',
                                            data: total_movimientos,
                                            backgroundColor: '#007bff',
                                            borderColor: 'rgba(54, 162, 235, 1)',
                                            borderWidth: 1
                                        },
                                        {
                                            label: 'Movimientos dentro de la sla',
                                            data: movimientos_dentro_sla,
                                            backgroundColor: '#ffc107',
                                            borderColor: 'rgba(54, 162, 235, 1)',
                                            borderWidth: 1
                                        },
                                        {
                                            label: 'Tasa cumplimiento sla (%)',
                                            data: tasa_cumplimiento_sla,
                                            backgroundColor: backgroundColors,
                                            borderColor: borderColors,
                                            borderWidth: 1
                                        },
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'Días hábiles'
                                            }
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        title: {
                                            display: true,
                                            text: 'Cumplimiento SLA por Dependencia'
                                        }
                                    }
                                }
                            });


                            ///////////////////////////////////////
                        }
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.errors;
                        if (errors) {
                            if (errors.fecha_inicio) {
                                $('#fecha_inicio_error').text(errors.fecha_inicio[0]);
                            }
                            if (errors.fecha_fin) {
                                $('#fecha_fin_error').text(errors.fecha_fin[0]);
                            }
                        }
                    }
                });
            });
        });
    </script>
@stop
