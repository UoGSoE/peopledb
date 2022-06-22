<?php

namespace App\Http\Controllers\Api;

use App\Models\People;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class PeopleController extends Controller
{
    public function index()
    {
        $people = QueryBuilder::for(People::class)
            ->allowedFilters([
                AllowedFilter::scope('current'),
                AllowedFilter::scope('peopleType'),
                AllowedFilter::scope('group'),
                AllowedFilter::callback('start_after', fn ($query, $value) => $query->where('start_at', '>=', $value)),
                AllowedFilter::callback('end_before', fn ($query, $value) => $query->where('end_at', '<=', $value)),
                AllowedFilter::exact('id'),
                AllowedFilter::exact('username'),
                AllowedFilter::exact('email'),
            ])
            ->allowedIncludes([
                'reportsTo',
                'reportees'
            ])
            ->get();

        return response()->json([
            'data' => $people
        ]);
    }
}
