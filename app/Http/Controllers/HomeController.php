<?php

namespace App\Http\Controllers;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Calendar;
use App\Models\Board;
use App\Models\Post;
use App\Http\Resources\NoticeCollection;
use App\Http\Resources\CalendarCollection;
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

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $all = QueryBuilder::for(Post::class)
        ->selectRaw('id, board, title, updated_at')
        ->whereIn("board", ["notice", "train"]) //훈련관리:train / 공지관리:notice
        ->orderBy('order', 'desc')
        ->orderBy('updated_at', 'desc')
        ->limit(4)
        ->get();

        $notice = QueryBuilder::for(Post::class)
        ->selectRaw('id, board, title, updated_at')
        ->where("board", "notice") //훈련관리:train / 공지관리:notice
        ->orderBy('order', 'desc')
        ->orderBy('updated_at', 'desc')
        ->limit(4)
        ->get();

        $train = QueryBuilder::for(Post::class)
        ->selectRaw('id, board, title, updated_at')
        ->where("board", "train") //훈련관리:train / 공지관리:notice
        ->orderBy('order', 'desc')
        ->orderBy('updated_at', 'desc')
        ->limit(4)
        ->get();



        return response()->json([
            'success' => true,
            'message' => '조회, all=전체, notice=공지, train=훈련',
            'all' => $all,
            'notice' => $notice,
            'train' => $train
        ], 200);
    }

    public function calendar(Request $request) {

        $currentDate = Carbon::today();


        $data = QueryBuilder::for(Calendar::class)
        ->selectRaw('calendars.*') // selectRaw
        ->where("board", "calendar") // 달력만
        ->where("category", "1")
        ->when($request->has('month') && $request->has('year'), function ($query) use ($request) {
            $query->whereYear('start', $request->year)
                  ->whereMonth('start', $request->month);
        })
        ->get();

        return response()->json([
            'success'=> true,
            'message' => '일정 조회',
            'data' => $data
        ]);
        
    }

    public function menu(Request $request) {

        $currentDate = Carbon::today();


        $data = QueryBuilder::for(Board::class)
        ->selectRaw('id, name_ko, name_en, information') // selectRaw
        ->where("name_en", "program") // 달력만
        ->get();

        return response()->json([
            'success'=> true,
            'message' => '메뉴 조회',
            'data' => $data
        ]);
        
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
