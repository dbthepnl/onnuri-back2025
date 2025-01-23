<?php

namespace App\Http\Controllers;
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
use App\Models\Form7;
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

        $tableName = $request->step_id ? "form" . $request->step_id . 's' : 'forms';
        $data = DB::table($tableName)
        ->where('user_id', Auth::user()->id)
        ->where('board_id', $request->board_id)
        ->where('cardinal_id', $request->cardinal_id)
        ->get();

        return response()->json(['result' => true, 'data' => $data, 'message' => $request->id ? '데이터 전송' : '데이터 없음']);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $tableName = $request->step_id ? "form" . $request->step_id . 's' : 'forms';
        $record = $request->all();
        $post = DB::table($tableName)
        ->where('board_id', $request->board_id)
        ->where('cardinal_id', $request->cardinal_id)
        ->where('user_id', Auth::user()->id)
        ->first();

        //STEP 1
        if($request->step_id == 1) {
            if($post) {
                $record = $request->except('step_id');
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $data = 
                DB::table($tableName)
                ->where('board_id', $request->board_id)
                ->where('cardinal_id', $request->cardinal_id)
                ->where('user_id', Auth::user()->id)
                ->update($record);
                return response()->json(['result' => true, 'data' => $data, 'message' => '수정 완료']);
            } else {
                $record = $request->except('step_id');
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $data = DB::table($tableName)->insert($record);
                return response()->json(['result' => true, 'data' => $data, 'message' => '등록 완료']);
            }
        }

        //STEP 2
        if($request->step_id == 2) {
            if($post) {
                $school_info = json_encode($request->input('school_info'));  
                $language_info = json_encode($request->input('language_info'));  
                $personal_info = json_encode($request->input('personal_info')); 
                $technician_info = json_encode($request->input('technician_info'));  
                $military_info = json_encode($request->input('military_info')); 

                $record = $request->except('step_id');
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $record['school_info'] = $school_info;
                $record['language_info'] = $language_info;
                $record['personal_info'] = $personal_info;
                $record['technician_info'] = $technician_info;
                $record['military_info'] = $$military_info;

                $data = 
                DB::table($tableName)
                ->where('board_id', $request->board_id)
                ->where('cardinal_id', $request->cardinal_id)
                ->where('user_id', Auth::user()->id)
                ->update($record);
                return response()->json(['result' => true, 'data' => $data, 'message' => '수정 완료']);
            } else {
                $school_info = json_encode($request->input('school_info'));  
                $language_info = json_encode($request->input('language_info'));  
                $personal_info = json_encode($request->input('personal_info')); 
                $technician_info = json_encode($request->input('technician_info'));  
                $military_info = json_encode($request->input('military_info')); 

                $record = $request->except('step_id');
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $record['school_info'] = $school_info;
                $record['language_info'] = $language_info;
                $record['personal_info'] = $personal_info;
                $record['technician_info'] = $technician_info;
                $record['military_info'] = $military_info;

    
                $data = DB::table($tableName)->insert($record);
                return response()->json(['result' => true, 'data' => $data, 'message' => '등록 완료']);
            }
        }

        //STEP 3
        if($request->step_id == 3) {
            if($post) {
                $church_info = json_encode($request->input('church_info'));  
                $work_info = json_encode($request->input('work_info'));  
                $disciple_info = json_encode($request->input('disciple_info')); 
                $faith_info = json_encode($request->input('faith_info'));  
                $mission_info = json_encode($request->input('mission_info')); 
                $dispatch_info = json_encode($request->input('dispatch_info')); 

                $record = $request->except('step_id');
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $record['church_info'] = $church_info;
                $record['work_info'] = $work_info;
                $record['disciple_info'] = $disciple_info;
                $record['faith_info'] = $faith_info;
                $record['mission_info'] = $mission_info;
                $record['dispatch_info'] = $dispatch_info;

                $data = 
                DB::table($tableName)
                ->where('board_id', $request->board_id)
                ->where('cardinal_id', $request->cardinal_id)
                ->where('user_id', Auth::user()->id)
                ->update($record);
                return response()->json(['result' => true, 'data' => $data, 'message' => '수정 완료']);
            } else {
                $church_info = json_encode($request->input('church_info'));  
                $work_info = json_encode($request->input('work_info'));  
                $disciple_info = json_encode($request->input('disciple_info')); 
                $faith_info = json_encode($request->input('faith_info'));  
                $mission_info = json_encode($request->input('mission_info')); 
                $dispatch_info = json_encode($request->input('dispatch_info')); 

                $record = $request->except('step_id');
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $record['church_info'] = $church_info;
                $record['work_info'] = $work_info;
                $record['disciple_info'] = $disciple_info;
                $record['faith_info'] = $faith_info;
                $record['mission_info'] = $mission_info;
                $record['dispatch_info'] = $dispatch_info;
    
                $data = DB::table($tableName)->insert($record);
                return response()->json(['result' => true, 'data' => $data, 'message' => '등록 완료']);
            }
        }

        //STEP 4
        if($request->step_id == 4) {
            if($post) {
                $church_info = json_encode($request->input('church_info'));  
                $work_info = json_encode($request->input('work_info'));  
                $disciple_info = json_encode($request->input('disciple_info')); 
                $faith_info = json_encode($request->input('faith_info'));  
                $mission_info = json_encode($request->input('mission_info')); 
                $dispatch_info = json_encode($request->input('dispatch_info')); 

                $record = $request->except('step_id');
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $record['church_info'] = $church_info;
                $record['work_info'] = $work_info;
                $record['disciple_info'] = $disciple_info;
                $record['faith_info'] = $faith_info;
                $record['mission_info'] = $mission_info;
                $record['dispatch_info'] = $dispatch_info;

                $data = 
                DB::table($tableName)
                ->where('board_id', $request->board_id)
                ->where('cardinal_id', $request->cardinal_id)
                ->where('user_id', Auth::user()->id)
                ->update($record);
                return response()->json(['result' => true, 'data' => $data, 'message' => '수정 완료']);
            } else {
                $church_info = json_encode($request->input('church_info'));  
                $work_info = json_encode($request->input('work_info'));  
                $disciple_info = json_encode($request->input('disciple_info')); 
                $faith_info = json_encode($request->input('faith_info'));  
                $mission_info = json_encode($request->input('mission_info')); 
                $dispatch_info = json_encode($request->input('dispatch_info')); 

                $record = $request->except('step_id');
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $record['church_info'] = $church_info;
                $record['work_info'] = $work_info;
                $record['disciple_info'] = $disciple_info;
                $record['faith_info'] = $faith_info;
                $record['mission_info'] = $mission_info;
                $record['dispatch_info'] = $dispatch_info;
    
                $data = DB::table($tableName)->insert($record);
                return response()->json(['result' => true, 'data' => $data, 'message' => '등록 완료']);
            }
        }

        return response()->json(['result' => false, 'data' => $data, 'message' => '데이터 등록 실패']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return 'show';
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return 'update';
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return 'destroy';
    }
}
