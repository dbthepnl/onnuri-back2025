<?php

namespace App\Http\Controllers;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Calendar;
use App\Http\Resources\CalendarCollection;
use App\Http\Resources\CalendarResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\UserRequest;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = QueryBuilder::for(Calendar::class)
        ->selectRaw('calendars.*') // selectRaw
    ->where("board", $request->board) // event, message, news, assembly
    ->when($request->has('month') && $request->has('year'), function ($query) use ($request) {
        $query->whereYear('start', $request->year)
              ->whereMonth('start', $request->month);
    })
    ->allowedSorts(['id', 'title'])
    ->orderBy('updated_at', 'desc')
    ->get();

    return new CalendarCollection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
