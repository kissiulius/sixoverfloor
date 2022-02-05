@extends('type1')
@section('contents')
    <script>
        alert('{{$msg}}');
        location.href='{{$return_url}}';
    </script>
@endsection
