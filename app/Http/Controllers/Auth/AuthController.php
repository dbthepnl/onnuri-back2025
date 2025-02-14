<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cardinal;
use App\Models\FormCheck;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return new UserResource(Auth::user());
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'boolean'
        ]);

        $user = User::where('email', $data['email'])->first();

        $credentials = [
            'email' => $data['email'],
            'password' => $data['password']
        ];

        if(!Auth::attempt($credentials)) {

            throw ValidationException::withMessages([
                'email' => 'Invalid credentials'
            ]);
           
        }

        session()->regenerate();
        $token = $user->createToken('auth_token')->plainTextToken;
        $parts = explode('|', $token);
        $token = $parts[1];

        return response()->json([
            'success' => true,
            'message' => '로그인완료',
            'token' => $token,
            'user' => Auth::user()
        ], 200);

    }

    public function profile(Request $request) {

        $user = User::findOrFail(Auth::user()->id);

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

    public function logout(Request $request)
    {

        Auth::user()->tokens->each(function ($token) {
            $token->delete();
        });
        auth()->guard('web')->logout();
        session()->invalidate();
        session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => '로그아웃',
        ], 200);

    }
    

    public function userId(Request $request){
        $data = User::where('username', $request->username)->first();

        return response()->json([
            'success' => $data ? false : true,
            'message' => $data ? '이미 존재합니다' : '사용가능합니다',
        ], 200);
    }

    public function register(Request $request)
    {
   
        $message = NULL;
        $data = $request->validate([
            'username' => 'required|string|max:16',
            'phone' => 'required|numeric|digits_between:10,11',
            'name' => 'required|max:255',
            'birth' => 'required|numeric|digits:8',
            'birth_type' => 'numeric|nullable',
            'address' => 'nullable|max:255',
            'detail_address' => 'nullable|max:255',
            'zip_code' => 'nullable|numeric|digits:5',
            'email' => 'required|string|email|max:255',
            'homepage' => 'nullable|max:255',
            'officers' => 'numeric|nullable',
            'password' => 'required|min:6|confirmed'
        ]);

        if(User::where('phone', $data['phone'])->first()) {
            $message['phone'] = '연락처 이미 존재합니다';
        }

        if(User::where('email', $data['email'])->first()) {
            $message['email'] = '이메일이 이미 존재합니다.';
        }

         if(User::where('username', $data['username'])->first()) {
            $message['username'] = '사용자ID가 이미 존재합니다.';
        } 

        if($message) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 200);
        }

        $user = User::create($data);

        if($request->hasFile('my_profile_photo')){
            $user->addMedia($request->file('my_profile_photo'))->toMediaCollection('my_profile_photo', 's3');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => '가입 완료',
            'token' => $token,
            'user' => $user
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    public function history(Request $request) {
        $formChecks = FormCheck::leftJoin('cardinals', 'form_checks.cardinal_id', '=', 'cardinals.id')
        ->where('form_checks.user_id', Auth::user()->id)
        ->selectRaw('
            form_checks.board_id, 
            form_checks.cardinal_id, 
            MAX(form_checks.step_id) as max_step_id, 
            cardinals.title, 
            MAX(form_checks.success) as success' 
        )
        ->groupBy('form_checks.board_id', 'form_checks.cardinal_id', 'cardinals.title') 
        ->orderBy('form_checks.board_id', 'asc') 
        ->get();
    
    return $formChecks;
    
    }

    public function passwordReset(Request $request) {

        $data = $request->validate([
            'password' => 'required|min:6|confirmed'
        ]);


        $password = Hash::make($data['password']);
        User::where('id', Auth::user()->id)->update([
            'password' => $password
        ]);
        return response()->json([
            'success' => true,
            'message' => '비밀번호 변경 완료',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail(Auth::user()->id);
        $record = NULL;
       if($user['email'] == $request->email) {
        $record = $request->except('email');
        $user->update($record);

       } else {
            $user->update($request->all());
       }
    
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
