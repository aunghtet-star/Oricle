@extends('frontEnd.layouts.appplain')
@section('title', 'login')
@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center" style="height:100vh;">
            <div class="col-md-6">
                <div class="card auth-form">
                    <div class="card-body">
                        <h3 class="text-center">Login</h3>
                        <p class="text-center text-muted">Fill the form to login</p>
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="form-group">
                                <label for="">Phone</label>
                                <input type="phone" name="phone" class="form-control @error('phone') is-invalid @enderror">
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="">Password</label>
                                <input type="password" name="password" value="{{ old('email') }}"
                                    class="form-control @error('password') is-invalid @enderror">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-theme btn-block my-4">Login</button>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('register') }}">Register Now</a>
                                <a href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
