<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = Role::selectRaw('roles.id, roles.name, count(users.id) AS user_count, roles.created_at')->leftJoin('users', 'users.role', '=', 'roles.id')->groupBy('roles.id', 'roles.name', 'roles.created_at')->get();
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */

     //회원 등급 생성
    public function store(Request $request)
    {
        if($request->name) {
            $data = Role::where('name', $request->name)->first();
            if(!$data) {
                $role = Role::create(['name' => $request->name ],['guard_name' => 'web'] );
                return response()->json(['success' => true, 'message' => '성공']);
            }
        }

        return response()->json(['success' => false, 'message' => '실패, 존재하는 데이터이거나 오류']);
       // $permission = Permission::create(['name' => 'edit articles']);
    }

    //
    public function userAssignRole(Request $request)
    {
        $data = Role::get();
        return response()->json(['result' => true, 'data' => $data, 'message' => '조회 완료']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
       $data = Role::findOrFail($id);
       return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if($request->name) {
            $data = Role::where('name', $request->name)->first();
            if(!$data) {
                $role = Role::create(['name' => $request->name ]);
                return response()->json(['success' => true, 'message' => '수정완료']);
            }
        }

        return response()->json(['result' => false, 'message' => '이름 이미 존재']);
       // $permission = Permission::create(['name' => 'edit articles']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Role::findOrFail($id);
        $post->forceDelete();
        return response()->json([
            'success' => true,
            'message' => '삭제 완료'
        ], 200);
    }
}
