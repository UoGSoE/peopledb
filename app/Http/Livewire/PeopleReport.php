<?php

namespace App\Http\Livewire;

use App\Models\People;
use Livewire\Component;

class PeopleReport extends Component
{
    public function render()
    {
        return view('livewire.people-report', [
            'people' => $this->getFilteredPeople(),
        ]);
    }

    protected function getFilteredPeople()
    {
        return People::orderBy('start_at')->get();
    }
}
