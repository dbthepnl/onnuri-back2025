<?php

namespace App\Http\Controllers\Admin;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Board;
use App\Models\Category;
use App\Http\Resources\BoardCollection;
use App\Http\Resources\BoardResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\UserRequest;

class BoardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = QueryBuilder::for(Board::class)
        ->where("name_en", "program") //program
        ->allowedFilters([
            AllowedFilter::callback('search', function ($query, $value) { //전체 검색
                $query->where(function ($query) use ($value) {
                    $query->where('name_ko', 'like', "%$value%");
                });
            }),
        ])
        ->allowedSorts(['id', 'name_ko']);
        $data = $data->get();
        
        return new BoardResource($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ko' => 'required|string', //프로그램명
            'name_en' => 'nullable'
            'information' => 'nullable'

        ]);
        $data['name_en'] = 'program';
        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();

        $post = Board::create($data);

        return response()->json([
            'success' => true,
            'message' => '등록 완료'
        ], 200);
    }

    public function categories(Request $request) {
        $data = QueryBuilder::for(Category::class)
        ->where('boards.name_ko', $request->name_ko)
        ->get();

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => '등록 완료'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {   
            $data = Board::where('name_en', 'program')->findOrFail($id);
            return new BoardResource($data);
            
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
        $data = Board::findOrFail($id);
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
        $data = Board::findOrFail($id);
        $data->forceDelete();
        return response()->json([
            'success' => true,
            'message' => '삭제 완료'
        ], 200);
    }
}
