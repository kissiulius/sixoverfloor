@extends('type1')
@section('contents')
    <script>
        function moveWrite(){
            location.href="/staff/write";
        }

        function staffDelete() {
            var deleteVal = "";

            $("input:checkbox[name=staffId]").each(function() {
                if($(this).is(":checked") == true) {
                    deleteVal = deleteVal + $(this).val()+"#";

                }
            });

            if(deleteVal == ""){
                alert("삭제할 목록을 선택 하여주세요");
                return;
            }

            var result = confirm("삭제 하시겠습니까?");
            if(result == true){
                $.ajax({
                    type : 'delete',
                    url : '/staff/list4delete',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType : 'json',
                    data: $.param({
                        arrStaff: deleteVal
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
                        <div class="container float-right">
                            <button class="mb-2 mr-2 btn btn-danger float-right" onclick="staffDelete();">삭제</button>
                            <button class="mb-2 mr-2 btn btn-primary float-right" type="button" onclick="moveWrite();">등록</button>
                        </div>
                        <table class="mb-0 table table-striped">
                            <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="45%">이름</th>
                                <th width="50%">이메일</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($boardList as $row)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="staffId" id="staffId" value="{{$row->id}}">
                                    </td>
                                    <td><a href="/staff/details/{{$row->id}}">{{$row->name}}</a></td>
                                    <td><a href="/staff/details/{{$row->id}}">{{$row->email}}</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="container row" style="float: none; margin:0 auto;">
                        <div class="mb-3" style="float: none; margin:0 auto;">
                            <nav class="" >
                                <ul class="pagination">
                                    @if($previous)
                                        <li class="page-item"><a href="/staff/list/{{sprintf('%s', $page_start-1)}};" class="page-link" aria-label="Previous"><span aria-hidden="true">«</span><span class="sr-only">Previous</span></a></li>
                                    @endif
                                    @for ($no=$page_start;$no<=$page_end;$no++)
                                        <li class="page-item @if($page_no == $no) active @endif"><a href="/staff/list/{{sprintf('%s', $no)}};" class="page-link" >{{ $no }}</a></li>
                                    @endfor
                                    @if($next)
                                        <li class="page-item"><a href="/staff/list/{{sprintf('%s', $page_end+1)}}" class="page-link" aria-label="Next"><span aria-hidden="true">»</span><span class="sr-only">Next</span></a></li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
