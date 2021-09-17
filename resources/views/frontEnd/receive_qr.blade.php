@extends('frontEnd.layouts.app')
@section('title','Receive QR')
@section('extra_css')
    <style>
        body {
            background: #EDEDF5;
            font-family: "Oswald", sans-serif;
        }

        .bottom-menu a {
            text-decoration: none;
        }

        .header-menu a {
            text-decoration: none;
        }

    </style>
@endsection
@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="text-center">
                <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(250)->generate($authUser->phone)) !!} ">
            </div>
            <p class="text-center">{{$authUser->name}}</p>
            <p class="text-center">{{$authUser->phone}}</p>
        </div>
    </div>
</div>
@endsection
