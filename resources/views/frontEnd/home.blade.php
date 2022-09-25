@extends('frontEnd.layouts.app')
@section('title','KTU IT Pay')
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
    <div class="container mb-5">
        <div class="card mb-3">
            @include('frontEnd.layouts.flash')
            <div class="card-body">
                <div class="profile  mb-3">
                    <img src="https://ui-avatars.com/api/?background=584283&color=fff&name={{$user->name}}" alt="">
                    <h4>{{$user->name}}</h4>
                    <span class="text-muted">{{number_format($user->wallet ? $user->wallet->amount : 0) }} MMK</span>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mb-3">
            <div class="col-6 p-0 pr-3">
                <div class="card shortcut-box">
                    <a href="{{url('/scan_qr')}}">
                        <div class="card-body">
                            <img src="{{asset('/img/scanner.png')}}" alt="">
                            <span>Scan Pay</span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-6 p-0">
                <div class="card shortcut-box">
                    <a href="{{url('/receive_qr')}}">
                        <div class="card-body">
                            <img src="{{asset('/img/qr-code.png')}}" alt="">
                            <span>Receive QR</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-12 p-0">
            <div class="card function-bar">
                <div class="card-body pr-0">
                        <a href="{{route('transfer')}}" class="d-flex justify-content-between">
                        <span><img src="{{asset('img/money-transfer.png')}}" alt="">Transfer</span>
                        <span class="mr-3"><i class="fas fa-angle-right"></i></span>
                        </a>
                    <hr>
                        <a href="{{url('/wallet')}}" class="d-flex justify-content-between">
                        <span><img src="{{asset('img/wallet.png')}}" alt="">Wallet</span>
                        <span class="mr-3"><i class="fas fa-angle-right"></i></span>
                        </a>
                    <hr>
                        <a href="{{url('transaction')}}" class="d-flex justify-content-between">
                        <span><img src="{{asset('img/clock.png')}}" alt="">History</span>
                        <span class="mr-3"><i class="fas fa-angle-right"></i></span>
                        </a>
                </div>
            </div>
        </div>

    </div>
@endsection
