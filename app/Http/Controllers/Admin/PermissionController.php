<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use App\Models\RoleHasPermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = Permission::get();

        $rolePermission = DB::table('role_has_permissions')->where('role_id', $request->role_id)->get();
        return response()->json(['role_id' => $request->role_id, 'data' => $data, 'role_permission' => $rolePermission]);
    }

    /**
     * Store a newly created resource in storage.
     */

     //회원 등급 생성
    public function store(Request $request)
    {
       if($request->role) {
            foreach ($request->permission as $permission) {
                foreach ($permission as $permissionId => $value) {
                    if ($value == 1) {
                    
                        RoleHasPermission::updateOrCreate(
                            ['role_id' => $request->role, 'permission_id' => $permissionId]
                        );
                    } else {  
                        RoleHasPermission::where('role_id', $request->role)
                        ->where('permission_id', $permissionId)
                        ->delete();
                    }
                }
            }
       }

       return response()->json(['result' => true, 'message' => '변경 완료']);
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
