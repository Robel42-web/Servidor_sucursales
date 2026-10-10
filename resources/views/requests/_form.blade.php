@if ($errors->any())
    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
        <p class="font-semibold">Revisa los siguientes campos:</p>
        <ul class="mt-2 list-inside list-disc">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <label for="request_type" class="mb-2 block text-sm font-semibold">Tipo de solicitud *</label>
        <input id="request_type" name="request_type" type="text" maxlength="100" required
               value="{{ old('request_type', $solicitud->request_type ?? '') }}"
               class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
        @error('request_type')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="status" class="mb-2 block text-sm font-semibold">Estado *</label>
        <select id="status" name="status" required
                class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
            <option value="pending" @selected(old('status', $solicitud->status ?? 'pending') === 'pending')>Pendiente</option>
            <option value="approved" @selected(old('status', $solicitud->status ?? '') === 'approved')>Aprobada</option>
            <option value="rejected" @selected(old('status', $solicitud->status ?? '') === 'rejected')>Rechazada</option>
            <option value="completed" @selected(old('status', $solicitud->status ?? '') === 'completed')>Completada</option>
        </select>
        @error('status')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="description" class="mb-2 block text-sm font-semibold">Descripción</label>
        <textarea id="description" name="description" rows="4"
                  class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">{{ old('description', $solicitud->description ?? '') }}</textarea>
        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="employee_id" class="mb-2 block text-sm font-semibold">Empleado relacionado</label>
        <select id="employee_id" name="employee_id"
                class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
            <option value="">Sin asignar</option>
            @foreach ($empleados as $empleado)
                <option value="{{ $empleado->id }}"
                    @selected((string) old('employee_id', $solicitud->employee_id ?? '') === (string) $empleado->id)>
                    {{ $empleado->full_name }}
                </option>
            @endforeach
        </select>
        @error('employee_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="customer_service_id" class="mb-2 block text-sm font-semibold">Atención relacionada</label>
        <select id="customer_service_id" name="customer_service_id"
                class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
            <option value="">Sin atención relacionada</option>
            @foreach ($atenciones as $atencion)
                <option value="{{ $atencion->id }}"
                    @selected((string) old('customer_service_id', $solicitud->customer_service_id ?? '') === (string) $atencion->id)>
                    {{ $atencion->service_type }} — {{ \Illuminate\Support\Str::limit($atencion->description ?? '', 50) }}
                </option>
            @endforeach
        </select>
        @error('customer_service_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="central_reference_id" class="mb-2 block text-sm font-semibold">Referencia del sistema central</label>
        <input id="central_reference_id" name="central_reference_id" type="text"
               value="{{ old('central_reference_id', $solicitud->central_reference_id ?? '') }}"
               placeholder="UUID (opcional)"
               class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
        @error('central_reference_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="resolved_at" class="mb-2 block text-sm font-semibold">Fecha de resolución</label>
        <input id="resolved_at" name="resolved_at" type="datetime-local"
               value="{{ old('resolved_at', isset($solicitud) && $solicitud->resolved_at ? $solicitud->resolved_at->format('Y-m-d\TH:i') : '') }}"
               class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
        @error('resolved_at')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-8 flex flex-wrap gap-3">
    <button type="submit"
            class="rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white hover:bg-blue-700">
        {{ $boton }}
    </button>

    <a href="{{ route('requests.index') }}"
       class="rounded-xl border border-slate-300 px-5 py-3 font-semibold hover:bg-slate-50">
        Cancelar
    </a>
</div>
