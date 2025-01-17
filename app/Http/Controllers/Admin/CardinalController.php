<?php

namespace App\Http\Controllers\Admin;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Cardinal;
use App\Http\Resources\CardinalCollection;
use App\Http\Resources\CardinalResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\UserRequest;

class CardinalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
            $data = QueryBuilder::for(Cardinal::class)
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) { //전체 검색
                    $query->where(function ($query) use ($value) {
                        $query->where('title', 'like', "%$value%");
                    });
                }),
            ])
            ->allowedSorts(['id', 'name_ko']);
            
        $data = 
        $data->leftJoin('boards', 'boards.id', '=', 'categories.board_id')
            ->select('categories.*', 'boards.name as board_name')
            ->paginate(15);
        
        return new CardinalCollection($users);
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
