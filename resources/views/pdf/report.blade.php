<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dnevni Izvještaj - {{ $report->date }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        h1 {
            color: #2563eb;
            text-align: center;
            font-size: 24px;
            margin-bottom: 10px;
        }
        h2 {
            color: #1e40af;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 5px;
            margin-top: 20px;
            font-size: 16px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #2563eb;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .total-row {
            font-weight: bold;
            background-color: #e0e7ff !important;
        }
        .grand-total {
            font-size: 14px;
            color: #1e40af;
        }
        .notes {
            background-color: #fef3c7;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Dnevni Izvještaj</h1>
        <div class="info">
            <p><strong>Lokacija:</strong> {{ $location->name }}</p>
            @if($location->address)
                <p><strong>Adresa:</strong> {{ $location->address }}, {{ $location->city }}</p>
            @endif
            <p><strong>Datum:</strong> {{ \Carbon\Carbon::parse($report->date)->format('d.m.Y') }} ({{ $report->day_of_week }})</p>
        </div>
    </div>

    @if($fiscalItems->count() > 0)
    <h2>Fiskalne Usluge</h2>
    <table>
        <thead>
            <tr>
                <th>Usluga</th>
                <th>Cijena</th>
                <th>Doktori</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fiscalItems as $item)
            <tr>
                <td>{{ $item->service_name }}</td>
                <td>{{ number_format($item->price, 2) }} KM</td>
                <td>
                    @if($item->doctor_counts)
                        @foreach($item->doctor_counts as $doctorId => $count)
                            {{ $count }}x
                        @endforeach
                    @endif
                </td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2">Ukupno Fiskalne</td>
                <td>{{ number_format($totals['totalFiscal'], 2) }} KM</td>
            </tr>
        </tbody>
    </table>
    @endif

    @if($nonFiscalItems->count() > 0)
    <h2>Nefiskalne Usluge</h2>
    <table>
        <thead>
            <tr>
                <th>Usluga</th>
                <th>Cijena</th>
                <th>Doktori</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nonFiscalItems as $item)
            <tr>
                <td>{{ $item->service_name }}</td>
                <td>{{ number_format($item->price, 2) }} KM</td>
                <td>
                    @if($item->doctor_counts)
                        @foreach($item->doctor_counts as $doctorId => $count)
                            {{ $count }}x
                        @endforeach
                    @endif
                </td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2">Ukupno Nefiskalne</td>
                <td>{{ number_format($totals['totalNonFiscal'], 2) }} KM</td>
            </tr>
        </tbody>
    </table>
    @endif

    @if($cardPayments->count() > 0)
    <h2>Kartično Plaćanje</h2>
    <table>
        <thead>
            <tr>
                <th>Usluga</th>
                <th>Cijena</th>
                <th>Doktori</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cardPayments as $item)
            <tr>
                <td>{{ $item->service_name }}</td>
                <td>{{ number_format($item->price, 2) }} KM</td>
                <td>
                    @if($item->doctor_counts)
                        @foreach($item->doctor_counts as $doctorId => $count)
                            {{ $count }}x
                        @endforeach
                    @endif
                </td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2">Ukupno Kartice</td>
                <td>{{ number_format($totals['totalCardPayments'], 2) }} KM</td>
            </tr>
        </tbody>
    </table>
    @endif

    @if($wireTransfers->count() > 0)
    <h2>Virman</h2>
    <table>
        <thead>
            <tr>
                <th>Pacijent</th>
                <th>Cijena</th>
                <th>Doktori</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wireTransfers as $item)
            <tr>
                <td>{{ $item->patient_name }}</td>
                <td>{{ number_format($item->price, 2) }} KM</td>
                <td>
                    @if($item->doctor_counts)
                        @foreach($item->doctor_counts as $doctorId => $count)
                            {{ $count }}x
                        @endforeach
                    @endif
                </td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2">Ukupno Virman</td>
                <td>{{ number_format($totals['totalWireTransfers'], 2) }} KM</td>
            </tr>
        </tbody>
    </table>
    @endif

    @if($patients->count() > 0)
    <h2>Pacijenti</h2>
    <table>
        <thead>
            <tr>
                <th>Ime i Prezime</th>
                <th>Grad</th>
                <th>Razlog</th>
                <th>Doktor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($patients as $patient)
            <tr>
                <td>{{ $patient->full_name }}</td>
                <td>{{ $patient->city ?? '-' }}</td>
                <td>{{ $patient->reason ?? '-' }}</td>
                <td>{{ $patient->doctor ? $patient->doctor->first_name . ' ' . $patient->doctor->last_name : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($unpaidExams->count() > 0)
    <h2>Nenaplaćeni Pregledi</h2>
    <table>
        <thead>
            <tr>
                <th>Ime</th>
                <th>Prezime</th>
                <th>Razlog Nenaplaćivanja</th>
                <th>Doktor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($unpaidExams as $exam)
            <tr>
                <td>{{ $exam->patient_first_name }}</td>
                <td>{{ $exam->patient_last_name }}</td>
                <td>{{ $exam->reason }}</td>
                <td>{{ $exam->doctor ? $exam->doctor->first_name . ' ' . $exam->doctor->last_name : '-' }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4">Ukupno Nenaplaćenih Pregleda: {{ $unpaidExams->count() }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    @if($todayPatientsQuick->count() > 0 || $todayPatientsDetailed->count() > 0)
    <h2>Današnji Pacijenti - Pregled po Uslugama</h2>

    <?php
        // Aggregate services from both quick and detailed entries
        $servicesSummary = [];

        // Add quick entries
        foreach($todayPatientsQuick as $quick) {
            if (!isset($servicesSummary[$quick->service_name])) {
                $servicesSummary[$quick->service_name] = 0;
            }
            $servicesSummary[$quick->service_name] += $quick->count;
        }

        // Add detailed entries
        foreach($todayPatientsDetailed as $detailed) {
            if (!isset($servicesSummary[$detailed->service_name])) {
                $servicesSummary[$detailed->service_name] = 0;
            }
            $servicesSummary[$detailed->service_name] += 1;
        }

        // Sort by count descending
        arsort($servicesSummary);
    ?>

    <table>
        <thead>
            <tr>
                <th style="width: 60%;">Usluga</th>
                <th style="width: 40%; text-align: center;">Broj Pacijenata</th>
            </tr>
        </thead>
        <tbody>
            @foreach($servicesSummary as $serviceName => $count)
            <tr>
                <td>{{ $serviceName }}</td>
                <td style="text-align: center; font-weight: bold; font-size: 14px;">{{ $count }}x</td>
            </tr>
            @endforeach
            <tr class="total-row" style="background-color: #2563eb !important; color: white;">
                <td style="font-weight: bold; font-size: 14px;">UKUPNO DANAŠNJIH PACIJENATA</td>
                <td style="text-align: center; font-weight: bold; font-size: 16px;">{{ $todayPatientsTotal }}</td>
            </tr>
        </tbody>
    </table>

    @if($todayPatientsDetailed->count() > 0)
    <h3 style="font-size: 14px; color: #1e40af; margin-top: 20px; margin-bottom: 10px;">Detaljni Pregled Pacijenata</h3>
    <table>
        <thead>
            <tr>
                <th>Ime</th>
                <th>Prezime</th>
                <th>Usluga</th>
                <th>Napomena</th>
            </tr>
        </thead>
        <tbody>
            @foreach($todayPatientsDetailed as $detailed)
            <tr>
                <td>{{ $detailed->patient_first_name }}</td>
                <td>{{ $detailed->patient_last_name }}</td>
                <td>{{ $detailed->service_name }}</td>
                <td>{{ $detailed->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    @endif

    @if($workSchedule->count() > 0)
    <h2>Raspored Rada</h2>
    <table>
        <thead>
            <tr>
                <th>Zaposlenik</th>
                <th>Dolazak</th>
                <th>Odlazak</th>
                <th>Sati</th>
            </tr>
        </thead>
        <tbody>
            @foreach($workSchedule as $schedule)
            <tr>
                <td>{{ $schedule->employee_name }}</td>
                <td>{{ $schedule->arrival_time ?? '-' }}</td>
                <td>{{ $schedule->departure_time ?? '-' }}</td>
                <td>{{ $schedule->hours_worked ? number_format($schedule->hours_worked, 2) : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <h2>Ukupni Pregled</h2>
    <table>
        <tbody>
            <tr>
                <td>Fiskalne Usluge</td>
                <td>{{ number_format($totals['totalFiscal'], 2) }} KM</td>
            </tr>
            <tr>
                <td>Nefiskalne Usluge</td>
                <td>{{ number_format($totals['totalNonFiscal'], 2) }} KM</td>
            </tr>
            <tr>
                <td>Kartično Plaćanje</td>
                <td>{{ number_format($totals['totalCardPayments'], 2) }} KM</td>
            </tr>
            <tr>
                <td>Virman</td>
                <td>{{ number_format($totals['totalWireTransfers'], 2) }} KM</td>
            </tr>
            <tr class="total-row grand-total">
                <td>UKUPNO</td>
                <td>{{ number_format($totals['grandTotal'], 2) }} KM</td>
            </tr>
        </tbody>
    </table>

    @if($report->notes)
    <div class="notes">
        <h3>Napomene:</h3>
        <p>{{ $report->notes }}</p>
    </div>
    @endif

    @if($plannedProcedures->count() > 0)
    <div style="margin-top: 30px; page-break-inside: avoid;">
        <h2 style="background-color: #10b981; color: white; padding: 10px; border-radius: 5px;">📋 PLAN - Planirane Procedure</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 30%;">Ime i Prezime</th>
                    <th style="width: 30%;">Vrsta Procedure</th>
                    <th style="width: 25%;">Planirano</th>
                    <th style="width: 15%;">Napomena</th>
                </tr>
            </thead>
            <tbody>
                @foreach($plannedProcedures as $procedure)
                <tr>
                    <td>{{ $procedure->patient_first_name }} {{ $procedure->patient_last_name }}</td>
                    <td>
                        <strong>{{ $procedure->procedure_type }}</strong>
                        @if($procedure->procedure_details)
                        <br><small style="color: #666;">{{ $procedure->procedure_details }}</small>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($procedure->planned_date)
                            <strong>{{ \Carbon\Carbon::parse($procedure->planned_date)->format('d.m.Y') }}</strong>
                        @else
                            {{ $procedure->planned_month ?? '-' }}
                        @endif
                    </td>
                    <td style="font-size: 10px;">{{ $procedure->notes ?? '-' }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" style="text-align: center;">
                        Ukupno Planiranih Procedura: <strong>{{ $plannedProcedures->count() }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Generisano: {{ now()->format('d.m.Y H:i') }}</p>
    </div>
</body>
</html>
