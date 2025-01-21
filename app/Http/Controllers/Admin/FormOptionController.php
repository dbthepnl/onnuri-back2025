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
        ->orderBy('order', 'desc')
        ->get();
        
        return new FormOptionResource($data);
       

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'form_id' => 'required|boolean',
            'order' => 'required|boolean',
            'element' => 'nullable|string',
            'element_name' => 'required|string',
            'tag' => 'nullable|string', 
            'required' => 'required|boolean',
            'label' => 'nullable|string',
            'values' => 'nullable|json',
            'sub_text' => 'nullable|string',
        ]);

        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();
        $data['user_id'] = 1;

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
        //
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
