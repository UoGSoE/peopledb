<?php

namespace App\Http\Livewire;

use App\Models\People;
use Livewire\Component;
use App\Models\PeopleType;
use Ohffs\SimpleSpout\ExcelSheet;

class PeopleReport extends Component
{
    public $showAllFilters = false;
    public $filterType;
    public $filterReportsTo;
    public $filterGroup;
    public $filterArrivingInDays;
    public $filterLeavingInDays;
    public $filterArrivedInDays;
    public $filterLeftInDays;
    public $filterSearch;

    public $possibleGroups = [];
    public $possibleTypes = [];
    public $possibleReportsTo = [];

    protected $queryString = [
        'showAllFilters',
        'filterType',
        'filterReportsTo',
        'filterGroup',
        'filterArrivingInDays',
        'filterLeavingInDays',
        'filterArrivedInDays',
        'filterLeftInDays',
    ];

    public function render()
    {
        $people = $this->getFilteredPeople();
        return view('livewire.people-report', [
            'people' => $people,
        ]);
    }

    public function updating($field, $value)
    {
        if ($field === 'filterArrivingInDays') {
            $this->filterLeavingInDays = null;
            $this->filterArrivedInDays = null;
            $this->filterLeftInDays = null;
        }
        if ($field === 'filterLeavingInDays') {
            $this->filterArrivingInDays = null;
            $this->filterArrivedInDays = null;
            $this->filterLeftInDays = null;
        }
        if ($field === 'filterArrivedInDays') {
            $this->filterLeavingInDays = null;
            $this->filterArrivingInDays = null;
            $this->filterLeftInDays = null;
        }
        if ($field === 'filterLeftInDays') {
            $this->filterArrivingInDays = null;
            $this->filterArrivedInDays = null;
            $this->filterLeavingInDays = null;
        }
    }

    protected function getFilteredPeople()
    {
        $this->possibleTypes = PeopleType::orderBy('name')->get();
        $this->possibleGroups = People::select('group')->distinct()->get()->pluck('group');
        $this->possibleReportsTo = People::whereHas('reportees')->orderBy('surname')->get();
        return People::orderByDesc('start_at')->with(['reportsTo', 'type'])
            ->when($this->filterType, function ($query) {
                return $query->where('people_type_id', $this->filterType);
            })
            ->when($this->filterReportsTo, function ($query) {
                return $query->where('reports_to', $this->filterReportsTo);
            })
            ->when($this->filterGroup, function ($query) {
                return $query->where('group', $this->filterGroup);
            })
            ->when($this->filterArrivingInDays, function ($query) {
                return $query->whereBetween('start_at', [now(), now()->addDays(intval($this->filterArrivingInDays))]);
            })
            ->when($this->filterLeavingInDays, function ($query) {
                return $query->whereBetween('end_at', [now(), now()->addDays(intval($this->filterLeavingInDays))]);
            })
            ->when($this->filterArrivedInDays, function ($query) {
                return $query->whereBetween('start_at', [now()->subDays(intval($this->filterArrivedInDays)), now()]);
            })
            ->when($this->filterLeftInDays, function ($query) {
                return $query->whereBetween('end_at', [now()->subDays(intval($this->filterLeftInDays)), now()]);
            })
            ->when($this->filterSearch, function ($query) {
                return $query->where(function ($query) {
                    $query->where('username', 'like', '%' . $this->filterSearch . '%')
                        ->orWhere('forenames', 'like', '%' . $this->filterSearch . '%')
                        ->orWhere('surname', 'like', '%' . $this->filterSearch . '%')
                        ->orWhere('email', 'like', '%' . $this->filterSearch . '%');
                });
            })
            ->get();
    }

    public function toggleFilterDisplay()
    {
        $this->showAllFilters = !$this->showAllFilters;
    }

    public function exportExcel()
    {
        $people = $this->getFilteredPeople()->map(function ($person) {
            return [
                $person->full_name,
                $person->email,
                $person->start_at?->format('d/m/Y') ?? '',
                $person->end_at?->format('d/m/Y') ?? '',
                $person->type->name ?? '',
                $person->group ?? '',
                $person->reportsTo?->full_name ?? '',
                $person->reportsTo?->email ?? '',
            ];
        });
        $people->prepend([
            'Name',
            'Email',
            'Start Date',
            'End Date',
            'Type',
            'Group',
            'Reports To',
            'Reports To Email',
        ]);
        $filename = 'people_report_' . now()->format('d_m_Y_H_i') . '.xlsx';
        $tempSheet = (new ExcelSheet())->generate($people->toArray());

        return response()->download(
            $tempSheet,
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend(true);
    }
}
