@extends('type1')
@section('contents')
    <script>
        function staffSet() {
            if (!$('input[name=staffName]').val()) {
                alert('관리자명을 입력해주세요.');
                $('input[name=staffName]').focus();
                return;
            }

            if (!$('input[name=staffEmail]').val()) {
                alert('이메일을 입력해주세요.');
                $('input[name=staffEmail]').focus();
                return;
            }

            var setPermissionVal = "";

            $("input:checkbox[name=permissionVal]").each(function() {
                if($(this).is(":checked") == true) {
                    setPermissionVal = setPermissionVal + $(this).val()+"#";
                }
            });

            var result = confirm("등록 하시겠습니까?");
            if(result == true){
                $.ajax({
                    type : 'post',
                    url : '/staff/write',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType : 'json',
                    data: $.param({
                        staffName: $('input[name=staffName]').val(),
                        staffEmail: $('input[name=staffEmail]').val(),
                        setPermissionVal: setPermissionVal,
                    }),
                    success : function (data) {
                        if (data.result == 'ok') {
                            alert(data.msg);
                            location.href="/staff/list";
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

    </script>
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-drawer icon-gradient bg-happy-itmeo">
                        </i>
                    </div>
                    <div>관리자 관리
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

                        <form class="" id="staffFrom" name="staffFrom">
                            <div class="position-relative form-group">
                                <label for="staffName" class="">관리자명</label>
                                <input name="staffName" id="staffName" placeholder="관리자명을 입력하여 주세요" class="form-control" required>
                            </div>
                            <div class="position-relative form-group">
                                <label for="staffEmail" class="">관리자 이메일</label>
                                <input name="staffEmail" id="staffEmail" placeholder="관리자명을 입력하여 주세요" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="title">권한설정</label>
                                @foreach($permissionList as $row)
                                <div class="form-check">
                                    <input id="permission_{{$row->permissionBit}}" name="permissionVal" type="checkbox" value="{{$row->permissionBit}}" class="form-check-input"/>
                                    <label class="form-check-label" for="permission_{{$row->permissionBit}}">
                                        {{$row->permissionName}}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <button class="mb-2 mr-2 btn btn-primary float-right" type="button" onclick="staffSet();">등록</button>
                        </form>

                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
