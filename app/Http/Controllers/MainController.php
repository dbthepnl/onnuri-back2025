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

class MainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $all = QueryBuilder::for(Post::class)
        ->selectRaw('id, board, title, updated_at')
        ->whereIn("board", ["qna", "info"]) //QNA: 질문 답변 / INFO: 소식관리
        ->orderBy('order', 'desc')
        ->orderBy('updated_at', 'desc')
        ->limit(4)
        ->get();

        $qna = QueryBuilder::for(Post::class)
        ->selectRaw('id, board, title, updated_at')
        ->where("board", "qna") //QNA: 질문 답변 / INFO: 소식관리
        ->orderBy('order', 'desc')
        ->orderBy('updated_at', 'desc')
        ->limit(4)
        ->get();

        $info = QueryBuilder::for(Post::class)
        ->selectRaw('id, board, title, updated_at')
        ->where("board", "info") //QNA: 질문 답변 / INFO: 소식관리
        ->orderBy('order', 'desc')
        ->orderBy('updated_at', 'desc')
        ->limit(4)
        ->get();



        return response()->json([
            'success' => true,
            'message' => '조회, all=전체, qna=질문, info=소식',
            'all' => $all,
            'qna' => $qna,
            'info' => $info
        ], 200);
    }

    public function menu(Request $request) {

        $currentDate = Carbon::today();


        $data = QueryBuilder::for(Board::class)
        ->selectRaw('id, nickname, name_ko, name_en, information') // selectRaw
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
