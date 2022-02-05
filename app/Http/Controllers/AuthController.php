<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Validator;
use Jenssegers\Agent\Agent;
use Illuminate\Container\Container;

class AuthController extends Controller
{
    public function login($request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string'
        ]);
        if ($validator->fails())
            return false;


        $credentials = $request->only('email', 'password');

        if(!Auth::attempt($credentials))
            return false;

        return true;
    }

    public function loginAdmin(Request $request) {
        if (!$this->login($request)) {
            return response()->json([
                'result' => 'fail',
                'message' => '로그인에 실패 하였습니다.'
            ], 200);
        }

        return response()->json([
            'result' => 'ok',
            'return_url'=> url()->previous(),
        ], 200);
    }

    public function setBaseAdmin(){
        //DB::statement("drop table t_staff",[]);
        //DB::statement("drop table t_permission",[]);
        //DB::statement("drop table t_staff_permission",[]);

        /*DB::statement("CREATE TABLE t_staff  (
                                    id                	bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
                                    name              	varchar(255) NOT NULL,
                                    email             	varchar(255) NOT NULL,
                                    email_verified_at 	timestamp ,
                                    password          	varchar(255) NOT NULL,
                                    remember_token    	varchar(100) ,
                                    profile_photo_path	varchar(2048) ,
                                    created_at        	timestamp DEFAULT current_timestamp(),
                                    updated_at        	timestamp ,
                                    role              	int(11) NOT NULL DEFAULT '0',
                                    PRIMARY KEY(id)
                                );
                                ALTER TABLE t_staff
                                    ADD CONSTRAINT t_staff_email_unique
                                    UNIQUE (email);", [

        ]);

        DB::statement("CREATE TABLE t_permission  (
                                    permissionBit 	int(11) NOT NULL,
                                    permissionName	varchar(255) NOT NULL,
                                    regdt         	timestamp DEFAULT current_timestamp(),
                                    PRIMARY KEY(permissionBit)
                                );", [

        ]);

        DB::statement("CREATE TABLE t_staff_permission  (
                                    id           	bigint(20) AUTO_INCREMENT NOT NULL,
                                    staff_id     	bigint(20) NOT NULL,
                                    permissionBit	int(11) NOT NULL,
                                    set_staff_id 	bigint(20) NOT NULL,
                                    regdt        	timestamp NOT NULL DEFAULT current_timestamp(),
                                    PRIMARY KEY(id)
                                );", [

        ]);*/

        DB::table('t_staff')->truncate();
        DB::table('t_staff_permission')->truncate();
        DB::table('t_permission')->truncate();


        DB::table('t_permission')->insert([
            'permissionBit' => 1,
            'permissionName' => "권한관리",
            'regdt' => now(),
        ]);

        DB::table('t_permission')->insert([
            'permissionBit' => 2,
            'permissionName' => "관리자관리",
            'regdt' => now(),
        ]);

        DB::table('t_staff')->insert([
            'name' => "admin",
            'email' => "admin@sixoverfloor.xyz",
            'email_verified_at' => null,
            'password' => bcrypt("01234567890"),
            'remember_token' => null,
            'profile_photo_path' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('t_staff_permission')->insert([
            'staff_id' => 1,
            'permissionBit' => 1,
            'set_staff_id' => 1,
            'regdt' => now(),
        ]);

        DB::table('t_staff_permission')->insert([
            'staff_id' => 1,
            'permissionBit' => 2,
            'set_staff_id' => 1,
            'regdt' => now(),
        ]);

        return redirect("/");

        /*return view('Staff.details',[
            'staffInfo'=> $staffInfo,
            'permissionList'=> $permissionList,
        ]);*/
    }
}
