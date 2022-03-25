<?php

namespace App\Models;

use Illuminate\Support\Collection;

class ArrivalsDeparturesDto
{
    public function __construct(
        public Collection $arrivals,
        public Collection $departures,
        public Collection $arrived,
        public Collection $departed
    ) {
    }
}
