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
