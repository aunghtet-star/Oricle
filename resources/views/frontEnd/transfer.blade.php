@extends('frontEnd.layouts.app')
@section('title','Transfer')
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
                <form action="{{route('transfer.confirm')}}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for=""><strong>From</strong></label>
                        <p class="text-muted mb-1">{{$user->name}}</p>
                        <p class="text-muted mb-1">{{$user->phone}}</p>
                    </div>

                    <label for=""><strong>To</strong><span class="text-success toaccount"></span></label>
                    <div class="input-group mb-3">
                        <input type="text" name="phone" class="form-control phone" value="{{ old('phone') }}" autocomplete="off">
                            <div class="input-group-append">
                                <span class="input-group-text btn check-btn"><i class="fas fa-check"></i></span>
                            </div>
                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                    </div>
                    <div class="form-group">
                        <label for=""><strong>Amount(MMK)</strong></label>
                        <input type="number" name="amount" class="form-control" value="{{ old('amount') }}">
                        @error('amount')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for=""><strong>Description</strong></label>
                        <textarea name="description" class="form-control" rows="4">{{old('description')}}</textarea>
                    </div>
                    <button type="submit" class="btn-block btn btn-theme mt-4">Continue</button>
                    
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    $(document).ready(function(){
        $('.check-btn').on('click',function(){
            var phone = $('.phone').val();
            $.ajax({
                url: '/toaccountVerify?phone=' + phone,
                type: 'GET',
                success: function(res) {
                    console.log(res);
                    if( res.status == 'success'){
                        $('.toaccount').text('('+ res.data.name+')');
                    }else{
                        $('.toaccount').text('('+ res.message+')');
                    }
                }
            })
        })
    })
</script>
@endsection