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
            return $this->handleStep1($request, $tableName, $post);
        }

        //STEP 2
        if($request->step_id == 2) {
            return $this->handleStep2($request, $tableName, $post);            
        }

        //STEP 3
        if($request->step_id == 3) {
            return $this->handleStep3($request, $tableName, $post);
        }

        //STEP 4
        if($request->step_id == 4) {
            return $this->handleStep4($request, $tableName, $post);            
        }

        //STEP 5
        if($request->step_id == 5) {
            return $this->handleStep5($request, $tableName, $post);            
        }

        //STEP 6
        if($request->step_id == 6) {
            return $this->handleStep6($request, $tableName, $post);            
        }

        //STEP 7
        if($request->step_id == 7) {
            return $this->handleStep7($request, $tableName, $post);            
        }

        //STEP 8
        if($request->step_id == 8) {
            return $this->handleStep8($request, $tableName, $post);            
        }

        //STEP 9
        if($request->step_id == 9) {
            return $this->handleStep9($request, $tableName, $post);            
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

    private function handleStep1($request, $tableName, $post) {
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

    private function handleStep2($request, $tableName, $post) {
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

    private function handleStep3($request, $tableName, $post) {
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

    private function handleStep4($request, $tableName, $post) {
        if($post) {
            $family_info = json_encode($request->input('family_info'));  

            $record = $request->except('step_id');
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['family_info'] = $family_info;

            $data = 
            DB::table($tableName)
            ->where('board_id', $request->board_id)
            ->where('cardinal_id', $request->cardinal_id)
            ->where('user_id', Auth::user()->id)
            ->update($record);

            return response()->json(['result' => true, 'data' => $data, 'message' => '수정 완료']);

        } else {
            $family_info = json_encode($request->input('family_info'));  

            $record = $request->except('step_id');
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['family_info'] = $family_info;
            $data = DB::table($tableName)->insert($record);

            return response()->json(['result' => true, 'data' => $data, 'message' => '등록 완료']);
        }
    }


    private function handleStep5($request, $tableName, $post) {
        if($post) {
            $health_info = json_encode($request->input('health_info'));  

            $record = $request->except('step_id');
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['health_info'] = $health_info;

            $data = 
            DB::table($tableName)
            ->where('board_id', $request->board_id)
            ->where('cardinal_id', $request->cardinal_id)
            ->where('user_id', Auth::user()->id)
            ->update($record);

            return response()->json(['result' => true, 'data' => $data, 'message' => '수정 완료']);

        } else {
            $health_info = json_encode($request->input('health_info'));  

            $record = $request->except('step_id');
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['health_info'] = $health_info;
            $data = DB::table($tableName)->insert($record);

            return response()->json(['result' => true, 'data' => $data, 'message' => '등록 완료']);
        }
    }

    private function handleStep6($request, $tableName, $post) {
        if($post) {
            $recommend_info1 = json_encode($request->input('recommend_info1'));  
            $recommend_info2 = json_encode($request->input('recommend_info2'));  
            $recommend_info3 = json_encode($request->input('recommend_info3'));  
            $recommend_info4 = json_encode($request->input('recommend_info4'));  
            $recommend_info5 = json_encode($request->input('recommend_info5'));  

            $record = $request->except('step_id');
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['recommend_info1'] = $recommend_info1;
            $record['recommend_info2'] = $recommend_info2;
            $record['recommend_info3'] = $recommend_info3;
            $record['recommend_info4'] = $recommend_info4;
            $record['recommend_info5'] = $recommend_info5;


            $data = 
            DB::table($tableName)
            ->where('board_id', $request->board_id)
            ->where('cardinal_id', $request->cardinal_id)
            ->where('user_id', Auth::user()->id)
            ->update($record);

            return response()->json(['result' => true, 'data' => $data, 'message' => '수정 완료']);

        } else {
            $recommend_info1 = json_encode($request->input('recommend_info1'));  
            $recommend_info2 = json_encode($request->input('recommend_info2'));  
            $recommend_info3 = json_encode($request->input('recommend_info3'));  
            $recommend_info4 = json_encode($request->input('recommend_info4'));  
            $recommend_info5 = json_encode($request->input('recommend_info5'));  

            $record = $request->except('step_id');
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['recommend_info1'] = $recommend_info1;
            $record['recommend_info2'] = $recommend_info2;
            $record['recommend_info3'] = $recommend_info3;
            $record['recommend_info4'] = $recommend_info4;
            $record['recommend_info5'] = $recommend_info5;
            $data = DB::table($tableName)->insert($record);

            return response()->json(['result' => true, 'data' => $data, 'message' => '등록 완료']);
        }
    }

    private function handleStep7($request, $tableName, $post) {
        if($post) {
            $testimony_info = json_encode($request->input('testimony_info'));  

            $record = $request->except('step_id');
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['testimony_info'] = $testimony_info;


            $data = 
            DB::table($tableName)
            ->where('board_id', $request->board_id)
            ->where('cardinal_id', $request->cardinal_id)
            ->where('user_id', Auth::user()->id)
            ->update($record);

            return response()->json(['result' => true, 'data' => $data, 'message' => '수정 완료']);

        } else {
            $testimony_info = json_encode($request->input('testimony_info'));  

            $record = $request->except('step_id');
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['testimony_info'] = $testimony_info;
            $data = DB::table($tableName)->insert($record);

            return response()->json(['result' => true, 'data' => $data, 'message' => '등록 완료']);
        }
    }

    private function handleStep8($request, $tableName, $post) {
        if($post) {
            $vision_info = json_encode($request->input('vision_info'));  

            $record = $request->except('step_id');
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['vision_info'] = $vision_info;


            $data = 
            DB::table($tableName)
            ->where('board_id', $request->board_id)
            ->where('cardinal_id', $request->cardinal_id)
            ->where('user_id', Auth::user()->id)
            ->update($record);

            return response()->json(['result' => true, 'data' => $data, 'message' => '수정 완료']);

        } else {
            $vision_info = json_encode($request->input('vision_info'));  

            $record = $request->except('step_id');
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['vision_info'] = $vision_info;
            $data = DB::table($tableName)->insert($record);

            return response()->json(['result' => true, 'data' => $data, 'message' => '등록 완료']);
        }
    }

    private function handleStep9($request, $tableName, $post) {
        if($post) {
            $infoOne = json_encode($request->input('info1'));  

            $record = $request->except('step_id');
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            if($request->input('info1')) {
                $infoOne = json_encode($request->input('info1'));  
                $record['info1'] = $infoOne;
                $data = 
                DB::table($tableName)
                ->where('board_id', $request->board_id)
                ->where('cardinal_id', $request->cardinal_id)
                ->where('user_id', Auth::user()->id)
                ->update($record);
            } 

            if($request->input('info2')) {
                $infoTwo = json_encode($request->input('info2'));  
                $record['info2'] = $infoTwo;
                $data = 
                DB::table($tableName)
                ->where('board_id', $request->board_id)
                ->where('cardinal_id', $request->cardinal_id)
                ->where('user_id', Auth::user()->id)
                ->update($record);
            } 

            if($request->input('info3')) {
                $infoThree = json_encode($request->input('info3'));  
                $record['info3'] = $infoThree;
                $data = 
                DB::table($tableName)
                ->where('board_id', $request->board_id)
                ->where('cardinal_id', $request->cardinal_id)
                ->where('user_id', Auth::user()->id)
                ->update($record);
            } 

            if($request->input('info4')) {
                $infoFour = json_encode($request->input('info4'));  
                $record['info4'] = $infoFour;
                $data = 
                DB::table($tableName)
                ->where('board_id', $request->board_id)
                ->where('cardinal_id', $request->cardinal_id)
                ->where('user_id', Auth::user()->id)
                ->update($record);
            } 

            if($request->input('info5')) {
                $infoFive = json_encode($request->input('info5'));  
                $record['info5'] = $infoFive;
                $data = 
                DB::table($tableName)
                ->where('board_id', $request->board_id)
                ->where('cardinal_id', $request->cardinal_id)
                ->where('user_id', Auth::user()->id)
                ->update($record);
            } 

            if($request->input('info6')) {
                $infoSix = json_encode($request->input('info6'));  
                $record['info6'] = $infoSix;
                $data = 
                DB::table($tableName)
                ->where('board_id', $request->board_id)
                ->where('cardinal_id', $request->cardinal_id)
                ->where('user_id', Auth::user()->id)
                ->update($record);
            } 

            if($request->input('info7')) {
                $infoSeven = json_encode($request->input('info7'));  
                $record['info7'] = $infoSeven;
                $data = 
                DB::table($tableName)
                ->where('board_id', $request->board_id)
                ->where('cardinal_id', $request->cardinal_id)
                ->where('user_id', Auth::user()->id)
                ->update($record);
            } 
        

            return response()->json(['result' => true, 'data' => $data, 'message' => '수정 완료']);

        } else {
            if($request->input('info1')) {
                $infoOne = json_encode($request->input('info1'));  
                $record = $request->except('step_id');
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $record['info1'] = $infoOne;
                $data = DB::table($tableName)->insert($record);
            }

            if($request->input('info2')) {
                $infoTwo = json_encode($request->input('info2'));  
                $record = $request->except('step_id');
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $record['info2'] = $infoTwo;
                $data = DB::table($tableName)->insert($record);
            }

            if($request->input('info3')) {
                $infoThree = json_encode($request->input('info3'));  
                $record = $request->except('step_id');
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $record['info3'] = $infoThree;
                $data = DB::table($tableName)->insert($record);
            }

            if($request->input('info4')) {
                $infoFour = json_encode($request->input('info4'));  
                $record = $request->except('step_id');
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $record['info4'] = $infoFour;
                $data = DB::table($tableName)->insert($record);
            } 

            if($request->input('info5')) {
                $infoFive = json_encode($request->input('info5'));  
                $record = $request->except('step_id');
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $record['info5'] = $infoFive;
                $data = DB::table($tableName)->insert($record);
            } 

            if($request->input('info6')) {
                $infoSix = json_encode($request->input('info6'));  
                $record = $request->except('step_id');
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $record['info6'] = $infoSix;
                $data = DB::table($tableName)->insert($record);
            } 

            if($request->input('info7')) {
                $infoSeven = json_encode($request->input('info7'));  
                $record = $request->except('step_id');
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $record['info7'] = $infoSeven;
                $data = DB::table($tableName)->insert($record);
            } 

            return response()->json(['result' => true, 'data' => $data, 'message' => '등록 완료']);
        }
    }
}
