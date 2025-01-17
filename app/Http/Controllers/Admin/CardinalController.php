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
            ->where("board_id", $request->board_id) //program
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) { //전체 검색
                    $query->where(function ($query) use ($value) {
                        $query->where('title', 'like', "%$value%");
                    });
                }),
            ]);
            
        $data = 
        $data->leftJoin('boards', 'boards.id', '=', 'cardinals.board_id')
            ->select('cardinals.*', 'boards.name_ko')
            ->orderBy('cardinals.cardinal_number', 'desc')
            ->paginate(15);
        
        return new CardinalCollection($data);
    }

    public function cardinalList(Request $request) {
        $data = QueryBuilder::for(Cardinal::class)
        ->where("board_id", $request->board_id) //program
        ->select('id')
        ->orderBy('id', 'desc')->first();
        
            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => '조회'
            ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'public' => 'nullable|string', //공개/비공개
            'board_id' => 'nullable',
            'title' => 'nullable',
            'name_en' => 'nullable',
            'start_at' => 'nullable',
            'end_at' => 'nullable',
            'cardinal_number' => 'nullable',
            'trainers' => 'nullable',
            'content' => 'nullable',
            'trainer_place' => 'nullable',
        ]);
        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();

        $post = Cardinal::create($data);

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
            $data = Cardinal::findOrFail($id);
            return new CardinalResource($data);
            
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

        $data = Cardinal::findOrFail($id);
        $request->updated_at = Carbon::now();
        $request->created_at = Carbon::now();
        $data->update($request->all());
    
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
        $data = Cardinal::findOrFail($id);
        $data->forceDelete();
        return response()->json([
            'success' => true,
            'message' => '삭제 완료'
        ], 200);
    }
}
