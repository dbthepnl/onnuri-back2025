<?php

namespace App\Http\Controllers;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Reservation;
use App\Http\Resources\ReservationCollection;
use App\Http\Resources\CalendarResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\UserRequest;


class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = QueryBuilder::for(Reservation::class)
        ->selectRaw('id, public, name, CONCAT(SUBSTRING(phone, 1, 7), "****") as phone, created_at')
        ->orderBy('updated_at', 'desc')
        ->paginate(15);

        return new ReservationCollection($data);
    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'public' => 'nullable',
            'reservation_type' => 'nullable|boolean',
            'name' => 'string',
            'home_phone' => 'string',
            'phone' => 'string',
            'email' => 'string',
            'password' => 'string',
            'church' => 'string',
            'church_phone' => 'string',
            'pastor_name' => 'string',
            'church_address' => 'string',
            'organization' => 'string',
            'leader' => 'string',
            'event_name' => 'string',
            'office_phone' => 'string',
            'address' => 'string',
            'room_worship_type' => 'nullable|boolean',
            'room_reservation' => 'nullable',
            'worship_reservation' => 'nullable',
            'cafeteria_reservation' => 'nullable'
        ]);

        if($request->input('room_reservation')) {
            $data['room_reservation'] = json_encode($request->input('room_reservation'));  
        } 

        if($request->input('worship_reservation')) {
            $data['worship_reservation'] = json_encode($request->input('worship_reservation'));         
        }

        if($request->input('cafeteria_reservation')) {
            $data['worship_reservation'] = json_encode($request->input('worship_reservation'));         
        }

  
       $data = Reservation::create($data);

        //$post = Reservation::create($data);
    
        return response()->json([
            'success' => true,
            'message' => '등록 완료'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {   
            $data = Reservation::where('password', $request->password)
            ->first();
            if($data) {
                return response()->json(['success' => true, 'message' => '성공', 'data' => $data]);
            }
            return response()->json(['success' => false, 'message' => '실패', 'data' => NULL]);
            
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
        $post = Calendar::findOrFail($id);
        $post->update($request->all());
    
        return response()->json([
            'success' => true,
            'message' => '업데이트 완료'
        ], 200);
    }

    public function updatePost(Request $request, string $id)
    {
        $post = Post::findOrFail($id);
        $post->update($request->all());
    
        return response()->json([
            'success' => true,
            'message' => '업데이트 완료'
        ], 200);
    }

    public function storeImage(Request $request)
    {
        $media = Auth::user()->addMedia($request->file('image'))->toMediaCollection('post_image');
        return response()->json(['result' => true, 'data' => $media, 'message' => NULL]);
    }

    public function fileUpdate(Request $request, string $id) {
        $uploadedMedia = [];
        $data = Post::where('id', $id)->first();
        
        if ($request->status == 'delete') { 
           $data =  DB::table('media')->where('id', $request->media_id)->delete();
        }

        if ($request->status == 'add' &&  $request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                 $media = $data->addMedia($file)
                     ->toMediaCollection('files', 's3'); 

                $uploadedMedia[] = [
                    'id' => $media->id,
                    'url' => $media->getUrl(),  
                    'name' => $media->file_name,
                    'size' => $media->size,     
                ];
            }
        }

        if ($request->status == 'update' && $request->hasFile('files')) {
            foreach ($request->file('files') as $file) {

                $media = $data->addMedia($file)
                     ->toMediaCollection('files', 's3'); 

                $uploadedMedia[] = [
                    'url' => $media->getUrl(),  
                    'name' => $media->file_name,
                    'size' => $media->size,     
                ];

                
            }
            DB::table('media')->where('id', $request->media_id)->delete();

        }


        return response()->json([
            'success' => true,
            'message' => '수정 완료',
            'uploaded_media' => $uploadedMedia,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Reservation::findOrFail($id);
        $post->update([
            'public' => 0
        ]);
        return response()->json([
            'success' => true,
            'message' => '취소 완료'
        ], 200);
    }
}
