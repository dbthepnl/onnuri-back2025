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

        $data = QueryBuilder::for(Calendar::class)
        ->selectRaw('calendars.*') // selectRaw
        ->where("board", "calendar") //달력만
        ->when($request->has('category'), function ($query) use ($request) {
            $query->where("category", $request->category); // 카테고리명 
        })
        ->when($request->has('month') && $request->has('year'), function ($query) use ($request) {
            $query->whereYear('start', $request->year)
                  ->whereMonth('start', $request->month);
        })
        ->allowedFilters([
            "title", //제목 검색
            AllowedFilter::callback('search', function ($query, $value) { //전체 검색
                $query->where(function ($query) use ($value) {
                    $query->where('title', 'like', "%$value%");
                });
            }),
        ])
        ->allowedSorts(['id', 'title']);
        if ($request->pageType == 'grid') {
            $data = $data->get();
            $data->map(fn($e) => $e->append(['img']));
            return response($data);
        }
        
        if ($request->pageType == 'list') {
            $data = $data
                ->orderByRaw("CASE WHEN calendars.order = 1 THEN 1 ELSE 2 END")
                ->orderBy('start', 'desc') 
                ->paginate(15);
            $data->map(fn($e) => $e->append(['img']));
            return new CalendarCollection($data);
        }
    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            'user_id' => 'required|string',
            'board' => 'required|string',
            'order' => 'nullable',
            'category' => 'nullable',
            'public' => 'required|string',
            'title' => 'nullable|string',
            'background_color' => 'nullable|string',
            'border_color' => 'nullable',
            'start' => 'nullable|string', 
            'end' => 'nullable|string', 
            'content' => 'nullable|string'
        ]);

        $post = Calendar::create($data);
        
        if($request->hasFile('calendar_photo')){
            $post->addMedia($request->file('calendar_photo'))->toMediaCollection('c_photo', 's3');
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
            $data = Calendar::findOrFail($id);
            return new CalendarResource($data);
            
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

        $post = Calendar::findOrFail($id);
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
        $post = Calendar::findOrFail($id);
        $post->forceDelete();
        return response()->json([
            'success' => true,
            'message' => '삭제 완료'
        ], 200);
    }
}
