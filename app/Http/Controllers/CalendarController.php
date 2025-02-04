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

        $users = QueryBuilder::for(Post::class)
        ->selectRaw('posts.*, categories.name_ko, categories.colorcode') // selectRaw
        ->where("board", "calendar") //event, message, news, assembly
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

    /**
     * Display a listing of the resource.
     */
    public function indexPopups(Request $request)
    {
        $data = QueryBuilder::for(Post::class)
        ->where("board", $request->board) //event, message, news, assembly
        ->where('category', 24)
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

        $data->getCollection()->transform(function ($post) {
            $post['file_exist'] = $post->hasMedia('files') ? 1 : 0; 
            
            return $post;
        });
        
        return $data;
        return new PopupCollection($data);
    
    }


    /**
     * Display a listing of the resource.
     */
    public function indexShorts(Request $request)
    {
        $users = QueryBuilder::for(Post::class)
        ->where("board", $request->board) //event, message, news, assembly
        ->where('category', 22)
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

        return new NoticeCollection($users);

    }

     /**
     * Display a listing of the resource.
     */
    public function indexVideos(Request $request)
    {

        $users = QueryBuilder::for(Post::class)
        ->where("board", $request->board) //event, message, news, assembly
        ->where('category', 23)
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
 
        return new NoticeCollection($users);
    
    }


    /**
     * Display a listing of the resource.
     */
    public function indexGongzimes(Request $request)
    {

        $users = QueryBuilder::for(Post::class)
        ->selectRaw('id, public, board, category, category_id, title, content, created_at, updated_at')
        ->where("board", $request->board) //event, message, news, assembly
        ->when($request->has('category'), function ($query) use ($request) {
            $query->where("category", $request->category); // 1, 2, 3, 25
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

        $users->getCollection()->transform(function ($post) {

            if ($post->hasMedia('files')) {
                $post->files = $post->getMedia('files')->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'url' => $media->getUrl(),
                        'name' => $media->file_name,
                        'size' => $media->size,
                    ];
                });
            }
    
            return $post->makeHidden(['media']);
        });

        return new GongzimeCollection($users);
    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'public' => 'required|boolean',
            'order' => 'nullable',
            'update_on' => 'nullable',
            'board' => 'required|string',
            'bus_content' => 'nullable|string',
            'safe_content' => 'nullable|string',
            'category_id' => 'nullable',
            'content' => 'nullable|string', 
            'urls' => 'nullable|string', 
            'start_at' => 'nullable|string',
            'end_at' => 'nullable|string',
            'time_at' => 'nullable|string',
        ]);

        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();
        $data['user_id'] = 1;
        $data['category'] = $request->category ?? 1;

        $post = Post::create($data);
        
        if($request->hasFile('notice_photo')){
            $post->addMedia($request->file('notice_photo'))->toMediaCollection('n_photo', 's3');
        }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $post->addMedia($file)
                     ->toMediaCollection('files', 's3'); // Store in 'files' collection on S3
            }
        }
    
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

        $post = Post::findOrFail($id);
        $post->update($request->all());
    
        return response()->json([
            'success' => true,
            'message' => '업데이트 완료'
        ], 200);
    }

    public function updatePost(Request $request, string $id)
    {
        $post = Post::findOrFail($id);
        $post->update($request->all());
    
        return response()->json([
            'success' => true,
            'message' => '업데이트 완료'
        ], 200);
    }


    public function storeImage(Request $request)
    {
        $media = Auth::user()->addMedia($request->file('image'))->toMediaCollection('post_image');
        return response()->json(['result' => true, 'data' => $media, 'message' => NULL]);
    }

    public function profile(Request $request, string $id) {
        $post = Post::findOrFail($id);

        //이미지 삭제한 경우
        if($request->status == 'delete'){
            $post->clearMediaCollection('n_photo');
        }
        //이미지 업데이트한 경우
        if($request->hasFile('notice_photo') && $request->status == 'update'){
            $post->clearMediaCollection('n_photo');
            $post->addMedia($request->file('notice_photo'))->toMediaCollection('n_photo', 's3');
        }

        //이미지 추가한 경우
        if($request->hasFile('notice_photo') && $request->status == 'add'){
            $post->addMedia($request->file('notice_photo'))->toMediaCollection('n_photo', 's3');
        }

        //이미지 기존걸 유지한 경우
        if(!$request->hasFile('notice_photo') && $request->status == 'keep'){
            $post->clearMediaCollection('n_photo');
        }


        return response()->json([
            'success' => true,
            'message' => '수정 완료'
        ], 200);
    }

    public function fileUpdate(Request $request, string $id) {
        $uploadedMedia = [];
        $data = Post::where('id', $id)->first();
        
        if ($request->status == 'delete') { 
           $data =  DB::table('media')->where('id', $request->media_id)->delete();
        }

        if ($request->status == 'add' &&  $request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                 $media = $data->addMedia($file)
                     ->toMediaCollection('files', 's3'); 

                $uploadedMedia[] = [
                    'id' => $media->id,
                    'url' => $media->getUrl(),  
                    'name' => $media->file_name,
                    'size' => $media->size,     
                ];
            }
        }

        if ($request->status == 'update' && $request->hasFile('files')) {
            foreach ($request->file('files') as $file) {

                $media = $data->addMedia($file)
                     ->toMediaCollection('files', 's3'); 

                $uploadedMedia[] = [
                    'url' => $media->getUrl(),  
                    'name' => $media->file_name,
                    'size' => $media->size,     
                ];

                
            }
            DB::table('media')->where('id', $request->media_id)->delete();

        }


        return response()->json([
            'success' => true,
            'message' => '수정 완료',
            'uploaded_media' => $uploadedMedia,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::findOrFail($id);
        $post->forceDelete();
        return response()->json([
            'success' => true,
            'message' => '삭제 완료'
        ], 200);
    }
}
