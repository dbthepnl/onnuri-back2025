<?php

namespace App\Http\Controllers;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Category;
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

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = QueryBuilder::for(Category::class)
        ->where("name_en", $request->name_en) //event, message, news, assembly
        ->allowedFilters([
            "name_ko", //제목 검색
        ])
        ->allowedSorts(['id', 'name_ko']);
        $users = $users->paginate(15);
        

        return new CategoryCollection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            'name_ko' => 'required|string',
            'colorcode' => 'required|string',

        ]);
        $data['name_en'] = 'category';
        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();

        $post = Category::create($data);

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
            $data = Category::where('name_en', 'category')->findOrFail($id);
            return new CategoryResource($data);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => '데이터 검증 실패',
            ]);
        }
    }

  
}
