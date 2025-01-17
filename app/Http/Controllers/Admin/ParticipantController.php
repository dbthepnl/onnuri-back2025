<?php

namespace App\Http\Controllers\Admin;

use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Participant;
use App\Http\Resources\ParticipantCollection;
use App\Http\Resources\ParticipantResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $participants = QueryBuilder::for(Participant::class)
        ->select('id', 'post_id', 'name', 'my_title', 'my_content', 'intro', 'email', 'phone', 'created_at') 
        ->when($request->post_id, function ($query) use ($request) {
            $query->where("post_id", $request->post_id); // 기본값 1: 공지사항
        })
        ->allowedFilters([
            AllowedFilter::callback('search', function ($query, $value) { // 전체 검색
                $query->where(function ($query) use ($value) {
                    $query->where('name', 'like', "%$value%");
                });
            }),
        ])
        ->allowedSorts(['id', 'name'])
        ->paginate(15);
        
        return new ParticipantCollection($participants);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {   
            
            $data = Participant::findOrFail($id);
            return new ParticipantResource($data);
            
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
