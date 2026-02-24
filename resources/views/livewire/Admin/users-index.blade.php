<!-- resources/views/livewire/admin/users-index.blade.php -->
<div>
    <div class="card">
        <div class="card-header">
            <input wire:model.live="search" class="form-control" placeholder="Ingrese Nombre o DNI">
        </div>

        @if ($users->count())
            <div class="card-body" wire:poll.1000ms>
                {{--  --}}
                <div class="table-responsive shadow-lg">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="bg-info"><i class="fas fa-th-large"></i></th>
                                <th class="bg-info">N° DNI</th>
                                <th class="bg-info">NOMBRES</th>
                                <th class="bg-info">N° CELULAR</th>
                                <th class="bg-info">EMAIL</th>
                                <th class="bg-info">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td><i class="fas fa-sign-in-alt"></i></td>
                                    <td>{{ $user->dni }}</td>
                                    <td>{{ $user->nombre ?? 'vacio' }}</td>
                                    <td>{{ $user->num_celualr ?? 'vacio' }}</td>
                                    <td>
                                        @if ($user->correo)
                                            {{ $user->correo }}
                                        @else
                                            <div class="p-1"><span style="color: rgb(186, 106, 9)">Sin correo registrado</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td width="10px" style="display: flex; align-items: center; gap: 5px;">
                                        <a class="btn btn-primary" title="Editar" href="{{ route('admin.users.edit', $user->id) }}">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <button wire:click="confirmarEliminacion({{ $user->id }})"
                                            class="btn btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="card-footer">
                {{ $users->links() }}
            </div>
        @else
            <div class="container mt-2">
                <div class="alert alert-light" role="alert">
                    No hay registros.
                </div>
            </div>
        @endif
    </div>
</div>
