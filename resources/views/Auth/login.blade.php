@if(Auth::check())
    <script>
        location.href="/main";
    </script>
@else
<!doctype html>
<html lang="ko">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>S.O.F SYSTEM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="description" content="This is S.O.F SYSTEM">
    <meta name="msapplication-tap-highlight" content="no">
    <link href="/css/main.css" rel="stylesheet"></head>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript" src="/assets/scripts/main.js"></script>
<body>
<script>
    function sof_login() {
        //console.log(document.referrer);
        if (!$('input[name=email]').val()) {
            alert('이메일을 입력해주세요.');
            $('input[name=email]').focus();
            return;
        }
        if (!$('input[name=password]').val()) {
            alert('비밀번호를 입력해주세요.');
            $('input[name=password]').focus();
            return;
        }
        var referrer = document.referrer;
        //var referrer = '{{url()->previous()}}';
        //console.log(referrer);
        $.ajax({
            type : 'post',
            url : '/login',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType : 'json',
            data: $.param({
                email: $('input[name=email]').val(),
                password: $('input[name=password]').val()
            }),
            success : function (data) {
                if (data.result == 'ok') {
                    location.href="/main";
                } else {
                    alert('아이디와 비밀번호를 확인해주세요.');
                }
            },
            error: function(request,status,error) {
                //alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });
    }

    $(function() {
        $('input[name=email]').keyup(function(key) {
            if (key.keyCode == 13) {
                if ($('input[name=email]').val()) {
                    $('input[name=password]').focus();
                }
            }else{
                var input_txt = $(this).val();
                var input_txt = input_txt.toLowerCase();
                $('input[name=email]').val(input_txt);
            }
        });
        $('input[name=password]').keyup(function(key) {
            if (key.keyCode == 13) {
                if ($('input[name=password]').val()) {
                    sof_login();
                }
            }else{
                var input_txt = $(this).val();
                $('input[name=password]').val(input_txt);
            }
        });
        $('#login_btn').on("click", function() {
            sof_login();
        });
    });

</script>
<div id="login">
    <h3 class="text-center text-white pt-5">Login form</h3>
    <div class="container">
        <div id="login-row" class="row justify-content-center align-items-center">
            <div id="login-column" class="col-md-6">
                <div id="login-box" class="col-md-12">
                    <form id="login-form" class="form" action="" method="post">
                        <h3 class="text-center text-info"> S.O.F Login</h3>
                        <div class="form-group">
                            <label for="email" class="text-info">Email:</label><br>
                            <input type="text" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password" class="text-info">Password:</label><br>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <input type="button" type="button" id="login_btn" class="btn btn-info btn-md" value="로그인">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
@endif
