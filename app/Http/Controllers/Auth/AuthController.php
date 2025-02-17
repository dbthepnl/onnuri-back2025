<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cardinal;
use App\Models\FormCheck;
use App\Models\PasswordReset;
use Illuminate\Support\Str;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
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

    //forget password
    public function forgetPassword(Request $request)
    {
        try{

            $user = User::where('email', $request->email)->get();

            if(count($user) > 0){
                $token = Str::random(6);
                $domain = URL::to('/api/');
                $url = $domain.'/reset-password?token='.$token;
                $data['url'] = $url;
                $data['email'] = $request->email;
                $data['title'] = "Password Reset";
                $data['token'] = $token;

                Mail::send('forgetPasswordMail', ['data'=>$data], function($message) use ($data){
                    $message->to($data['email'])->subject($data['title']);
                });

                $datetime = Carbon::now()->format('Y-m-d H:i:s');

                PasswordReset::updateOrCreate(
                    ['email' => $request->email],
                    [
                        'email' => $request->email,
                        'token' => $token,
                        'created_at' => $datetime
                    ]
                    );
                return response()->json([
                    'success' => true,
                    'msg'=>'아래 코드 입력',
                    'code'=> $token
                ]);

            } else {

                return response()->json(['success'=>false,'msg'=>'Enter the wrong email address!']);
            }

        } catch (\Exception $e) {
            return response()->json(['success'=>false, 'msg'=>$e->getMessage()]);
        }
    }

    public function emailVerify(Request $request){
        try{      

            if($request->token) {
                $resetData = PasswordReset::where('token', $request->token)->get();
                    if(isset($request->token) && count($resetData)>0){
                        return response()->json(['success'=>true,'message'=>'성공']); //add input certify
                    
                    } else {
                        return response()->json(['success'=>false,'message'=>'코드']);
                    }

            }
            
            
            $email = User::where('email', $request->email)->first();
            
            if($email) {
                return response()->json(['success'=>false,'message'=>'Email is already used. Try again.']);
            }

            $token = Str::random(6);
            $domain = URL::to('/api/');
            $url = $domain.'/email-vertify?token='.$token;
            $data['url'] = $url;
            $data['email'] = $request->email;
            $data['title'] = "Email Certify";
            $data['token'] = $token;

            Mail::send('EmailVerification', ['data'=>$data], function($message) use ($data){
                $message->to($data['email'])->subject($data['title']);
            });

                $datetime = Carbon::now()->format('Y-m-d H:i:s');

                PasswordReset::updateOrCreate(
                    ['email' => $request->email],
                    [
                        'email' => $request->email,
                        'token' => $token,
                        'created_at' => $datetime
                    ]
                    );
                return response()->json([
                    'success' => true,
                    'message'=>'Token is sent to email.',
                    'code'=> $token
                ]);

        } catch (\Exception $e) {
            return response()->json(['success'=>false, 'message'=>$e->getMessage()]);
        }
    }

    public function resetPasswordLoad(Request $request)
    {
        $resetData = PasswordReset::where('token', $request->token)->get();
        if(isset($request->token) && count($resetData)>0){

            $user = User::where('email', $resetData[0]['email'])->get();
            return response()->json(['success'=>true,'msg'=>'User Found!']); //add input password in view

        } else {
            return response()->json(['success'=>false,'msg'=>'Enter the wrong code. Try again.']);
        }

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
        if(Auth::user()->role != 0){
            $permission = DB::table('role_has_permissions')->where('role_id', Auth::user()->role)->get();
        }
        $parts = explode('|', $token);
        $token = $parts[1];

        return response()->json([
            'success' => true,
            'message' => '로그인완료',
            'token' => $token,
            'permission' => $permission ?? NULL,
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

    public function passwordResetToken(Request $request) {


        $reset = PasswordReset::where('token', $request->token)->first();

        if($reset) {
            $password = Hash::make($request->password);
            User::where('email', $reset['email'])->update([
                'password' => $password
            ]);

            return response()->json([
                'success' => true,
                'message' => '비밀번호 변경 완료',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => '에러',
            ], 200);
        }
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
