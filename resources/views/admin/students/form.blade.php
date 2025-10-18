@php
    $currentStudent = $student ?? null;
    $currentUser = $currentStudent?->user;

    $formSchools = $schools ?? \App\Models\School::orderBy('name')->get();

    $nameValue = old('name', $currentUser?->name ?? '');
    $emailValue = old('email', $currentUser?->email ?? '');
    $documentTypeValue = old('document_type', $currentUser?->document_type ?? '');
    $documentNumberValue = old('document_number', $currentUser?->document_number ?? '');
    $schoolValue = (string) old('school_id', $currentUser?->school_id ?? '');
    $statusValue = old('status', $currentUser?->status ?? 'active');
    $codeValue = old('code', $currentStudent?->code ?? '');
    $enrollmentYearValue = old('enrollment_year', $currentStudent?->enrollment_year ?? now()->year);
@endphp

<form action="{{ isset($student) ? route('admin.students.update', $student) : route('admin.students.store') }}"
      method="POST"
      class="row g-3"
      novalidate>
    @csrf
    @if(isset($student))
        @method('PUT')
    @endif

    <div class="col-12">
        <h6 class="text-uppercase text-muted fw-semibold mb-0">Datos personales</h6>
        <hr class="mt-2 mb-3">
    </div>

    <div class="col-md-6">
        <label for="name" class="form-label fw-semibold text-secondary">
            Nombre completo <span class="text-danger">*</span>
        </label>
        <input type="text"
               id="name"
               name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ $nameValue }}"
               placeholder="Nombre y apellidos"
               maxlength="120"
               required>
        @error('name')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label fw-semibold text-secondary">
            Correo institucional <span class="text-danger">*</span>
        </label>
        <input type="email"
               id="email"
               name="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ $emailValue }}"
               placeholder="estudiante@unfv.edu.pe"
               maxlength="120"
               required>
        @error('email')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="document_type" class="form-label fw-semibold text-secondary">
            Tipo de documento
        </label>
        <select name="document_type"
                id="document_type"
                class="form-select @error('document_type') is-invalid @enderror">
            <option value="">Seleccione...</option>
            <option value="DNI" {{ $documentTypeValue === 'DNI' ? 'selected' : '' }}>DNI</option>
            <option value="CE" {{ $documentTypeValue === 'CE' ? 'selected' : '' }}>CE</option>
            <option value="PAS" {{ $documentTypeValue === 'PAS' ? 'selected' : '' }}>PAS</option>
        </select>
        @error('document_type')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="document_number" class="form-label fw-semibold text-secondary">
            N° Documento
        </label>
        <input type="text"
               id="document_number"
               name="document_number"
               class="form-control @error('document_number') is-invalid @enderror"
               value="{{ $documentNumberValue }}"
               maxlength="20"
               placeholder="Ingrese el número de documento">
        @error('document_number')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="school_id" class="form-label fw-semibold text-secondary">
            Escuela profesional <span class="text-danger">*</span>
        </label>
        <select name="school_id"
                id="school_id"
                class="form-select @error('school_id') is-invalid @enderror"
                required>
            <option value="">Seleccione una escuela</option>
            @foreach($formSchools as $school)
                <option value="{{ $school->id }}"
                    {{ $schoolValue === (string) $school->id ? 'selected' : '' }}>
                    {{ $school->name }}
                </option>
            @endforeach
        </select>
        @error('school_id')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="status" class="form-label fw-semibold text-secondary">
            Estado
        </label>
        <select name="status"
                id="status"
                class="form-select @error('status') is-invalid @enderror">
            <option value="active" {{ $statusValue === 'active' ? 'selected' : '' }}>Activo</option>
            <option value="suspended" {{ $statusValue === 'suspended' ? 'selected' : '' }}>Suspendido</option>
        </select>
        @error('status')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 mt-2">
        <h6 class="text-uppercase text-muted fw-semibold mb-0">Datos académicos</h6>
        <hr class="mt-2 mb-3">
    </div>

    <div class="col-md-4">
        <label for="code" class="form-label fw-semibold text-secondary">
            Código de matrícula <span class="text-danger">*</span>
        </label>
        <input type="text"
               id="code"
               name="code"
               class="form-control @error('code') is-invalid @enderror"
               value="{{ $codeValue }}"
               maxlength="20"
               placeholder="Ej: 202410123"
               required>
        @error('code')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="enrollment_year" class="form-label fw-semibold text-secondary">
            Año de ingreso <span class="text-danger">*</span>
        </label>
        <input type="number"
               id="enrollment_year"
               name="enrollment_year"
               class="form-control @error('enrollment_year') is-invalid @enderror"
               value="{{ $enrollmentYearValue }}"
               min="2000"
               max="{{ now()->year }}"
               required>
        @error('enrollment_year')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="password" class="form-label fw-semibold text-secondary">
            Contraseña inicial
        </label>
        <input type="password"
               id="password"
               name="password"
               class="form-control @error('password') is-invalid @enderror"
               placeholder="Opcional (por defecto: 123456)">
        @error('password')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
        @if(isset($student))
            <div class="form-text">Dejar en blanco para mantener la contraseña actual.</div>
        @endif
    </div>

    <div class="col-12 d-flex justify-content-between align-items-center pt-3">
        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i>
            {{ isset($student) ? 'Cancelar' : 'Volver' }}
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-save me-2"></i>
            Guardar estudiante
        </button>
    </div>
</form>
