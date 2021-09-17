@extends('frontEnd.layouts.app')
@section('title','Wallet')
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
        <div class="card mycard">
            <div class="card-body">
                <div class="mb-3">
                    <span>Balance</span>
                    <h4>{{number_format($user->wallet ? $user->wallet->amount : 0) }} <span>MMK</span> </h4>
                </div>
                <div class="mb-3">
                    <span>Account Number</span>
                    <h4>{{$user->wallet ? $user->wallet->account_numbers : '-'}}</h4>
                </div>
                <div>
                    <p>{{$user->name}}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
