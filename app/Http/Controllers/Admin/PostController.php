<?php

namespace App\Http\Controllers\Admin;
use App\Models\Post;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Post::get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            'public' => 'required|boolean',
            'title' => 'required|string',
            'board' => 'required|string',
            'category' => 'required|string',
            'content' => 'nullable|string',    
        ]);


        $data->user_id = 1;
        $data->created_at = Carbon::now();
        $data->updated_at = Carbon::now();
        Post::create($data);

        return response()->json([
            'success' => true,
            'message' => '등록 완료'
        ], 200);
    
    }

    public function storeImage(Request $request)
    {
        $media = Auth::user()->addMedia($request->file('image'))->toMediaCollection('post_image');
        return response()->success($media);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {   
            $posts = Post::findOrFail($id);
            return new NoticeResource($posts);
            
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
