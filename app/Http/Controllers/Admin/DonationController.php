<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Http\Resources\DonationCollection;
use App\Http\Resources\DonationResource;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = QueryBuilder::for(Donation::class)
        ->where("board", 'donation') //donation
        ->when($request->has('category'), function ($query) use ($request) {
            $query->where("category", $request->category); // 기본값 1: 공지사항
        })
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

        return new DonationCollection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'public' => 'required|boolean',
            'order' => 'nullable',
            'board' => 'required|string',
            'goal_price' => 'nullable|string',
            'current_price' => 'nullable|string',
            'content' => 'nullable|string', 
        ]);

        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();
        $data['user_id'] = 1;
        $data['category'] = $request->category ?? 1;

        $post = Donation::create($data);
        
        if($request->hasFile('donation_photo')){
            $post->addMedia($request->file('donation_photo'))->toMediaCollection('d_photo', 's3');
        }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $post->addMedia($file)
                     ->toMediaCollection('files', 's3'); // Store in 'files' collection on S3
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
            $data = Donation::findOrFail($id);
            return new DonationResource($data);
            
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
        $post = Donation::findOrFail($id);
        $post->update($request->all());
    
        return response()->json([
            'success' => true,
            'message' => '업데이트 완료'
        ], 200);
    }

    public function updateDonation(Request $request, string $id)
    {
        $post = Donation::findOrFail($id);
        $post->update($request->all());
    
        return response()->json([
            'success' => true,
            'message' => '업데이트 완료'
        ], 200);
    }

    public function profile(Request $request, string $id) {
        $post = Donation::findOrFail($id);

        //이미지 삭제한 경우
        if($request->status == 'delete'){
            $post->clearMediaCollection('d_photo');
        }
        //이미지 업데이트한 경우
        if($request->hasFile('donation_photo') && $request->status == 'update'){
            $post->clearMediaCollection('d_photo');
            $post->addMedia($request->file('donation_photo'))->toMediaCollection('d_photo', 's3');
        }

        //이미지 추가한 경우
        if($request->hasFile('donation_photo') && $request->status == 'add'){
            $post->addMedia($request->file('donation_photo'))->toMediaCollection('d_photo', 's3');
        }

        //이미지 기존걸 유지한 경우
        if(!$request->hasFile('donation_photo') && $request->status == 'keep'){
            $post->clearMediaCollection('d_photo');
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
        $post = Donation::findOrFail($id);
        $post->forceDelete();
        return response()->json([
            'success' => true,
            'message' => '삭제 완료'
        ], 200);
    }
}
