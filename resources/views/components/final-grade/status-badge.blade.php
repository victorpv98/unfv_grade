@props(['status', 'label' => null])

@php
    $rawStatus = strtoupper((string) $status);
    $aliases = [
        'A' => 'A',
        'APROBADO' => 'A',
        'APPROVED' => 'A',
        'D' => 'D',
        'DESAPROBADO' => 'D',
        'FAILED' => 'D',
        'S' => 'S',
        'SUSTITUTORIO' => 'S',
        'SUBSTITUTE' => 'S',
        'R' => 'R',
        'APLAZADO' => 'R',
        'MAKEUP' => 'R',
    ];
    $statusCode = $aliases[$rawStatus] ?? $rawStatus;
    $statusMap = [
        'A' => ['label' => 'Aprobado', 'class' => 'bg-success-subtle text-success'],
        'D' => ['label' => 'Desaprobado', 'class' => 'bg-danger-subtle text-danger'],
        'S' => ['label' => 'Sustitutorio', 'class' => 'bg-info-subtle text-info'],
        'R' => ['label' => 'Aplazado', 'class' => 'bg-warning-subtle text-warning'],
    ];
    $resolved = $statusMap[$statusCode] ?? ['label' => 'Sin registrar', 'class' => 'bg-secondary-subtle text-secondary'];
@endphp

<span {{ $attributes->class(['badge', $resolved['class']]) }}>
    {{ $label ?? $resolved['label'] }}
</span>
