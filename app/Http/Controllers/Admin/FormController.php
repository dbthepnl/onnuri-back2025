<?php

namespace App\Http\Controllers\Admin;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Form;
use App\Models\Form1;
use App\Models\Form2;
use App\Models\Form3;
use App\Models\Form4;
use App\Models\Form5;
use App\Models\Form6;
use App\Http\Resources\FormCollection;
use App\Http\Resources\FormResource;
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

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = QueryBuilder::for(Form::class)
        ->allowedFilters([
            "title", //제목 검색
            AllowedFilter::callback('search', function ($query, $value) { //전체 검색
                $query->where(function ($query) use ($value) {
                    $query->where('title', 'like', "%$value%");
                });
            }),
        ])
        ->allowedSorts(['id', 'title'])
        ->orderBy('order', 'desc')
        ->orderBy('updated_at', 'desc')
        ->paginate(15);
        
        return new FormCollection($data);
    }

    public function indexElement(Request $request)
    {
        $data = DB::table('form_elements')->get();
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => '등록 완료'
        ], 200);
    }

    //신청자 관리
    public function registerIndex(Request $request)
    {
        $data = QueryBuilder::for(Form1::class)
        ->where('board_id', $request->board_id)
        ->where('cardinal_id', $request->cardinal_id)
        ->allowedFilters([
            "name", //제목 검색
            AllowedFilter::callback('search', function ($query, $value) { //전체 검색
                $query->where(function ($query) use ($value) {
                    $query->where('name', 'like', "%$value%");
                });
            }),
        ])
        ->allowedSorts(['id', 'name'])
        ->orderBy('order', 'desc')
        ->orderBy('updated_at', 'desc')
        ->paginate(15);
        
        return new FormCollection($data);
    }

    //신청자 관리
    public function registerShow(Request $request, $id)
    {
        $form1 = Form1::where('user_id', $id)->get();
        $form2 = Form2::where('user_id', $id)->get();
        $form3 = Form3::where('user_id', $id)->get();
        $form4 = Form4::where('user_id', $id)->get();
        $form5 = Form4::where('user_id', $id)->get();

        return response()->json([
            'result' => true,
            'data' =>
            [
                'form1' => $form1,
                'form2' => $form2,
                'form3' => $form3,
                'form4' => $form4,
                'form5' => $form5,
                'form6' => NULL,
                'form7' => NULL,
                'form8' => NULL,
                'form9' => NULL
            ],
            'message' => '조회 완료'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'public' => 'required|boolean',
            'order' => 'required|boolean',
            'category_id' => 'nullable|string',
            'title' => 'required|string',
            'caption' => 'nullable|string', 
            'member_type' => 'nullable|json',
        ]);

        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();
        $data['user_id'] = 1;
        $data['category_id'] = $request->category ?? 1;


        $post = Form::create($data);

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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {   
            $data = Form::findOrFail($id);
            return new FormResource($data);
            
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
        $post = Form::findOrFail($id);
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
        $post = Form::findOrFail($id);
        $post->forceDelete();
        return response()->json([
            'success' => true,
            'message' => '삭제 완료'
        ], 200);
    }
}
