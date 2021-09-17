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
                <form action="{{url('/scan_and_pay_confirm')}}" method="POST">
                    @csrf
                    <input type="hidden" name="to_phone" value="{{$to_account->phone}}">
                    <div class="form-group">
                        <label for=""><strong>From</strong></label>
                        <p class="text-muted mb-1">{{$from_account->name}}</p>
                        <p class="text-muted mb-1">{{$from_account->phone}}</p>
                    </div>
                    <div class="form-group">
                        <label for=""><strong>To</strong></label>
                        <p class="text-muted mb-1">{{$to_account->name}}</p>
                        <p class="text-muted mb-1">{{$to_account->phone}}</p>
                    </div>
                    <div class="form-group">
                        <label for=""><strong>Amount(MMK)</strong></label>
                        <input type="number" name="amount" class="form-control"  value="{{ old('amount') }}">
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
    
</script>
@endsection