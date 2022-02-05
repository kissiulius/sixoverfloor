@extends('foundation')
@section('body')

    @include('topbar')
    <div class="app-main">
        @include('leftmenu')
        <div class="app-main__outer">
            @yield('contents')

            @include('footer')

        </div>
    </div>
@endsection

