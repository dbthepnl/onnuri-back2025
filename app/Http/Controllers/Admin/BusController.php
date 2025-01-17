<?php

namespace App\Http\Controllers\Admin;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Category;
use App\Models\Post;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\UserRequest;

class BusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            $data = Post::selectRaw('id, title, content, created_at, updated_at')->where('id', $id)->first();
            
            return response()->json([
                'data' => $data,
                'success' => true,
                'message' => '등록 완료'
            ]);

            
            
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
        try {   
            $data = Post::selectRaw('id, title, content, created_at, updated_at')->where('id', $id)->first();
            $data->update($request->all());
            return response()->json([
                'success' => true,
                'message' => '수정 완료'
            ]);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => '데이터 검증 실패',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
