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
    <script type="text/javascript" src="/assets/scripts/main.js"></script>
    <script type="text/javascript" src="/js/jquery-3.6.0.min.js"></script>
<body>
<style>
    #loading {
        width: 100%;
        height: 100%;
        top: 0px;
        left: 0px;
        position: fixed;
        display: block;
        opacity: 0.7;
        background-color: #fff;
        z-index: 99;
        text-align: center;
    }

    #loading-image {
        //position: absolute;
        top: 50%;
        left: 50%;
        z-index: 100;
    }
</style>
    <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
        @yield('body')
    </div>
<div id="loading" style="display: none"><img id="loading-image" src="/img/loading_700.svg" alt="Loading..." /></div>
</body>
</html>
