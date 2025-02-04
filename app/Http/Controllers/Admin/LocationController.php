<?php

namespace App\Http\Controllers\Admin;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Location;
use App\Http\Resources\LocationResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\UserRequest;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = QueryBuilder::for(Location::class)
        ->allowedFilters([
            AllowedFilter::callback('search', function ($query, $value) { //전체 방 이름으로 검색
                $query->where(function ($query) use ($value) {
                    $query->where('room_name', 'like', "%$value%");
                });
            }),
        ])
        ->allowedSorts(['id', 'title'])
        ->orderBy('updated_at', 'desc')
        ->get();

        return new locationResource($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $data = $request->validate([
            'public' => 'nullable|boolean',
            'building_name' => 'required|string',
            'room_name' => 'required|string',
            'room_type' => 'required|string',
            'population' => '',
            'gender' => '',
            'room_number' => '',

        ]);
        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();

        $location = Location::where('room_name', $request->room_name)->first();
        if($location) {
            return response()->json([
                'success' => false,
                'message' => '이미 존재'
            ], 200);
        }
        $post = Location::create($data);
    
        return response()->json([
            'success' => true,
            'message' => '등록 완료'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {   
            $data = Location::findOrFail($id);
            return new LocationResource($data);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => '데이터 검증 실패',
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $post = Location::findOrFail($id);
        $post->update($request->all());
    
        return response()->json([
            'success' => true,
            'message' => '업데이트 완료'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Location::findOrFail($id);
        $post->forceDelete();
        return response()->json([
            'success' => true,
            'message' => '삭제 완료'
        ], 200);
    }
}
