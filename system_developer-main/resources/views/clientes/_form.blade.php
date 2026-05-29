@php
    $tipoDocOld = old('tipo_doc');
    $docDigits = preg_replace('/\D/', '', (string) old('dni_ruc', $cliente->dni_ruc ?? ''));
    if ($tipoDocOld !== 'ruc' && $tipoDocOld !== 'dni') {
        $tipoDocOld = strlen($docDigits) === 11 ? 'ruc' : 'dni';
    }
@endphp

<div
    class="grid grid-cols-1 gap-4"
    x-data="{
        docType: @js($tipoDocOld === 'ruc' ? 'ruc' : 'dni'),
        digitosDoc: @js($docDigits),
        consultingApi: false,
        consultFeedback: '',
        consultTone: '',
        csrfMeta() {
            const m = document.querySelector('meta[name=csrf-token]');
            return m ? m.getAttribute('content') : '';
        },
        maxLen() { return this.docType === 'ruc' ? 11 : 8 },
        truncate() {
            const el = this.$refs.docInput;
            if (! el) {
                return;
            }
            const cleaned = el.value.replace(/\D/g, '').slice(0, this.maxLen());
            el.value = cleaned;
            this.digitosDoc = cleaned;
            this.consultTone = '';
            this.consultFeedback = '';
        },
        setType(t) {
            if (this.docType === t) {
                return;
            }
            this.docType = t;
            this.$nextTick(() => this.truncate());
        },
        puedeConsultar() {
            const n = this.digitosDoc || '';
            return this.docType === 'ruc' ? n.length === 11 : n.length === 8;
        },
        aplicarCamposApi(campos) {
            ['nombre_completo', 'empresa', 'direccion'].forEach((clave) => {
                if (!(clave in campos) || campos[clave] == null || campos[clave] === '') {
                    return;
                }
                const el = this.$refs[clave];
                if (el) {
                    el.value = campos[clave];
                }
            });
        },
        async consultDocumentoApi() {
            const token = this.csrfMeta();
            if (! token) {
                this.consultTone = 'err';
                this.consultFeedback = 'Falta el token CSRF en la pagina. Recarga e intenta otra vez.';
                return;
            }

            const documento = (this.digitosDoc || '').replace(/\D/g, '');

            if (! this.puedeConsultar()) {
                this.consultTone = 'err';
                this.consultFeedback = this.docType === 'ruc'
                    ? 'Ingresa primero los 11 digitos del RUC.'
                    : 'Ingresa primero los 8 digitos del DNI.';
                return;
            }

            this.consultingApi = true;
            this.consultTone = '';
            this.consultFeedback = '';

            try {
                const res = await fetch(@js(route('clientes.consulta-documento')), {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        tipo_doc: this.docType,
                        documento: documento,
                    }),
                });

                const payload = await res.json().catch(() => ({}));

                if (! res.ok) {
                    this.consultTone = 'err';
                    this.consultFeedback = payload.message ?? 'No se pudo completar la consulta.';
                    return;
                }

                if (payload.campos) {
                    this.aplicarCamposApi(payload.campos);
                }

                this.consultTone = 'ok';
                this.consultFeedback = payload.info ?? 'Datos cargados desde ApiPeru.';
            } catch (e) {
                this.consultTone = 'err';
                this.consultFeedback = 'Error de red al consultar.';
            } finally {
                this.consultingApi = false;
            }
        }
    }">

    <div>
        <label for="dni_ruc" class="mb-1 block text-sm font-medium text-slate-700">Documento</label>
        <input type="hidden" name="tipo_doc" id="tipo_doc" x-bind:value="docType">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end">
            <div class="min-w-0 flex-1">
                <input
                    type="text"
                    id="dni_ruc"
                    name="dni_ruc"
                    x-ref="docInput"
                    autocomplete="off"
                    inputmode="numeric"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                    value="{{ old('dni_ruc', $cliente->dni_ruc) }}"
                    x-bind:maxlength="maxLen()"
                    x-init="$nextTick(() => truncate())"
                    @input="truncate()"
                >
            </div>
            <div class="flex flex-wrap gap-2 sm:shrink-0 sm:justify-end">
                <fieldset class="relative inline-grid shrink-0 grid-cols-2 gap-1 rounded-xl border border-slate-300 bg-slate-100 p-1 sm:inline-grid sm:h-[38px] sm:grid-cols-2">
                    <legend class="sr-only">Tipo de documento</legend>
                    <button
                        type="button"
                        @click.prevent="setType('dni')"
                        :aria-pressed="docType === 'dni'"
                        :class="docType === 'dni' ? 'bg-white text-indigo-700 shadow-sm ring-1 ring-slate-200/80' : 'text-slate-600 hover:bg-slate-50'"
                        class="rounded-lg px-3 py-1.5 text-sm font-semibold transition sm:flex sm:h-full sm:items-center sm:justify-center"
                    >
                        DNI
                    </button>
                    <button
                        type="button"
                        @click.prevent="setType('ruc')"
                        :aria-pressed="docType === 'ruc'"
                        :class="docType === 'ruc' ? 'bg-white text-indigo-700 shadow-sm ring-1 ring-slate-200/80' : 'text-slate-600 hover:bg-slate-50'"
                        class="rounded-lg px-3 py-1.5 text-sm font-semibold transition sm:flex sm:h-full sm:items-center sm:justify-center"
                    >
                        RUC
                    </button>
                </fieldset>
                <button
                    type="button"
                    @click.prevent="consultDocumentoApi()"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-800 bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-900 disabled:cursor-not-allowed disabled:opacity-60"
                    x-bind:disabled="consultingApi || ! puedeConsultar()"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5m-5 5v-12" />
                    </svg>
                    <span x-text="consultingApi ? 'Consultando...' : 'Consultar'"></span>
                </button>
            </div>
        </div>
        <p class="mt-1 text-xs text-slate-500 wrap-break-word" x-text="docType === 'ruc' ? 'Ingresa 11 digitos de RUC.' : 'Ingresa 8 digitos de DNI.'"></p>
        <p
            x-show="consultFeedback !== ''"
            x-cloak
            class="mt-1 wrap-break-word text-xs"
            :class="consultTone === 'ok' ? 'text-emerald-700' : consultTone === 'err' ? 'text-rose-700' : 'text-slate-600'"
            x-text="consultFeedback">
        </p>
        @error('dni_ruc')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="nombre_completo" class="mb-1 block text-sm font-medium text-slate-700">Nombre completo</label>
        <input type="text" id="nombre_completo" name="nombre_completo" x-ref="nombre_completo" value="{{ old('nombre_completo', $cliente->nombre_completo) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        @error('nombre_completo')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="nro_celular" class="mb-1 block text-sm font-medium text-slate-700">Nro de celular</label>
        <input type="text" id="nro_celular" name="nro_celular" value="{{ old('nro_celular', $cliente->nro_celular) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        @error('nro_celular')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="correo" class="mb-1 block text-sm font-medium text-slate-700">Correo (opcional)</label>
        <input type="email" id="correo" name="correo" value="{{ old('correo', $cliente->correo) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        @error('correo')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="empresa" class="mb-1 block text-sm font-medium text-slate-700">Empresa (opcional)</label>
        <input type="text" id="empresa" name="empresa" x-ref="empresa" value="{{ old('empresa', $cliente->empresa) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        @error('empresa')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="direccion" class="mb-1 block text-sm font-medium text-slate-700">Direccion</label>
        <input type="text" id="direccion" name="direccion" x-ref="direccion" value="{{ old('direccion', $cliente->direccion) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        @error('direccion')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>
</div>
