@php
    /** @var \App\Models\Course $course */
    /** @var \Illuminate\Support\Collection|\App\Models\CourseStudent[] $students */
    /** @var \App\Models\CourseActa $acta */
    $teacherUser = optional($teacherAssignment?->teacher)->user;
    $logoPath = public_path('images/logo_unfv.png');
    $logoDataUrl = null;
    $timezone = config('app.timezone');

    if (is_file($logoPath)) {
        $logoDataUrl = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 35px 30px 60px;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            color: #1f2933;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .header .identity {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header img {
            height: 60px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metadata {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .metadata td {
            padding: 6px 8px;
            border: 1px solid #d1d5db;
        }

        .metadata td.label {
            width: 22%;
            font-weight: bold;
            background-color: #f3f4f6;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .students-table thead {
            background-color: #111827;
            color: #ffffff;
        }

        .students-table th,
        .students-table td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
        }

        .students-table th.number,
        .students-table td.number {
            text-align: center;
            width: 40px;
        }

        .students-table th.score,
        .students-table td.score {
            text-align: center;
            width: 90px;
        }

        .students-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .signature-block {
            margin-top: 40px;
            display: flex;
            justify-content: center;
            align-items: flex-end;
        }

        .signature {
            width: 50%;
            text-align: center;
            border-top: 1px solid #1f2933;
            padding-top: 10px;
            position: relative;
            height: 120px;
            margin: 0 auto;
        }

        .signature img {
            max-width: 100%;
            max-height: 100px;
            object-fit: contain;
            margin-bottom: 8px;
        }

        .signature-placeholder {
            height: 100px;
            border: 1px dashed #9ca3af;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            font-style: italic;
            margin-bottom: 12px;
        }

        .signature-role {
            font-size: 10px;
            color: #6b7280;
            margin-top: 8px;
        }

        .footer {
            position: fixed;
            bottom: 15px;
            left: 0;
            right: 0;
            font-size: 10px;
            color: #6b7280;
            text-align: center;
        }

        .stats {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }

        .stats td {
            border: 1px solid #d1d5db;
            padding: 6px 10px;
            background-color: #f9fafb;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="identity">
            @if($logoDataUrl)
                <img src="{{ $logoDataUrl }}" alt="UNFV">
            @endif
            <div>
                <h1>Acta de Evaluación</h1>
                <p style="margin: 4px 0 0; font-size: 12px;">
                    Facultad de Ingeniería Electrónica e Informática · Universidad Nacional Federico Villarreal
                </p>
            </div>
        </div>
        <div style="text-align: right; font-size: 11px;">
            <div><strong>Generado:</strong> {{ $generatedAt->clone()->setTimezone($timezone)->format('d/m/Y H:i') }}</div>
            <div><strong>Estado:</strong> {{ strtoupper($acta->status) }}</div>
            @if($acta->period?->name)
                <div><strong>Periodo:</strong> {{ $acta->period->name }}</div>
            @endif
        </div>
    </div>

    <table class="metadata">
        <tr>
            <td class="label">Curso</td>
            <td>{{ $course->code }} · {{ $course->name }}</td>
            <td class="label">Créditos</td>
            <td>{{ $course->credits }}</td>
        </tr>
        <tr>
            <td class="label">Escuela</td>
            <td>{{ $course->school?->name ?? 'Sin registro' }}</td>
            <td class="label">Docente</td>
            <td>{{ $teacherUser?->name ?? 'No asignado' }}</td>
        </tr>
        <tr>
            <td class="label">Correo docente</td>
            <td>{{ $teacherUser?->email ?? '—' }}</td>
            <td class="label">Acta</td>
            <td>#{{ $acta->id }}</td>
        </tr>
    </table>

    <table class="students-table">
        <thead>
            <tr>
                <th class="number">#</th>
                <th>Código</th>
                <th>Estudiante</th>
                <th class="score">Promedio</th>
                <th class="score">Estado</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $courseStudent)
                @php
                    $student = $courseStudent->student;
                    $finalGrade = $courseStudent->finalGrade;
                    $average = optional($finalGrade)->average;
                    $status = optional($finalGrade)->status;
                @endphp
                <tr>
                    <td class="number">{{ $index + 1 }}</td>
                    <td>{{ $student?->code ?? '—' }}</td>
                    <td>{{ $student?->user?->name ?? 'Sin nombre' }}</td>
                    <td class="score">
                        @if(!is_null($average))
                            {{ number_format($average, 2) }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="score">{{ $status ?? 'Pendiente' }}</td>
                    <td>
                        @if($status === 'A')
                            Aprobado
                        @elseif($status === 'D')
                            Desaprobado
                        @elseif($status === 'S')
                            Sustitutorio
                        @elseif($status === 'R')
                            Aplazado
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">
                        No se encontraron estudiantes matriculados para esta sección.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="stats">
        <tr>
            <td><strong>Total estudiantes:</strong> {{ $stats['total'] }}</td>
            <td><strong>Aprobados:</strong> {{ $stats['approved'] }}</td>
            <td><strong>Desaprobados:</strong> {{ $stats['disapproved'] }}</td>
            <td><strong>Sustitutorio:</strong> {{ $stats['substitute'] }}</td>
            <td><strong>Refuerzo:</strong> {{ $stats['makeup'] }}</td>
            <td><strong>Promedio general:</strong> {{ number_format($stats['average'], 2) }}</td>
        </tr>
    </table>

    <div class="signature-block">
        <div class="signature">
            @if(!empty($signature['data_url']))
                <img src="{{ $signature['data_url'] }}" alt="Firma del docente">
            @else
                <div class="signature-placeholder"></div>
            @endif
            <div class="signature-role">Docente responsable</div>
        </div>
    </div>

    <div class="footer">
        Acta generada automáticamente por el Sistema de Notas UNFV · {{ now($timezone)->format('d/m/Y') }}
    </div>
</body>
</html>
