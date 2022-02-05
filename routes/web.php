<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

//인덱스
Route::get('/', function () { return view('Auth.login'); })->name('login');
//로그인
Route::post('/login', 'AuthController@loginAdmin');
Route::get('/setadmin', 'AuthController@setBaseAdmin');

//로그인 사용자마 접근
Route::middleware('auth')->group(function() {
    //메인
    Route::get('/main', function () { return view('main'); });

    //권한관리
    Route::prefix('/permission')->group(function() {
        Route::get('/list/{page_no?}', 'StaffPermissionController@PermisstionList'); //권한 관리 리스트
        Route::get('/write', 'StaffPermissionController@PermisstionWrite'); //권한 관리 등록 사용자 화면
        Route::post('/write', 'StaffPermissionController@PermisstionSet'); //권한 관리 등록하기
        Route::get('/write', 'StaffPermissionController@PermisstionWrite'); //권한 관리 상세 사용자 화면
        Route::get('/details/{permissionBit}', 'StaffPermissionController@PermisstionDetails'); //권한 관리 상세 사용자 화면
        Route::post('/update', 'StaffPermissionController@PermisstionUpdate'); //권한 관리 수정
        Route::delete('/delete', 'StaffPermissionController@PermisstionDelete'); //권한 관리 삭제
        Route::delete('/list4delete', 'StaffPermissionController@PermisstionList4Delete'); //권한 관리 삭제
    });

    //관리자 관리
    Route::prefix('/staff')->group(function() {
        Route::get('/list/{page_no?}', 'StaffController@StaffList'); //관리자 관리 리스트
        Route::get('/write', 'StaffController@StaffWrite'); //관리자 관리 등록 사용자 화면
        Route::post('/write', 'StaffController@StaffSet'); //관리자 관리 등록하기
        Route::get('/write', 'StaffController@StaffWrite'); //관리자 관리 상세 사용자 화면
        Route::get('/details/{id}', 'StaffController@StaffDetails'); //관리자 관리 상세 사용자 화면
        Route::post('/update', 'StaffController@StaffUpdate'); //관리자 관리 수정
        Route::delete('/delete', 'StaffController@StaffDelete'); //관리자 관리 삭제
        Route::delete('/list4delete', 'StaffController@StaffList4Delete'); //관리자 리스트 관리 삭제
        Route::post('/ajaxImgUpload', 'StaffController@ajaxImgUpload'); //이미지 업로드
    });
});
