<?php

namespace App\Http\Controllers;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Post;
use App\Http\Resources\NoticeCollection;
use App\Http\Resources\GonzimeCollection;
use App\Http\Resources\NoticeResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\UserRequest;

class NoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        $users = QueryBuilder::for(Post::class)
        ->whereNotIn('board', ['popup', 'slider'])
        ->when($request->has('board'), function ($query) use ($request) {
            $query->where("board", $request->board);  // If 'board' exists, filter by it
        })  //훈련관리:train / 공지관리:notice
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
        ->orderBy('order', 'desc')
        ->orderBy('updated_at', 'desc')
        ->paginate(15);

        return new NoticeCollection($users);

    }

    public function popups(Request $request) {
        $data = QueryBuilder::for(Post::class)
        ->selectRaw('id, public, urls, created_at')
        ->where("board", "assembly")
        ->where("category", 24) 
        ->where('public', 1)
        ->get();
        $data->map(function ($e) {
            $e->setAppends(['img']);
            return $e;
        });

        return response()->json($data->makeHidden(['media']));
    }

    public function sliders(Request $request) {

        $data = QueryBuilder::for(Post::class)
        ->selectRaw('id, public, urls, created_at')
        ->where("board", "slider")
        ->where('public', 1)
        ->get();
        $data->map(function ($e) {
            $e->setAppends(['img']);
            return $e;
        });

        return response()->json($data->makeHidden(['media']));
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
        try {   
            $data = Post::findOrFail($id);
            return new NoticeResource($data);
            
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
