<?php

namespace App\Http\Controllers;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffPermissionController extends Controller
{
    private $gc;

    public function __construct() {
        $container = Container::getInstance();
        $this->gc = $container->make(GlobalController::class);
    }

    public function pageCount(){

        $page_count = DB::select("
                Select count(*) as totcount From t_permission
                ",
            [
            ]);

        return $page_count[0]->totcount;
    }

    public function boardList($offset, $nextrow){
        $boardData = DB::select("
            Select permissionBit , permissionName
            From t_permission
            limit :offset , :nextrow
        ",
        [
            "offset" => $offset,
            "nextrow" => $nextrow,
        ]);

        return $boardData;
    }

    public function getPermissionStaffList($staff_id){
        $boardData = DB::select("
            Select a.permissionBit , a.permissionName, ifnull(b.permissionBit,0) as staffPermission
            From t_permission a left outer join t_staff_permission b
            On a.permissionBit = b.permissionBit and staff_id = :staff_id
        ",
            [
                "staff_id" => $staff_id,
            ]);

        return $boardData;
    }

    public function PermisstionList($page_no=1, Request $request){
        //접속 권한 체크
        $permissionCheck = $this->gc->getPermissionCheck(1);
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

        return view('Permission.list',[
            'boardList'=> $boardList,
            'page_start' => $page_start,
            'page_end' => $page_end,
            'previous' => $page_start > 1 ? true : false,
            'next' => ceil($page_count/$list_size) > $page_end ? true : false,
            'page_no' => $page_no,
        ]);
    }

    public function PermisstionWrite(Request $request){

        //접속 권한 체크
        $permissionCheck = $this->gc->getPermissionCheck(1);
        if(!$permissionCheck){
            return view('msg',[
                'msg' => '접속 권한이 없습니다.',
                'return_url' => '/main',
            ]);
        }

        return view('Permission.write',[

        ]);
    }

    public function getPermisstionLastBit(){
        $maxBit = DB::select("
                Select ifnull(max(permissionBit),0) as maxbit From t_permission
                ",
            [
            ]);

        return $maxBit[0]->maxbit;
    }

    public function PermisstionSet(Request $request){
        $permissionName = $request->permissionName;

        if(!isset($permissionName)){
            return response()->json([
                'result' => 'fail',
                'msg' => '필수 값이 누락 되었습니다.'
            ]);
        }

        $maxBit = $this->getPermisstionLastBit();
        if($maxBit == 0){
            $maxBit = 1;
            $setBit = $maxBit;
        }else{
            $setBit = $maxBit * 2;
        }


        $result = DB::table('t_permission')->insert([
            'permissionBit' => $setBit,
            'permissionName' => $permissionName,
            'regdt' => now(),
        ]);

        if (!$result){
            return response()->json([
                'result' => 'fail',
                'msg' => '데이터 등록에 실패 했습니다. 다시 시도하여 주세요'
            ]);
        }

        return response()->json([
            'result' => 'ok',
            'msg' => '등록 되었습니다.'
        ]);
    }

    public function permissionInfo($permissionBit){
        $permissionInfo = DB::select("
                Select permissionBit, permissionName From t_permission Where permissionBit = :permissionBit
                ",
            [
                "permissionBit" => $permissionBit,
            ]);

        return $permissionInfo[0];
    }

    public function PermisstionDetails(Request $request){

        //접속 권한 체크
        $permissionCheck = $this->gc->getPermissionCheck(1);
        if(!$permissionCheck){
            return view('msg',[
                'msg' => '접속 권한이 없습니다.',
                'return_url' => '/main',
            ]);
        }

        $permissionBit = $request->permissionBit;

        if(!isset($permissionBit)){
            return view('msg',[
                'msg' => '필수 값이 누락 되었습니다.',
                'return_url' => '/permission/list',
            ]);
        }

        $permissionInfo = $this->permissionInfo($permissionBit);

        return view('Permission.details',[
            'permissionInfo'=> $permissionInfo,
        ]);
    }

    public function PermisstionUpdate(Request $request){
        $permissionBit = $request->permissionBit;
        $permissionName = $request->permissionName;

        if(!isset($permissionName) || !isset($permissionbit)){
            return response()->json([
                'result' => 'fail',
                'msg' => '필수 값이 누락 되었습니다.'
            ]);
        }


        $result = DB::table('t_permission')->where('permissionBit', $permissionBit )->update([
            'permissionName' => $permissionName,
            'regdt' => now()
        ]);

        if (!$result){
            return response()->json([
                'result' => 'fail',
                'msg' => '데이터 수정에 실패 했습니다. 다시 시도하여 주세요'
            ]);
        }

        return response()->json([
            'result' => 'ok',
            'msg' => '수정 되었습니다.'
        ]);
    }

    public function PermisstionDel($permissionBit){
        $result = DB::table('t_permission')->where('permissionBit', $permissionBit )->delete();
        if (!$result){
            return false;
        }

        return true;
    }

    public function PermisstionDelete(Request $request){
        $permissionBit = $request->permissionBit;

        if(!isset($permissionBit) ){
            return response()->json([
                'result' => 'fail',
                'msg' => '필수 값이 누락 되었습니다.'
            ]);
        }

        //$result = DB::table('t_permission')->where('bit', $permissionBit )->delete();
        $result = $this->PermisstionDel($permissionBit);

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

    public function PermisstionList4Delete(Request $request){
        $val = explode("#", substr($request->arrPermissionBit,0,-1));

        foreach ($val as $value) {
            $result = $this->PermisstionDel($value);

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
}
