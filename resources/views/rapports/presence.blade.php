<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport de Présence</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1f2937; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 3px solid #0d6efd; padding-bottom: 15px; }
        .logo { font-size: 28px; font-weight: bold; color: #0d6efd; }
        h1 { color: #0d6efd; font-size: 20px; margin: 10px 0; }
        .stats { display: flex; gap: 10px; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #0d6efd; color: white; padding: 8px; text-align: left; font-size: 11px; }
        td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        tr:nth-child(even) { background: #f8fafc; }
        .footer { margin-top: 30px; text-align: center; color: #9ca3af; font-size: 10px; }
    </style>
</head>
<body>

<div class="header">
    <div class="logo">DATA LINKS</div>
    <h1>📋 Rapport de Présence</h1>
    <p>
        @if($employe) Employé : <strong>{{ $employe->user->name }}</strong> — {{ $employe->departement->nom }} | @endif
        @if($debut && $fin) Période : {{ $debut }} → {{ $fin }} @endif
    </p>
    <p>Généré le : {{ now()->format('d/m/Y à H:i') }}</p>
</div>

<table style="margin-bottom:25px;">
    <tr>
        <td style="text-align:center; background:#eff6ff; padding:12px; border-radius:5px; border:1px solid #0d6efd;">
            <div style="font-size:22px; font-weight:bold; color:#0d6efd;">{{ $totalJours }}</div>
            <div>Jours présents</div>
        </td>
        <td style="text-align:center; background:#f0fdf4; padding:12px; border-radius:5px; border:1px solid #10b981;">
            <div style="font-size:22px; font-weight:bold; color:#10b981;">{{ $heuresTotal }}h</div>
            <div>Heures totales</div>
        </td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th>#</th>
            @if(!$employe)<th>Employé</th><th>Département</th>@endif
            <th>Date</th>
            <th>Arrivée</th>
            <th>Départ</th>
            <th>Durée</th>
        </tr>
    </thead>
    <tbody>
        @foreach($presences as $index => $p)
        <tr>
            <td>{{ $index + 1 }}</td>
            @if(!$employe)
                <td>{{ $p->employe->user->name ?? '-' }}</td>
                <td>{{ $p->employe->departement->nom ?? '-' }}</td>
            @endif
            <td>{{ $p->date }}</td>
            <td>{{ $p->heure_arrivee ?? '-' }}</td>
            <td>{{ $p->heure_depart ?? '-' }}</td>
            <td>
                @if($p->heure_arrivee && $p->heure_depart)
                    {{ round((strtotime($p->heure_depart) - strtotime($p->heure_arrivee)) / 3600, 1) }}h
                @else
                    -
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    <p>DATA LINKS SARL — Rapport de présence généré automatiquement</p>
</div>

</body>
</html>