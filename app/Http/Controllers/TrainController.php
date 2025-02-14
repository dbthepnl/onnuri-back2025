<?php

namespace App\Http\Controllers;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Post;
use App\Models\Board;
use App\Http\Resources\TrainCollection;
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

class TrainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = QueryBuilder::for(Post::class)
        ->selectRaw('posts.id, boards.name_ko, posts.board_id, posts.cardinal_id, posts.board, posts.title, posts.cardinal_check, posts.form_check, posts.updated_at, posts.created_at')
        ->where("boards.id", $request->board_id) // event, message, news, assembly
        ->when($request->has('category'), function ($query) use ($request) {
            $query->where("category", $request->category); // 기본값 1: 공지사항
        })
        ->allowedFilters([
            "title", // 제목 검색
            AllowedFilter::callback('search', function ($query, $value) { // 전체 검색
                $query->where(function ($query) use ($value) {
                    $query->where('title', 'like', "%$value%");
                });
            }),
        ])
        ->leftJoin('boards', 'boards.id', '=', 'posts.board_id')  // Add the join condition here
        ->allowedSorts(['posts.id', 'posts.title'])
        ->orderBy('posts.order', 'desc')
        ->orderBy('posts.updated_at', 'desc')
        ->paginate(15);

        $board = Board::where('id', $request->board_id)->first();
        return new TrainCollection($data, $board);

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
    public function show(Request $request, string $id)
    {
        try {   

            $data = Post::where('id', $id)->first();
            $board = Board::where('id', $data['board_id'])->first();
            return response()->json(['board' => $board, 'data' => $data]);
            
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
