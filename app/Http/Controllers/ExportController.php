<?php

namespace App\Http\Controllers;

use App\Models\People;
use Illuminate\Http\Request;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

class ExportController extends Controller
{
    public function arrivalsDepartures()
    {
        $includeDaysPast = config('peopledb.recentish_days_fudge_factor', 14);
        $recentishArrivals = People::arrivingSoon(includingDaysPast: $includeDaysPast)->orderBy('start_at')->get();
        $recentishDepartures = People::leavingSoon(includingDaysPast: $includeDaysPast)->orderBy('end_at')->get();

        $tempFile = tempnam(sys_get_temp_dir(), 'arrivals-departures' . now()->format('YmdHis')) . '.xlsx';
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($tempFile);

        $arrivalRows = $recentishArrivals->map(function ($person) {
            return [
                $person->full_name,
                $person->email,
                $person->start_at?->format('d/m/Y') ?? '',
                $person->type->value ?? '',
                $person->group ?? '',
                $person->reportsTo?->full_name ?? '',
                $person->reportsTo?->email ?? '',
            ];
        });
        $arrivalRows->prepend([
            'Name',
            'Email',
            'Start Date',
            'Type',
            'Group',
            'Reports To',
            'Reports To Email',
        ]);

        $departureRows = $recentishDepartures->map(function ($person) {
            return [
                $person->full_name,
                $person->email,
                $person->end_at?->format('d/m/Y') ?? '',
                $person->type->value ?? '',
                $person->group ?? '',
                $person->reportsTo?->full_name ?? '',
                $person->reportsTo?->email ?? '',
            ];
        });
        $departureRows->prepend([
            'Name',
            'Email',
            'End Date',
            'Type',
            'Group',
            'Reports To',
            'Reports To Email',
        ]);

        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Arrivals');
        $writer->addRows($arrivalRows->map(fn ($row) => WriterEntityFactory::createRowFromArray($row))->toArray());

        $newSheet = $writer->addNewSheetAndMakeItCurrent();
        $newSheet->setName('Departures');
        $writer->addRows($departureRows->map(fn ($row) => WriterEntityFactory::createRowFromArray($row))->toArray());

        $writer->close();

        return response()->download(
            $tempFile,
            'arrivals_departures_' . now()->format('d_m_Y_H_i') . '.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend(true);
    }
}
