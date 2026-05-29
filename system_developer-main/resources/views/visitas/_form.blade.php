<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label for="dia" class="mb-1 block text-sm font-medium text-slate-700">Dia</label>
        <input type="date" id="dia" name="dia" value="{{ old('dia', $visita->dia ? $visita->dia->format('Y-m-d') : now()->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        @error('dia')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="hora_estimada" class="mb-1 block text-sm font-medium text-slate-700">Hora estimada</label>
        <input type="time" id="hora_estimada" name="hora_estimada" value="{{ old('hora_estimada', $visita->hora_estimada ? substr((string) $visita->hora_estimada, 0, 5) : '') }}" step="60" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        @error('hora_estimada')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div>
    <label for="direccion" class="mb-1 block text-sm font-medium text-slate-700">Direccion</label>
    <input type="text" id="direccion" name="direccion" value="{{ old('direccion', $visita->direccion) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
    @error('direccion')
        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="detalle" class="mb-1 block text-sm font-medium text-slate-700">Detalle</label>
    <textarea id="detalle" name="detalle" rows="3" placeholder="Motivo de la visita, notas, etc." class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">{{ old('detalle', $visita->detalle) }}</textarea>
    @error('detalle')
        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="estado" class="mb-1 block text-sm font-medium text-slate-700">Estado</label>
    <select id="estado" name="estado" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        @foreach (['en proceso' => 'En proceso', 'cancelada' => 'Cancelada', 'culminada' => 'Culminada'] as $valor => $etiqueta)
            <option value="{{ $valor }}" @selected(old('estado', $visita->estado) === $valor)>{{ $etiqueta }}</option>
        @endforeach
    </select>
    @error('estado')
        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
    @enderror
</div>
