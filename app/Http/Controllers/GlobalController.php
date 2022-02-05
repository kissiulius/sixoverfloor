<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GlobalController extends Controller
{
    //

    public static function getUserFileDomain() {
        return 'http://img.sixoverfloor.xyz';
    }

    public function getStaffInfo4Id($id){
        if(!isset($id))
            return false;

        $result = DB::select("select id, name, email, email_verified_at, password,  remember_token, profile_photo_path, created_at, updated_at, role
            from t_staff where id=:id",
            [
                "id" => $id
            ]);

        if ($result)
            return $result[0];
        return false;
    }

    public function getStaffInfo4Eail($email){
        if(!isset($id))
            return false;

        $result = DB::select("select id, name, email, email_verified_at, password,  remember_token, profile_photo_path, created_at, updated_at, role
            from t_staff where email=:email",
            [
                "email" => $email
            ]);

        if ($result)
            return $result[0];
        return false;
    }

    public function getPermissionCheck($permissionBit){

        if (!Auth::check()) {
            return false;
        }

        $result = DB::select("
                Select count(*) as permissionVal
                From t_staff
                Where id = :id
                And role & (select permissionBit from t_permission where permissionBit = :permissionBit)
            ",
            [
                "id" => auth()->user()->id,
                "permissionBit" => $permissionBit,
            ]);

        if ($result)
            return true;

        return false;
    }
}
