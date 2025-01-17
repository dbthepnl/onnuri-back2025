<?php

namespace App\Http\Controllers\Admin;
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
        ->where("board", $request->board) //event, message, news, assembly
        ->when($request->has('category'), function ($query) use ($request) {
            $query->where("category", $request->category); // 기본값 1: 공지사항
        })
        ->allowedFilters([
            "title", //제목 검색
            AllowedFilter::callback('search', function ($query, $value) { //전체 검색
                $query->where(function ($query) use ($value) {
                    $query->where('title', 'like', "%$value%");
                });
            }),
        ])
        ->allowedSorts(['id', 'title'])
        ->orderBy('updated_at', 'desc')
        ->paginate(15);

    return get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'board' => 'required|string',
            'content' => 'required|string', 
            'start' => 'nullable|string',
            'end' => 'nullable|string',
            'background_color' => 'nullable|string',
            'border_color' => 'nullable|string',
        ]);

        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();
        $data['user_id'] = 1;
        $data['category'] = $request->category ?? 1;

        $post = Calendar::create($data);

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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $post = Calendar::findOrFail($id);
    
        $request->updated_at = Carbon::now();
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
        $post = Calendar::findOrFail($id);
        $post->forceDelete();
        return response()->json([
            'success' => true,
            'message' => '삭제 완료'
        ], 200);
    }
}
