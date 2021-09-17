@extends('frontEnd.layouts.app')
@section('title','Transaction Detail')
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
    <div class="card transaction-detail">
        <div class="card-body">
            <div class="text-center mb-3">
                <img src="{{asset('img/transaction.png')}}" alt="">
            </div>
            @if (session('transfer_success'))
            <div class="alert alert-success p-2 text-center alert-dismissible fade show done" role="alert">
                <strong>{{ session('transfer_success') }} <img src="{{asset('/img/checked.png')}}" alt=""></strong>
            </div>
            @endif
            <p class="mb-4 text-center @if($transaction->type == 1) text-success @elseif($transaction->type == 2) text-danger @endif">{{number_format($transaction->amount)}}MMK</p>
            <div class="d-flex justify-content-between">
                <p class="mb-0 text-muted">Trx ID</p>
                <p class="mb-0">{{$transaction->trx_id}}</p>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <p class="mb-0 text-muted">Ref Number</p>
                <p class="mb-0">{{$transaction->ref_no}}</p>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <p class="mb-0 text-muted">Type</p>
                @if($transaction->type == 1) 
                <p class="mb-0 badge badge-pill badge-success">income <i class="fas fa-arrow-circle-left"></i></p>
                @elseif($transaction->type == 2) 
                <p class="mb-0 badge badge-pill badge-danger">expense <i class="fas fa-arrow-circle-right"></i></p>
                @endif
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <p class="mb-0 text-muted">Amount</p>
                <p class="mb-0">{{number_format($transaction->amount)}} <small>MMK</small> </p>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <p class="mb-0 text-muted">Date and Time</p>
                <p class="mb-0">{{$transaction->created_at}}</p>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <p class="mb-0 text-muted">
                    @if($transaction->type == 1) From @elseif($transaction->type == 2) To @endif
                </p>
                <p class="mb-0">{{$transaction->source ? $transaction->source->name : '-'}}</p>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <p class="mb-0 text-muted">Description</p>
                <p class="mb-0">{{$transaction->description}}</p>
            </div>
        </div>
    </div>
</div>
    
@endsection
