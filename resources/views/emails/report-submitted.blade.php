<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        h2 {
            color: #2563eb;
        }
        .info {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info p {
            margin: 5px 0;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Izvještaj je uspješno predan</h2>

        <div class="info">
            <p><strong>Lokacija:</strong> {{ $report->location->name }}</p>
            <p><strong>Datum:</strong> {{ \Carbon\Carbon::parse($report->date)->format('d.m.Y') }}</p>
            <p><strong>Predao:</strong> {{ $report->submittedBy->first_name }} {{ $report->submittedBy->last_name }}</p>
        </div>

        <p>Izvještaj možete pregledati u sistemu.</p>

        <a href="{{ env('FRONTEND_URL') }}/reports/{{ $report->id }}" class="button">
            Pregledaj Izvještaj
        </a>

        <p style="margin-top: 30px; color: #666; font-size: 12px;">
            Ovo je automatska poruka. Molimo ne odgovarajte na ovaj email.
        </p>
    </div>
</body>
</html>
