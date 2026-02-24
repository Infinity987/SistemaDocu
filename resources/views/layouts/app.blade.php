@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')


    <h1>Dashboard</h1>
@stop

@section('content')
    <p>Welcome to this beautiful admin panel.</p>
@stop

@section('css')
<style>
    /* .main-sidebar { background-color: #a44a00 !important } */
</style>
    @livewireStyles
    <!-- Agregar Font Awesome desde CDN -->
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> --}}
@stop

@section('js')
    @livewireScripts
    {{-- <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.x.x/dist/alpine.min.js" defer></script> --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let url = window.location.href; // Obtiene la URL completa
            console.log('sssssssss')
            let menuItems = document.querySelectorAll(".nav-item a");

            menuItems.forEach(link => {
                if (url.includes(link.href)) {
                    link.closest('.nav-item').classList.add("active");
                }
            });
        });
    </script>
@stop
