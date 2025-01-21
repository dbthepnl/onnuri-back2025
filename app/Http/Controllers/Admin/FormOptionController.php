<?php

namespace App\Http\Controllers\Admin;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\FormOption;
use App\Http\Resources\FormCollection;
use App\Http\Resources\FormOptionResource;
use App\Http\Resources\GongzimeCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\UserRequest;

class FormOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = QueryBuilder::for(FormOption::class)
        ->where('form_id', $request->form_id)
        ->orderBy('order', 'desc')
        ->get();
        
        return new FormOptionResource($data);
       

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data = $request->all();

        if (isset($data['values']) && is_array($data['values'])) {
            $data['values'] = json_encode($data['values']);
        }
        
        $post = FormOption::create($data);

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
        $data = FormOption::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => '조회 완료'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->all();

        if (isset($data['values']) && is_array($data['values'])) {
            $data['values'] = json_encode($data['values']);
        }
        
        $post = FormOption::where('id', $id)->update($data);

        return response()->json([
            'success' => true,
            'message' => '수정 완료'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = FormOption::where('id', $id)->forceDelete();

        return response()->json([
            'success' => true,
            'message' => '삭제 완료'
        ], 200);
    }
}
