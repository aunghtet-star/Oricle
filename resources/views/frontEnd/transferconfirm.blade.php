@extends('frontEnd.layouts.app')
@section('title','Transfer Confirmation')
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
        p {
            margin: 0;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="card">
            @include('frontEnd.layouts.flash')
            <div class="card-body">
                <form action="{{route('transfer.complete')}}" method="POST" id="form">
                    @csrf
                    <input type="hidden" name="phone" value="{{$phone}}">
                    <input type="hidden" name="amount" value="{{$amount}}">
                    <input type="hidden" name="description" value="{{$description}}">
                    <div class="form-group">
                        <label for=""><strong>From</strong></label>
                        <p class="text-muted mb-1">{{$user->name}}</p>
                        <p class="text-muted mb-1">{{$user->phone}}</p>
                    </div>
                    <div class="form-group">
                        <label for=""><strong>To</strong></label>
                        <p class="text-muted">{{$phone}}</p>
                        <p class="text-muted">{{$to_account->name}}</p>
                    </div>
                    <div class="form-group">
                        <label for=""><strong>Amount(MMK)</strong></label>
                        <p>{{$amount}} <span>MMK</span> </p>
                    </div>
                    <div class="form-group">
                        <label for=""><strong>Description</strong></label>
                        <p>{{$description}}</p>
                    </div>
                    <button type="submit" class="btn-block btn btn-theme mt-4 confirm-btn">Confirm</button>
                    
                </form>
            </div>
        </div>
    </div>
    
@endsection
@section('scripts')
<script>
    $(document).ready(function(){
        $('.confirm-btn').on('click',function(e){
            e.preventDefault();
            Swal.fire({
                title: '<strong>Please fill your password</strong>',
                icon: 'info',
                html: '<input type="password" class="form-control text-center password">',
                    
                showCloseButton: true,
                showCancelButton: true,
                focusConfirm: false,
                reverseButtons: true,
                confirmButtonText: 'Confirm',
            }).then((result) => {
                if (result.isConfirmed) {
                    var password = $('.password').val();
                    $.ajax({
                    url: '/password_check?password=' + password,
                    type: 'POST',
                    success: function(res) {
                        if(res.status == 'success'){
                            $('#form').submit();
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: res.message ,
                                })
                        }
                }
            })
                }
            })

        })
    })
</script>
@endsection