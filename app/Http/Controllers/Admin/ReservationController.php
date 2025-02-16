<?php

namespace App\Http\Controllers\Admin;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Reservation;
use App\Http\Resources\ReservationCollection;
use App\Http\Resources\ReservationResource;
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
        $data = QueryBuilder::for(Reservation::class);

        //객실 예약
        if($request->room_reservation && $request->pageType == "grid") {
            $data = $data->where('id, reservation_type, name, phone, email, room_worship_type, created_at')->get();
            $data->transform(function ($item) {
                $item->room_reservation = json_decode($item->room_reservation, true);
                return $item;
            });
        
        }

        //예배 예약
        if($request->worship_reservation && $request->pageType == "grid") {
            $data = $data->get();
            $data->transform(function ($item) {
                $item->room_reservation = json_decode($item->worship_reservation, true);
                return $item;
            });

        }

        //식사 예약
        if($request->cafeteria_reservation && $request->pageType == "grid") {
            $data = $data->get();
            $data->transform(function ($item) {
                $item->room_reservation = json_decode($item->cafeteria_reservation, true);
                return $item;
            });
        }

        if($request->pageType == 'list') {
            $data = $data->paginate(15);
        }



        return new ReservationCollection($data);
    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'public' => 'nullable|boolean',
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

        $data['room_reservation'] = json_encode($request->input('room_reservation'));  
        $data['worship_reservation'] = json_encode($request->input('worship_reservation'));  
        $data['cafeteria_reservation'] = json_encode($request->input('cafeteria_reservation'));  
  
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
    public function show(string $id)
    {
        try {   
            $data = Reservation::findOrFail($id);
            return new ReservationResource($data);
            
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
        $data = $request->validate([
            'public' => 'nullable|boolean',
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

        $data['room_reservation'] = json_encode($request->input('room_reservation'));  
        $data['worship_reservation'] = json_encode($request->input('worship_reservation'));  
        $data['cafeteria_reservation'] = json_encode($request->input('cafeteria_reservation'));  
  
       $data = Reservation::where('id', $id)->update($data);
       
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
        $post = Reservation::findOrFail($id);
        $post->forceDelete();
        return response()->json([
            'success' => true,
            'message' => '삭제 완료'
        ], 200);
    }
}
