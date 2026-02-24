<div>
    <div class="col-sm-12 mb-3">
        <div class="form-group">
            <label for="carrera"><i class="fas fa-user-tie"></i> CARRERA:</label>
            <select id="carrera" name="carrera" class="form-control">
                <option value="">Seleccione una carrera</option>
                @foreach($carreras as $carrera)
                    <option value="{{ $carrera->idvacantes }}">{{ $carrera->nombre_de_carrera }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
