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
        if($request->room_reservation == 1 && $request->pageType == "grid") {
            $data = $data->selectRaw('id, public, reservation_type, name, phone, email, room_worship_type, room_reservation, created_at')
            ->whereYear('created_at', $request->year)
            ->whereMonth('created_at', $request->month)
            ->get();

            $data->transform(function ($item) use ($request) {
                $roomReservations = json_decode($item->room_reservation, true);
            
                $year = $request->year;
                $month = $request->month;
            
                $filteredReservations = collect($roomReservations)->filter(function ($reservation) use ($year, $month) {
                    return isset($reservation['start_date']) && 
                           \Carbon\Carbon::parse($reservation['start_date'])->year == $year && 
                           \Carbon\Carbon::parse($reservation['start_date'])->month == $month;
                });
            
                if ($filteredReservations->isNotEmpty()) {
                    $item->room_reservation = $filteredReservations;
                    return $item;
                }
                return null;
            })->filter(function ($item) {
                return $item !== null;
            });
    
        }

        //예배 예약
        if($request->room_reservation == 2 && $request->pageType == "grid") {
            $data = $data->selectRaw('id, public, reservation_type, name, phone, email, room_worship_type, worship_reservation AS room_reservation, created_at')
            ->whereYear('created_at', $request->year)
            ->whereMonth('created_at', $request->month)
            ->get();

            $data->transform(function ($item) use ($request) {
                $roomReservations = json_decode($item->room_reservation, true);
            
                $year = $request->year;
                $month = $request->month;
            
                $filteredReservations = collect($roomReservations)->filter(function ($reservation) use ($year, $month) {
                    return isset($reservation['start_date']) && 
                           \Carbon\Carbon::parse($reservation['start_date'])->year == $year && 
                           \Carbon\Carbon::parse($reservation['start_date'])->month == $month;
                });
            
                if ($filteredReservations->isNotEmpty()) {
                    $item->room_reservation = $filteredReservations;
                    return $item;
                }
                return null;
            })->filter(function ($item) {
                return $item !== null;
            });

        }

        //식사 예약
        if($request->room_reservation == 3 && $request->pageType == "grid") {
            $data = $data->selectRaw('id, public, reservation_type, name, phone, email, room_worship_type, cafeteria_reservation AS room_reservation, created_at')
            ->whereYear('created_at', $request->year)
            ->whereMonth('created_at', $request->month)
            ->get();

            $data->transform(function ($item) use ($request) {
                $roomReservations = json_decode($item->room_reservation, true);
            
                $year = $request->year;
                $month = $request->month;
            
                $filteredReservations = collect($roomReservations)->filter(function ($reservation) use ($year, $month) {
                    return isset($reservation['start_date']) && 
                           \Carbon\Carbon::parse($reservation['start_date'])->year == $year && 
                           \Carbon\Carbon::parse($reservation['start_date'])->month == $month;
                });
            
                if ($filteredReservations->isNotEmpty()) {
                    $item->room_reservation = $filteredReservations;
                    return $item;
                }
                return null;
            })->filter(function ($item) {
                return $item !== null;
            });
        }

        if($request->pageType == 'list') {
            $data = $data->selectRaw('
                id,
                public,
                reservation_type,
                name,
                phone,
                email,
                room_worship_type,
                CASE WHEN room_reservation IS NOT NULL AND room_reservation != "" THEN 1 ELSE 0 END AS room_reservation,
                CASE WHEN worship_reservation IS NOT NULL AND worship_reservation != "" THEN 1 ELSE 0 END AS worship_reservation,
                CASE WHEN cafeteria_reservation IS NOT NULL AND cafeteria_reservation != "" THEN 1 ELSE 0 END AS cafeteria_reservation,
                created_at
            ')
            ->when(request()->has('reservation_type') && request('reservation_type') !== null, function($query) {
                return $query->where('reservation_type', request('reservation_type'));
            })
            ->orderBy('updated_at', 'asc')
            ->paginate(15);

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
