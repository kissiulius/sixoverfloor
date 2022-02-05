@extends('type1')
@section('contents')
    <script>
        function permisstionUpdate() {
            if (!$('input[name=permissionName]').val()) {
                alert('권한명을 입력해주세요.');
                $('input[name=permissionName]').focus();
                return;
            }

            var result = confirm("수정 하시겠습니까?");
            if(result == true){
                $.ajax({
                    type : 'post',
                    url : '/permission/update',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType : 'json',
                    data: $.param({
                        permissionBit: '{{$permissionInfo->permissionBit}}',
                        permissionName: $('input[name=permissionName]').val()
                    }),
                    success : function (data) {
                        if (data.result == 'ok') {
                            alert(data.msg);
                            location.reload();
                        } else {
                            alert(data.msg);
                            return;
                        }
                    },
                    error: function(request,status,error) {
                        //alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                    }
                });
            }
        }
        function permisstionDelete() {
            var result = confirm("삭제 하시겠습니까?");
            if(result == true){
                $.ajax({
                    type : 'delete',
                    url : '/permission/delete',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType : 'json',
                    data: $.param({
                        permissionBit: '{{$permissionInfo->permissionBit}}'
                    }),
                    success : function (data) {
                        if (data.result == 'ok') {
                            alert(data.msg);
                            permisstionList();
                        } else {
                            alert(data.msg);
                            return;
                        }
                    },
                    error: function(request,status,error) {
                        //alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                    }
                });
            }
        }

        function permisstionList(){
            location.href="/permission/list";
        }

    </script>
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-drawer icon-gradient bg-happy-itmeo">
                        </i>
                    </div>
                    <div>관리자 권한 관리
                        <div class="page-title-subheading">허가된 사용자만 이용 하세요
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-lg-12">
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <form class="" id="permissionFrom" name="permissionForm">
                            <div class="position-relative form-group">
                                <label for="permissionName" class="">권한명</label>
                                <input name="permissionName" id="permissionName" placeholder="권한명을 입력하여 주세요" class="form-control" value="{{$permissionInfo->permissionName}}" required>
                            </div>
                        </form>
                        <button class="mb-2 mr-2 btn btn-primary float-right" type="button" onclick="permisstionUpdate();">수정</button>
                        <button class="mb-2 mr-2 btn btn-danger float-right" type="button" onclick="permisstionDelete();">삭제</button>
                        <button class="mb-2 mr-2 btn btn-secondary float-right" type="button" onclick="permisstionList();">리스트</button>

                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
