<?php

namespace App\Http\Controllers\Admin;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\UserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $users = QueryBuilder::for(User::class)
            ->allowedFilters([
                "name", //이름 검색
                "username", //user ID 검색
                AllowedFilter::callback('search', function ($query, $value) { //전체 검색
                    $query->where(function ($query) use ($value) {
                        $query->where('name', 'like', "%$value%")
                              ->orWhere('email', 'like', "%$value%");
                    });
                }),
            ])
            ->allowedSorts(['id', 'name', 'email'])
            ->paginate(15);

        return new UserCollection($users);
        
    }   

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:16|unique:users',
            'phone' => 'required|numeric|digits_between:10,11',
            'name' => 'required|max:255',
            'birth' => 'required|numeric|digits:8',
            'birth_type' => 'numeric|nullable',
            'address' => 'nullable|max:255',
            'detail_address' => 'nullable|max:255',
            'zip_code' => 'nullable|numeric|digits:5',
            'email' => 'required|string|email|max:255|unique:users',
            'homepage' => 'nullable|max:255',
            'officers' => 'numeric|nullable',
            'password' => 'required|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => '데이터 검증 실패',
                'errors' => $validator->errors()
            ], 422); 
        }

        try {   
            $user = User::create($validator->validated());
            if($request->hasFile('my_profile_photo')){
                $user->addMedia($request->file('my_profile_photo'))->toMediaCollection('my_profile_photo', 's3');
            }

            return response()->json([
                'success' => true,
                'message' => '등록 완료'
            ], 200);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => '데이터 검증 실패',
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $users = User::findOrFail($id);
        return new UserResource($users);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    $user = User::findOrFail($id);
    $user->update($request->all());
    
    return response()->json([
        'success' => true,
        'message' => '업데이트 완료'
    ], 200);
    
}

    public function profile(Request $request, string $id) {
        $user = User::findOrFail($id);

        //이미지 삭제한 경우
        if($request->has('my_profile_photo') && $request->status == 'delete'){
            $user->clearMediaCollection('my_profile_photo');
        }
        //이미지 업데이트한 경우
        if($request->hasFile('my_profile_photo') && $request->status == 'update'){
            $user->clearMediaCollection('my_profile_photo');
            $user->addMedia($request->file('my_profile_photo'))->toMediaCollection('my_profile_photo', 's3');
        }

        //이미지 추가한 경우
        if($request->hasFile('my_profile_photo') && $request->status == 'add'){
            $user->addMedia($request->file('my_profile_photo'))->toMediaCollection('my_profile_photo', 's3');
        }

        //이미지 기존걸 유지한 경우
        if(!$request->hasFile('my_profile_photo') && $request->status == 'keep'){
            $user->clearMediaCollection('my_profile_photo');
        }


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
        $user = User::findOrFail($id);
        $user->forceDelete();
        return response()->json([
            'success' => true,
            'message' => '삭제 완료'
        ], 200);
    }
}
