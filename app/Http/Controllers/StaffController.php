<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class StaffController extends Controller
{
    private $gc;

    public function __construct() {
        $container = Container::getInstance();
        $this->gc = $container->make(GlobalController::class);
    }

    public function pageCount(){

        $page_count = DB::select("
                Select count(*) as totcount From t_staff
                ",
            [
            ]);

        return $page_count[0]->totcount;
    }

    public function boardList($offset, $nextrow){
        $boardData = DB::select("
            Select id, name, email, email_verified_at, password,  remember_token, profile_photo_path, created_at, updated_at, role
            From t_staff
            Order By created_at desc
            limit :offset , :nextrow
        ",
            [
                "offset" => $offset,
                "nextrow" => $nextrow,
            ]);

        return $boardData;
    }

    public function StaffList($page_no=1, Request $request){
        //접속 권한 체크
        $permissionCheck = $this->gc->getPermissionCheck(2);
        if(!$permissionCheck){
            return view('msg',[
                'msg' => '접속 권한이 없습니다.',
                'return_url' => '/main',
            ]);
        }

        $list_size = 10;
        $page_size = 10;
        $page_no = $page_no < 1 ? 1 : $page_no;
        if ($page_no > 1) {
            $offset = ($page_no - 1) * $list_size;
        } else {
            $offset = 0;
        }

        $page_start = ($page_no % $page_size == 0 ? $page_no - $page_size : $page_no - $page_no % $page_size) + 1;
        $page_start = $page_start < 1 ? 1 : $page_start;

        //검색결과를 상단의 함수를 통해서 가지고 온다
        $page_count = $this->pageCount();
        $boardList = $this->boardList($offset, $list_size);

        $page_end = ceil($page_count/$list_size);
        if ($page_end > $page_start + $page_size - 1) {
            $page_end = $page_start + $page_size - 1;
        } else if ($page_count < 1) {
            $page_end = 0;
        }

        ///시작번호 찾기//
        //$startnumber=$page_count-($page_no-1)*$list_size;

        return view('Staff.list',[
            'boardList'=> $boardList,
            'page_start' => $page_start,
            'page_end' => $page_end,
            'previous' => $page_start > 1 ? true : false,
            'next' => ceil($page_count/$list_size) > $page_end ? true : false,
            'page_no' => $page_no,
        ]);
    }

    public function StaffWrite(Request $request){

        //접속 권한 체크
        $permissionCheck = $this->gc->getPermissionCheck(2);
        if(!$permissionCheck){
            return view('msg',[
                'msg' => '접속 권한이 없습니다.',
                'return_url' => '/main',
            ]);
        }

        $permissionList = (new StaffPermissionController())->boardList(0,100);

        return view('Staff.write',[
            'permissionList'=> $permissionList,
        ]);
    }

    public function staffSet(Request $request){
        $staffName = $request->staffName;
        $staffEmail = $request->staffEmail;
        $setPermissionVal = $request->setPermissionVal;

        if(!isset($staffName) || !isset($staffEmail) || !isset($setPermissionVal) ){
            return response()->json([
                'result' => 'fail',
                'msg' => '필수 값이 누락 되었습니다.'
            ]);
        }

        DB::beginTransaction();
        try {
            $result = DB::table('t_staff')->insert([
                'name' => $staffName,
                'email' => $staffEmail,
                'email_verified_at' => null,
                'password' => bcrypt(1234567890),
                'remember_token' => null,
                'profile_photo_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (!$result){
                DB::rollback();
                return response()->json([
                    'result' => 'fail',
                    'msg' => '데이터 등록에 실패 했습니다. 다시 시도하여 주세요'
                ]);
            }

            $lastInsertStaffId = $this->getLastInsertStaffId();

            $val = explode("#", substr($request->setPermissionVal,0,-1));

            foreach ($val as $value) {
                $result = DB::table('t_staff_permission')->insert([
                    'staff_id' => $lastInsertStaffId,
                    'permissionBit' => $value,
                    'set_staff_id' => auth()->user()->id,
                    'regdt' => now(),
                ]);

                if (!$result){
                    DB::rollback();
                    return response()->json([
                        'result' => 'fail',
                        'msg' => '삭제에 실패한 권한이 있습니다. 다시 시도하여 주세요'
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'result' => 'ok',
                'msg' => '등록 되었습니다.'
            ]);
        }catch (\Exception $e) {
            echo $e->getMessage();
        }
        DB::rollback();

    }

    public function getLastInsertStaffId(){
        $lastInsertInfo = DB::select("
                Select LAST_INSERT_ID() As LastNo
                ",
            [
            ]);

        return $lastInsertInfo[0]->LastNo;
    }

    public function StaffDetails(Request $request){

        //접속 권한 체크
        $permissionCheck = $this->gc->getPermissionCheck(2);
        if(!$permissionCheck){
            return view('msg',[
                'msg' => '접속 권한이 없습니다.',
                'return_url' => '/main',
            ]);
        }

        $StaffId = $request->id;

        if(!isset($StaffId)){
            return view('msg',[
                'msg' => '필수 값이 누락 되었습니다.',
                'return_url' => '/staff/list',
            ]);
        }

        $staffInfo = $this->gc->getStaffInfo4Id($StaffId);
        $permissionList = (new StaffPermissionController())->getPermissionStaffList($StaffId);

        return view('Staff.details',[
            'staffInfo'=> $staffInfo,
            'permissionList'=> $permissionList,
        ]);
    }

    public function StaffUpdate(Request $request){

        $staffId = $request->staffId;
        $staffName = $request->staffName;
        $staffEmail = $request->staffEmail;
        $setPermissionVal = $request->setPermissionVal;

        if(!isset($staffName) || !isset($staffEmail) || !isset($setPermissionVal) ){
            return response()->json([
                'result' => 'fail',
                'msg' => '필수 값이 누락 되었습니다.'
            ]);
        }

        DB::beginTransaction();
        try {
            $result = DB::table('t_staff')->where('id', $staffId )->update([
                'name' => $staffName,
                'email' => $staffEmail,
                'updated_at' => now(),
            ]);

            if (!$result){
                DB::rollback();
                return response()->json([
                    'result' => 'fail',
                    'msg' => '데이터 수정에 실패 했습니다. 다시 시도하여 주세요(1)'
                ]);
            }

            $result = DB::table('t_staff_permission')->where('staff_id', $staffId )->delete();

            if (!$result){
                DB::rollback();

                return response()->json([
                    'result' => 'fail',
                    'msg' => '데이터 수정에 실패 했습니다. 다시 시도하여 주세요(2)'
                ]);
            }

            $val = explode("#", substr($request->setPermissionVal,0,-1));

            foreach ($val as $value) {
                $result = DB::table('t_staff_permission')->insert([
                    'staff_id' => $staffId,
                    'permissionBit' => $value,
                    'set_staff_id' => auth()->user()->id,
                    'regdt' => now(),
                ]);

                if (!$result){
                    DB::rollback();
                    return response()->json([
                        'result' => 'fail',
                        'msg' => '삭제에 실패한 권한이 있습니다. 다시 시도하여 주세요'
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'result' => 'ok',
                'msg' => '수정 되었습니다.'
            ]);
        }catch (\Exception $e) {
            echo $e->getMessage();
        }
        DB::rollback();

        return response()->json([
            'result' => 'fail',
            'msg' => '수정이 실패 되었습니다. 다시 시도하여 주세요'
        ]);
    }

    public function StaffDel($id){
        /*$result = DB::table('t_staff')->where('id', $id )->delete();
        if (!$result){
            return false;
        }

        return true;*/

        DB::beginTransaction();
        try {
            $result = DB::table('t_staff')->where('id', $id )->delete();

            if (!$result){
                DB::rollback();
                return false;
            }

            $result = DB::table('t_staff_permission')->where('staff_id', $id )->delete();

            if (!$result){
                DB::rollback();
                return false;
            }

            DB::commit();

            return true;
        }catch (\Exception $e) {
            echo $e->getMessage();
        }
        DB::rollback();
    }

    public function StaffDelete(Request $request){
        $id = $request->staffId;

        if(!isset($id) ){
            return response()->json([
                'result' => 'fail',
                'msg' => '필수 값이 누락 되었습니다.'
            ]);
        }

        $result = $this->StaffDel($id);

        if (!$result){
            return response()->json([
                'result' => 'fail',
                'msg' => '데이터 삭제에 실패 했습니다. 다시 시도하여 주세요'
            ]);
        }

        return response()->json([
            'result' => 'ok',
            'msg' => '삭제 되었습니다.'
        ]);
    }

    public function StaffList4Delete(Request $request){
        $val = explode("#", substr($request->arrStaff,0,-1));

        foreach ($val as $value) {
            $result = $this->StaffDel($value);

            if (!$result){
                return response()->json([
                    'result' => 'fail',
                    'msg' => '삭제에 실패한 리스트가 있습니다. 다시 시도하여 주세요'
                ]);
            }
        }

        return response()->json([
            'result' => 'ok',
            'msg' => '삭제 되었습니다.'
        ]);
    }

    public function ajaxImgUpload(Request $request){
        if (!Auth::check()) {
            return response()->json([
                'result' => 'fail',
                'msg' => '로그인이 필요합니다.'
            ], 200);
        }

        $allowedExt = array("jsp", "cgi", "php", "asp", "aspx", "exe", "com", "html", "htm", "cab", "php3", "pl", "java", "class", "js", "css","bat");
        $arrMimeCheck = array("image/gif", "image/png", "image/jpg", "image/jpeg");

        $maxFileSize = (1204 * 1024) * 20; //2097152; //20메가 설정
        $fileName = $request->file('file');
        $imgMime = $fileName->getMimeType();
        $ext = $fileName->getClientOriginalExtension(); //파일 확장자 가져오기
        $fileSize = $fileName->getSize(); //파일 사이즈 가져오기
        if(!in_array($imgMime, $arrMimeCheck)){
            return response()->json([
                'result' => 'fail',
                'msg' => '허용되지 않는 파일 입니다.'
            ], 200);
        }
        //파일생성
        $img = Image::make($request->file('file'))->orientate();

        if($fileSize > $maxFileSize) {
            return response()->json([
                'result' => 'fail',
                'msg' => '파일의 업로드 최대 크기는 20MB 입니다.'
            ], 200);
        }
        // 업로드 가능한 확장자 인지 확인한다.
        if(in_array($ext, $allowedExt)) {
            return response()->json([
                'result' => 'fail',
                'msg' => '허용되지 않는 확장자입니다.'
            ], 200);
        } else {
            $saveFileName = '_'.time();
            $saveFileNameS = $saveFileName.'_s';
            $saveFileNameL = $saveFileName.'_l';
            $folder = '/staff/'.date('Y').'/'.date('m').'/'.date('d'); //CDN 임시 업로드 주소
            $tmpFolderLocalSave = public_path('/tmp/staff/'); //서버 임시 업로드 물리 주소 가져오기
            $tmpFolder = '/tmp/staff/'; //서버 임시 업로드

            $return_file_chat_s_url = "";
            $return_file_chat_l_url = "";

            $width = $img->width();

            if(strtolower($ext) == "gif"){
                $fileName->storeAs(
                    $folder, $saveFileNameS . '.' . $ext, "ftp"
                );

                $return_file_chat_s_url = $this->gc->getUserFileDomain().$folder."/".$saveFileNameS.'.'.$ext;
                $return_file_chat_l_url = $this->gc->getUserFileDomain().$folder."/".$saveFileNameS.'.'.$ext;

            }else {

                //채팅용 이미지 생성
                if ($width <= 270) {
                    $fileName->storeAs(
                        $folder, $saveFileNameS . '.' . $ext, "ftp"
                    );

                    $return_file_chat_s_url = $this->gc->getUserFileDomain().$folder."/".$saveFileNameS.'.'.$ext;
                    $return_file_chat_l_url = $this->gc->getUserFileDomain().$folder."/".$saveFileNameS.'.'.$ext;
                } else {
                    //웹용 이미지 리사이즈
                    $resultWeb = $img->resize(270, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($tmpFolderLocalSave . $saveFileNameS . '.' . $ext);

                    //이미지 전송
                    Storage::disk('sftp')->put($folder . '/' . $saveFileNameS . '.' . $ext, $resultWeb);
                    //업로드 완료후 로컬 이미지 삭제
                    Storage::disk('public')->delete([$tmpFolder . $saveFileNameS . '.' . $ext]);

                    $resultWeb2 = $img->resize(700, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($tmpFolderLocalSave . $saveFileNameL . '.' . $ext);

                    //이미지 전송
                    Storage::disk('sftp')->put($folder . '/' . $saveFileNameL . '.' . $ext, $resultWeb2);
                    //업로드 완료후 로컬 이미지 삭제
                    Storage::disk('public')->delete([$tmpFolder . $saveFileNameL . '.' . $ext]);

                    $return_file_chat_s_url = $this->gc->getUserFileDomain().$folder."/".$saveFileNameS.'.'.$ext;
                    $return_file_chat_l_url = $this->gc->getUserFileDomain().$folder."/".$saveFileNameL.'.'.$ext;
                }
            }




            return response()->json([
                'result' => 'ok',
                'fileNameS'=> $return_file_chat_s_url,
                'fileNameL'=> $return_file_chat_l_url,
                'width'=> $width,
                'mimetype'=> $imgMime,
            ], 200);

        }
    }
}
