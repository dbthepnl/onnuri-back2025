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
        ->selectRaw('posts.*, categories.name_ko, categories.colorcode') // selectRaw
        ->where("board", $request->board) //event, message, news, assembly
        ->where('public', 1)
        ->when($request->has('category'), function ($query) use ($request) {
            $query->where("category", $request->category); // 카테고리명 
        })
        ->when($request->has('category_id'), function ($query) use ($request) {
            $query->where("category_id", $request->category_id); // 카테고리명 
        })
        ->when($request->has('month') && $request->has('year'), function ($query) use ($request) {
            $query->whereYear('start_at', $request->year)
                  ->whereMonth('start_at', $request->month);
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
        ->leftJoin('categories', 'posts.category_id', '=', 'categories.id');
        if ($request->pageType == 'main') {
            $users = $users->get();
            $users->map(fn($e) => $e->append(['img']));
            return response( $users);
        }
        
        if ($request->pageType == 'list') {
            $users = $users
                ->orderByRaw("CASE WHEN posts.order = 1 THEN 1 ELSE 2 END")
                ->orderBy('start_at', 'desc') 
                ->paginate(15);
            $users->map(fn($e) => $e->append(['img']));
            return new NoticeCollection($users);
        }

    }

    public function indexGongzimes(Request $request)
    {
        $users = QueryBuilder::for(Post::class)
        ->selectRaw('id, order, board, public, title, content, created_at, updated_at')
        ->where("board", $request->board) //event, message, news, assembly
        ->when($request->has('category'), function ($query) use ($request) {
            $query->where("category", $request->category); // 1, 2, 3, 25
        })
        ->orderBy('order', 'desc')
        ->orderBy('updated_at', 'desc')
        ->paginate(15);

        return response()->json($users->makeHidden(['media']));
    
    }

    public function shorts(Request $request) {
        $data = QueryBuilder::for(Post::class)
        ->selectRaw('id, public, urls, created_at')
        ->where("board", "assembly")
        ->where("category", 22) 
        ->where('public', 1)
        ->get();
        $data->map(function ($e) {
            $e->setAppends(['img']);
            return $e;
        });

        return response()->json($data->makeHidden(['media']));
    }

    public function videos(Request $request) {
        $data = QueryBuilder::for(Post::class)
        ->selectRaw('id, public, urls, created_at')
        ->where("board", "assembly")
        ->where("category", 23) 
        ->where('public', 1)
        ->get();

        return response()->json($data);
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
    public function show(string $id)
    {
        try {   
            $subCategories = Cache::remember('categories-' . $id , 60 * 60 * 24, function() use ($id) {
                $data = Post::findOrFail($id);
                return $data;
            });

            return new NoticeResource($subCategories);
            
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
