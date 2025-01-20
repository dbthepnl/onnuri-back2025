<?php

namespace App\Http\Controllers\Admin;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Post;
use App\Http\Resources\TrainCollection;
use App\Http\Resources\TrainResource;
use App\Http\Resources\NoticeCollection;
use App\Http\Resources\GongzimeCollection;
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
        ->whereNotIn('category', [22, 23, 24])
        ->allowedSorts(['id', 'title'])
        ->orderBy('order', 'desc')
        ->orderBy('updated_at', 'desc')
        ->paginate(15);
        
        return new TrainCollection($data);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            'public' => 'required|boolean',
            'board' => 'required|string',
            'order' => 'required|boolean',
            'category' => 'nullable',
            'title' => 'required|string',
            'content' => 'nullable|string', 
            'start_at' => 'nullable|string',
            'end_at' => 'nullable|string',
            'cardinal_id' => 'required|string',
            'cardinal_check' => 'nullable|boolean',
            'form_id' => 'nullable|string',
            'form_check' => 'nullable|boolean'
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
                     ->toMediaCollection('files', 's3');
            }
        }
    
        return response()->json([
            'success' => true,
            'message' => '등록 완료'
        ], 200);
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {   
            $data = Post::findOrFail($id);
            return new TrainResource($data);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => '데이터 검증 실패',
            ]);
        }
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
