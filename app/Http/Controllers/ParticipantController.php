<?php

namespace App\Http\Controllers;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Participant;
use App\Models\Post;
use App\Http\Resources\NoticeCollection;
use App\Http\Resources\NoticeResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\UserRequest;

class ParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'board' => 'nullable|string',
            'email' => 'nullable|string',
            'intro' => 'nullable|string',
            'my_title' => 'nullable',
            'my_content' => 'required|string', 
            'post_id' => 'nullable|string',
        ]);
    
        if(Participant::where('post_id', $request->post_id)->where('phone', $request->phone)->exists()) {
            if(Participant::where('post_id', $request->post_id)->where('email', $request->email)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => '이미 참가 완료'
                ], 200);
            }

        }

        
        $participant = Participant::create($data);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $participant->addMedia($file)
                     ->toMediaCollection('files', 's3'); // 파일 등록
            }
        }

        return response()->json([
            'success' => $participant ? true : false,
            'message' => $participant ? '등록 완료' : '등록 실패'
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
