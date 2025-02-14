<?php

namespace App\Http\Controllers;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\FormCheck;
use App\Models\Form;
use App\Models\Form1;
use App\Models\Form2;
use App\Models\Form3;
use App\Models\Form4;
use App\Models\Form5;
use App\Models\Form6;
use App\Models\Form7;
use App\Models\Form8;
use App\Models\Form9;
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

        if($request->step_id == 1) {
            $data = Form1::where('user_id', Auth::user()->id)
            ->where('board_id', $request->board_id)
            ->where('cardinal_id', $request->cardinal_id)
            ->get();
        }
        if($request->step_id == 2) {
            $data = Form2::where('user_id', Auth::user()->id)
        ->where('board_id', $request->board_id)
        ->where('cardinal_id', $request->cardinal_id)
        ->get();
        }
        if($request->step_id == 3) {
            $data = Form3::where('user_id', Auth::user()->id)
        ->where('board_id', $request->board_id)
        ->where('cardinal_id', $request->cardinal_id)
        ->get();
        }
        if($request->step_id == 4) {
            $data = Form4::where('user_id', Auth::user()->id)
        ->where('board_id', $request->board_id)
        ->where('cardinal_id', $request->cardinal_id)
        ->get();
        }
        if($request->step_id == 5) {
            $data = Form5::where('user_id', Auth::user()->id)
        ->where('board_id', $request->board_id)
        ->where('cardinal_id', $request->cardinal_id)
        ->get();
        }
        if($request->step_id == 6) {
            $data = Form6::where('user_id', Auth::user()->id)
        ->where('board_id', $request->board_id)
        ->where('cardinal_id', $request->cardinal_id)
        ->get();
        }
        if($request->step_id == 7) {
            $data = Form7::where('user_id', Auth::user()->id)
        ->where('board_id', $request->board_id)
        ->where('cardinal_id', $request->cardinal_id)
        ->get();
        }
        if($request->step_id == 8) {
            $data = Form8::where('user_id', Auth::user()->id)
        ->where('board_id', $request->board_id)
        ->where('cardinal_id', $request->cardinal_id)
        ->get();
        }
        if($request->step_id == 9) {
            $data = Form9::where('user_id', Auth::user()->id)
        ->where('board_id', $request->board_id)
        ->where('cardinal_id', $request->cardinal_id)
        ->get(); }

        return response()->json([
            'result' => true,
            'step_id' => $request->step_id,
            'data' => $data,
            'message' => '조회 완료'
        ]);

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
            $success = $request->success ?? 0;
            $record = $request->except('success');
            return $this->handleStep1($request, $tableName, $post, $success);
        }

        //STEP 2
        if($request->step_id == 2) {
            $success = $request->success ?? 0;
            $record = $request->except('success');
            return $this->handleStep2($request, $tableName, $post, $success);           
        }

        //STEP 3
        if($request->step_id == 3) {
            $success = $request->success ?? 0;
            $record = $request->except('success');
            return $this->handleStep3($request, $tableName, $post, $success);
        }

        //STEP 4
        if($request->step_id == 4) {
            $success = $request->success ?? 0;
            $record = $request->except('success');
            return $this->handleStep4($request, $tableName, $post, $success);            
        }

        //STEP 5
        if($request->step_id == 5) {
            $success = $request->success ?? 0;
            $record = $request->except('success');
            return $this->handleStep5($request, $tableName, $post, $success);          
        }

        //STEP 6
        if($request->step_id == 6) {
            $success = $request->success ?? 0;
            $record = $request->except('success');
            return $this->handleStep6($request, $tableName, $post, $success);       
        }

        //STEP 7
        if($request->step_id == 7) {
            $success = $request->success;
            $record = $request->except('success');
            return $this->handleStep7($request, $tableName, $post, $success);   
        }

        //STEP 8
        if($request->step_id == 8) {
            $success = $request->success ?? 0;
            $record = $request->except('success');
            return $this->handleStep8($request, $tableName, $post, $success);     
        }

        //STEP 9
        if($request->step_id == 9) {
            $success = $request->success;
            $record = $request->except('success');
            return $this->handleStep9($request, $tableName, $post, $success);      
        }

        return response()->json(['result' => false, 'data' => NULL, 'message' => '데이터 등록 실패']);
    }

    //신청자 관리
    public function registerShow(Request $request, $id)
    {
        $form1 = Form1::where('user_id', Auth::user()->id)
        ->where('board_id', $request->board_id)
        ->where('cardinal_id', $request->cardinal_id)
        ->get();
        $form2 = Form2::where('user_id', Auth::user()->id)
        ->where('board_id', $request->board_id)
        ->where('cardinal_id', $request->cardinal_id)
        ->get();
        $form3 = Form3::where('user_id', Auth::user()->id)
        ->where('board_id', $request->board_id)
        ->where('cardinal_id', $request->cardinal_id)
        ->get();
        $form4 = Form4::where('user_id', Auth::user()->id)
        ->where('board_id', $request->board_id)
        ->where('cardinal_id', $request->cardinal_id)
        ->get();
        $form5 = Form5::where('user_id', Auth::user()->id)
        ->where('board_id', $request->board_id)
        ->where('cardinal_id', $request->cardinal_id)
        ->get();
        $form6 = Form6::where('user_id', Auth::user()->id)
        ->where('board_id', $request->board_id)
        ->where('cardinal_id', $request->cardinal_id)
        ->get();
        $form7 = Form7::where('user_id', Auth::user()->id)
        ->where('board_id', $request->board_id)
        ->where('cardinal_id', $request->cardinal_id)
        ->get();
        $form8 = Form8::where('user_id', Auth::user()->id)
        ->where('board_id', $request->board_id)
        ->where('cardinal_id', $request->cardinal_id)
        ->get();
        $form9 = Form9::where('user_id', Auth::user()->id)
        ->where('board_id', $request->board_id)
        ->where('cardinal_id', $request->cardinal_id)
        ->get();


        return response()->json([
            'result' => true,
            'data' =>
            [
                'form1' => $form1,
                'form2' => $form2,
                'form3' => $form3,
                'form4' => $form4,
                'form5' => $form5,
                'form6' => $form6,
                'form7' => $form7,
                'form8' => $form8,
                'form9' => $form9
            ],
            'message' => '조회 완료'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $form1 = Form1::where('user_id', $id)->get();
        $form2 = Form2::where('user_id', $id)->get();
        $form3 = Form3::where('user_id', $id)->get();
        $form4 = Form4::where('user_id', $id)->get();
        $form5 = Form5::where('user_id', $id)->get();
        $form6 = Form6::where('user_id', $id)->get();
        $form7 = Form7::where('user_id', $id)->get();
        $form8 = Form8::where('user_id', $id)->get();
        $form9 = Form9::where('user_id', $id)->get();


        return response()->json([
            'result' => true,
            'data' =>
            [
                'form1' => $form1,
                'form2' => $form2,
                'form3' => $form3,
                'form4' => $form4,
                'form5' => $form5,
                'form6' => $form6,
                'form7' => $form7,
                'form8' => $form8,
                'form9' => $form9
            ],
            'message' => '조회 완료'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function formCheck(Request $request)
    {

        $data = DB::table('form_checks')
        ->selectRaw('step_id, success')
        ->where('user_id', Auth::user()->id)
        ->where('board_id', $request->board_id)
        ->where('cardinal_id', $request->cardinal_id)
        ->get();

        $exist = DB::table('cardinals')
        ->where('board_id', $request->board_id)
        ->where('cardinal_number', $request->cardinal_number)
        ->first();

        $requiredSteps = [1,2,3,4,5,6,7,8,9]; //몇개 step있는지 확인


        if ($data->isEmpty()) {
            foreach ($requiredSteps as $step) {
                $data->push((object)[
                    'step_id' => $step,
                    'success' => 0
                ]);
            }
        } else {
            foreach ($requiredSteps as $step) {
                $found = false;
    
                foreach ($data as $entry) {
                    if ($entry->step_id == $step) {
                        $found = true;
                        break;
                    }
                }
    
                if (!$found) {
                    $data->push((object)[
                        'step_id' => $step,
                        'success' => 0
                    ]);
                }
            }
        }
        
        return response()->json(['result' => $exist ? true : false, 'data' => $data, 'message' => '신청서 현황']);
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

    private function handleStep1($request, $tableName, $post, $success) {
        

        if($post) {
                $record = $request->except(['step_id', 'success']);
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $data = 
                DB::table($tableName)
                ->where('board_id', $request->board_id)
                ->where('cardinal_id', $request->cardinal_id)
                ->where('user_id', Auth::user()->id)
                ->update($record);
                
                FormCheck::updateOrCreate(
                    [
                        'user_id' => Auth::user()->id,
                        'board_id' => $request->board_id,
                        'cardinal_id' => $request->cardinal_id,
                        'step_id' => 1,
                    ],
                    [
                        'success' => $success,
                    ]
                );
                
                return response()->json(['result' => true, 'data' => $data, 'message' => '수정 완료']);
            } else {
                $record = $request->except(['step_id', 'success']);
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $data = DB::table($tableName)->insert($record);

                FormCheck::updateOrCreate(
                    [
                        'user_id' => Auth::user()->id,
                        'board_id' => $request->board_id,
                        'cardinal_id' => $request->cardinal_id,
                        'step_id' => 1,
                    ],
                    [
                        'success' => $success,
                    ]
                );

                return response()->json(['result' => true, 'data' => $data, 'message' => '등록 완료']);
            }
    }

    private function handleStep2($request, $tableName, $post, $success) {

        if($post) {
            $school_info = json_encode($request->input('school_info'));  
            $language_info = json_encode($request->input('language_info'));  
            $personal_info = json_encode($request->input('personal_info')); 
            $technician_info = json_encode($request->input('technician_info'));  
            $military_info = json_encode($request->input('military_info')); 


            $record = $request->except(['step_id', 'success']);
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['school_info'] = $school_info;
            $record['language_info'] = $language_info;
            $record['personal_info'] = $personal_info;
            $record['technician_info'] = $technician_info;
            $record['military_info'] = $military_info;

            $data = 
            DB::table($tableName)
            ->where('board_id', $request->board_id)
            ->where('cardinal_id', $request->cardinal_id)
            ->where('user_id', Auth::user()->id)
            ->update($record);

            FormCheck::updateOrCreate(
                [
                    'user_id' => Auth::user()->id,
                    'board_id' => $request->board_id,
                    'cardinal_id' => $request->cardinal_id,
                    'step_id' => 2,
                ],
                [
                    'success' => $success,
                ]
            );


            return response()->json(['result' => true, 'data' => $data, 'message' => '수정 완료']);
        } else {
            
            $school_info = json_encode($request->input('school_info'));  
            $language_info = json_encode($request->input('language_info'));  
            $personal_info = json_encode($request->input('personal_info')); 
            $technician_info = json_encode($request->input('technician_info'));  
            $military_info = json_encode($request->input('military_info')); 

            $record = $request->except(['step_id', 'success']);
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['school_info'] = $school_info;
            $record['language_info'] = $language_info;
            $record['personal_info'] = $personal_info;
            $record['technician_info'] = $technician_info;
            $record['military_info'] = $military_info;


            $data = DB::table($tableName)->insert($record);

            FormCheck::updateOrCreate(
                [
                    'user_id' => Auth::user()->id,
                    'board_id' => $request->board_id,
                    'cardinal_id' => $request->cardinal_id,
                    'step_id' => 2,
                ],
                [
                    'success' => $success,
                ]
            );

            return response()->json(['result' => true, 'data' => $data, 'message' => '등록 완료']);
        }
    }

    private function handleStep3($request, $tableName, $post, $success) {
        if($post) {
            $church_info = json_encode($request->input('church_info'));  
            $work_info = json_encode($request->input('work_info'));  
            $disciple_info = json_encode($request->input('disciple_info')); 
            $faith_info = json_encode($request->input('faith_info'));  
            $mission_info = json_encode($request->input('mission_info')); 
            $dispatch_info = json_encode($request->input('dispatch_info')); 

            $record = $request->except(['step_id', 'success']);
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

            FormCheck::updateOrCreate(
                [
                    'user_id' => Auth::user()->id,
                    'board_id' => $request->board_id,
                    'cardinal_id' => $request->cardinal_id,
                    'step_id' => 3,
                ],
                [
                    'success' => $success,
                ]
            );

            return response()->json(['result' => true, 'data' => $data, 'message' => '수정 완료']);
        
        } else {
            $church_info = json_encode($request->input('church_info'));  
            $work_info = json_encode($request->input('work_info'));  
            $disciple_info = json_encode($request->input('disciple_info')); 
            $faith_info = json_encode($request->input('faith_info'));  
            $mission_info = json_encode($request->input('mission_info')); 
            $dispatch_info = json_encode($request->input('dispatch_info')); 

            $record = $request->except(['step_id', 'success']);
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['church_info'] = $church_info;
            $record['work_info'] = $work_info;
            $record['disciple_info'] = $disciple_info;
            $record['faith_info'] = $faith_info;
            $record['mission_info'] = $mission_info;
            $record['dispatch_info'] = $dispatch_info;

            $data = DB::table($tableName)->insert($record);

            FormCheck::updateOrCreate(
                [
                    'user_id' => Auth::user()->id,
                    'board_id' => $request->board_id,
                    'cardinal_id' => $request->cardinal_id,
                    'step_id' => 3,
                ],
                [
                    'success' => $success,
                ]
            );


            return response()->json(['result' => true, 'data' => $data, 'message' => '등록 완료']);
        }
    }

    private function handleStep4($request, $tableName, $post, $success) {
        if($post) {
            $family_info = json_encode($request->input('family_info'));  

            $record = $request->except(['step_id', 'success']);
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['family_info'] = $family_info;

            $data = 
            DB::table($tableName)
            ->where('board_id', $request->board_id)
            ->where('cardinal_id', $request->cardinal_id)
            ->where('user_id', Auth::user()->id)
            ->update($record);

            FormCheck::updateOrCreate(
                [
                    'user_id' => Auth::user()->id,
                    'board_id' => $request->board_id,
                    'cardinal_id' => $request->cardinal_id,
                    'step_id' => 4,
                ],
                [
                    'success' => $success,
                ]
            );

            return response()->json(['result' => true, 'data' => $data, 'message' => '수정 완료']);

        } else {
            $family_info = json_encode($request->input('family_info'));  

            $record = $request->except(['step_id', 'success']);
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['family_info'] = $family_info;
            $data = DB::table($tableName)->insert($record);

            FormCheck::updateOrCreate(
                [
                    'user_id' => Auth::user()->id,
                    'board_id' => $request->board_id,
                    'cardinal_id' => $request->cardinal_id,
                    'step_id' => 4,
                ],
                [
                    'success' => $success,
                ]
            );

            return response()->json(['result' => true, 'data' => $data, 'message' => '등록 완료']);
        }
    }


    private function handleStep5($request, $tableName, $post, $success) {
        if($post) {
            $health_info = json_encode($request->input('health_info'));  

            $record = $request->except(['step_id', 'success']);
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['health_info'] = $health_info;

            $data = 
            DB::table($tableName)
            ->where('board_id', $request->board_id)
            ->where('cardinal_id', $request->cardinal_id)
            ->where('user_id', Auth::user()->id)
            ->update($record);

            FormCheck::updateOrCreate(
                [
                    'user_id' => Auth::user()->id,
                    'board_id' => $request->board_id,
                    'cardinal_id' => $request->cardinal_id,
                    'step_id' => 5,
                ],
                [
                    'success' => $success,
                ]
            );

            return response()->json(['result' => true, 'data' => $data, 'message' => '수정 완료']);

        } else {
            $health_info = json_encode($request->input('health_info'));  

            $record = $request->except(['step_id', 'success']);
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['health_info'] = $health_info;
            $data = DB::table($tableName)->insert($record);

            FormCheck::updateOrCreate(
                [
                    'user_id' => Auth::user()->id,
                    'board_id' => $request->board_id,
                    'cardinal_id' => $request->cardinal_id,
                    'step_id' => 5,
                ],
                [
                    'success' => $success,
                ]
            );

            return response()->json(['result' => true, 'data' => $data, 'message' => '등록 완료']);
        }
    }

    private function handleStep6($request, $tableName, $post, $success) {
        if($post) {
            $recommend_info1 = json_encode($request->input('recommend_info1'));  
            $recommend_info2 = json_encode($request->input('recommend_info2'));  
            $recommend_info3 = json_encode($request->input('recommend_info3'));  
            $recommend_info4 = json_encode($request->input('recommend_info4'));  
            $recommend_info5 = json_encode($request->input('recommend_info5'));  

            $record = $request->except(['step_id', 'success']);
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

            FormCheck::updateOrCreate(
                [
                    'user_id' => Auth::user()->id,
                    'board_id' => $request->board_id,
                    'cardinal_id' => $request->cardinal_id,
                    'step_id' => 6,
                ],
                [
                    'success' => $success,
                ]
            );

            return response()->json(['result' => true, 'data' => $data, 'message' => '수정 완료']);

        } else {
            $recommend_info1 = json_encode($request->input('recommend_info1'));  
            $recommend_info2 = json_encode($request->input('recommend_info2'));  
            $recommend_info3 = json_encode($request->input('recommend_info3'));  
            $recommend_info4 = json_encode($request->input('recommend_info4'));  
            $recommend_info5 = json_encode($request->input('recommend_info5'));  

            $record = $request->except(['step_id', 'success']);
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['recommend_info1'] = $recommend_info1;
            $record['recommend_info2'] = $recommend_info2;
            $record['recommend_info3'] = $recommend_info3;
            $record['recommend_info4'] = $recommend_info4;
            $record['recommend_info5'] = $recommend_info5;
            $data = DB::table($tableName)->insert($record);

            FormCheck::updateOrCreate(
                [
                    'user_id' => Auth::user()->id,
                    'board_id' => $request->board_id,
                    'cardinal_id' => $request->cardinal_id,
                    'step_id' => 6,
                ],
                [
                    'success' => $success,
                ]
            );

            return response()->json(['result' => true, 'data' => $data, 'message' => '등록 완료']);
        }
    }

    private function handleStep7($request, $tableName, $post, $success) {
        if($post) {
            $testimony_info = json_encode($request->input('testimony_info'));  

            $record = $request->except(['step_id', 'success']);
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['testimony_info'] = $testimony_info;


            $data = 
            DB::table($tableName)
            ->where('board_id', $request->board_id)
            ->where('cardinal_id', $request->cardinal_id)
            ->where('user_id', Auth::user()->id)
            ->update($record);

            FormCheck::updateOrCreate(
                [
                    'user_id' => Auth::user()->id,
                    'board_id' => $request->board_id,
                    'cardinal_id' => $request->cardinal_id,
                    'step_id' => 7,
                ],
                [
                    'success' => $success,
                ]
            );

            return response()->json(['result' => true, 'data' => $data, 'message' => '수정 완료']);

        } else {
            $testimony_info = json_encode($request->input('testimony_info'));  

            $record = $request->except(['step_id', 'success']);
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['testimony_info'] = $testimony_info;
            $data = DB::table($tableName)->insert($record);

            FormCheck::updateOrCreate(
                [
                    'user_id' => Auth::user()->id,
                    'board_id' => $request->board_id,
                    'cardinal_id' => $request->cardinal_id,
                    'step_id' => 7,
                ],
                [
                    'success' => $success,
                ]
            );

            return response()->json(['result' => true, 'data' => $data, 'message' => '등록 완료']);
        }
    }

    private function handleStep8($request, $tableName, $post, $success) {
        if($post) {
            $vision_info = json_encode($request->input('vision_info'));  

            $record = $request->except(['step_id', 'success']);
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['vision_info'] = $vision_info;


            $data = 
            DB::table($tableName)
            ->where('board_id', $request->board_id)
            ->where('cardinal_id', $request->cardinal_id)
            ->where('user_id', Auth::user()->id)
            ->update($record);

            FormCheck::updateOrCreate(
                [
                    'user_id' => Auth::user()->id,
                    'board_id' => $request->board_id,
                    'cardinal_id' => $request->cardinal_id,
                    'step_id' => 8,
                ],
                [
                    'success' => $success,
                ]
            );

            return response()->json(['result' => true, 'data' => $data, 'message' => '수정 완료']);

        } else {
            $vision_info = json_encode($request->input('vision_info'));  

            $record = $request->except(['step_id', 'success']);
            $record['user_id'] = Auth::user()->id;
            $record['created_at'] = Carbon::now();
            $record['vision_info'] = $vision_info;
            $data = DB::table($tableName)->insert($record);

            FormCheck::updateOrCreate(
                [
                    'user_id' => Auth::user()->id,
                    'board_id' => $request->board_id,
                    'cardinal_id' => $request->cardinal_id,
                    'step_id' => 8,
                ],
                [
                    'success' => $success,
                ]
            );

            return response()->json(['result' => true, 'data' => $data, 'message' => '등록 완료']);
        }
    }

    private function handleStep9($request, $tableName, $post, $success) {
        if($post) {
            $infoOne = json_encode($request->input('info1'));  

            $record = $request->except(['step_id', 'success']);
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

            FormCheck::updateOrCreate(
                [
                    'user_id' => Auth::user()->id,
                    'board_id' => $request->board_id,
                    'cardinal_id' => $request->cardinal_id,
                    'step_id' => 9,
                ],
                [
                    'success' => $success,
                ]
            );

            return response()->json(['result' => true, 'data' => $data, 'message' => '수정 완료']);

        } else {
            if($request->input('info1')) {
                $infoOne = json_encode($request->input('info1'));  
                $record = $request->except(['step_id', 'success']);
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $record['info1'] = $infoOne;
                $data = DB::table($tableName)->insert($record);
            }

            if($request->input('info2')) {
                $infoTwo = json_encode($request->input('info2'));  
                $record = $request->except(['step_id', 'success']);
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
                $record = $request->except(['step_id', 'success']);
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $record['info4'] = $infoFour;
                $data = DB::table($tableName)->insert($record);
            } 

            if($request->input('info5')) {
                $infoFive = json_encode($request->input('info5'));  
                $record = $request->except(['step_id', 'success']);
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $record['info5'] = $infoFive;
                $data = DB::table($tableName)->insert($record);
            } 

            if($request->input('info6')) {
                $infoSix = json_encode($request->input('info6'));  
                $record = $request->except(['step_id', 'success']);
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $record['info6'] = $infoSix;
                $data = DB::table($tableName)->insert($record);
            } 

            if($request->input('info7')) {
                $infoSeven = json_encode($request->input('info7'));  
                $record = $request->except(['step_id', 'success']);
                $record['user_id'] = Auth::user()->id;
                $record['created_at'] = Carbon::now();
                $record['info7'] = $infoSeven;
                $data = DB::table($tableName)->insert($record);
            } 

            FormCheck::updateOrCreate(
                [
                    'user_id' => Auth::user()->id,
                    'board_id' => $request->board_id,
                    'cardinal_id' => $request->cardinal_id,
                    'step_id' => 9,
                ],
                [
                    'success' => $success,
                ]
            );

            return response()->json(['result' => true, 'data' => $data, 'message' => '등록 완료']);
        }
    }
}
