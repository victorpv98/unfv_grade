@php
    $currentTeacher = $teacher ?? null;
    $currentUser = $currentTeacher?->user;

    $formSchools = $schools ?? \App\Models\School::orderBy('name')->get();

    $nameValue = old('name', $currentUser?->name ?? '');
    $emailValue = old('email', $currentUser?->email ?? '');
    $schoolValue = (string) old('school_id', $currentUser?->school_id ?? '');
    $specialtyValue = old('specialty', $currentTeacher?->specialty ?? '');
    $statusValue = old('status', $currentUser?->status ?? 'active');
@endphp

<form action="{{ isset($teacher) ? route('admin.teachers.update', $teacher) : route('admin.teachers.store') }}"
      method="POST"
      class="row g-3"
      novalidate>
    @csrf
    @if(isset($teacher))
        @method('PUT')
    @endif

    <div class="col-12">
        <h6 class="text-uppercase text-muted fw-semibold mb-0">Información del docente</h6>
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
            <div class="invalid-feedback">{{ $message }}</div>
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
               placeholder="docente@unfv.edu.pe"
               maxlength="120"
               required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
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
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="specialty" class="form-label fw-semibold text-secondary">
            Especialidad
        </label>
        <input type="text"
               id="specialty"
               name="specialty"
               class="form-control @error('specialty') is-invalid @enderror"
               value="{{ $specialtyValue }}"
               placeholder="Área de especialización"
               maxlength="100">
        @error('specialty')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="password" class="form-label fw-semibold text-secondary">
            Contraseña inicial
        </label>
        <input type="password"
               id="password"
               name="password"
               class="form-control @error('password') is-invalid @enderror"
               placeholder="Opcional (por defecto: 123456)">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @if(!old('password') && isset($teacher))
            <div class="form-text">Deje en blanco para mantener la contraseña actual.</div>
        @endif
    </div>

    <div class="col-md-6">
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
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 d-flex justify-content-between align-items-center pt-3">
        <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i>
            {{ isset($teacher) ? 'Cancelar' : 'Volver' }}
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-save me-2"></i> Guardar docente
        </button>
    </div>
</form>
