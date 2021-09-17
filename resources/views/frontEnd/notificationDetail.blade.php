@extends('frontEnd.layouts.app')
@section('title','Notification Detail')
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
        <div class="card notification">
                <div class="card-body text-center">
                    <img src="{{asset('img/noti.png')}}" alt="">
                    <h5>{{$notification->data['title']}}</h5>
                    <p>{{$notification->data['message']}}</p>
                    <p><small class="text-muted">{{Carbon\Carbon::parse($notification->created_at)->format('Y-m-d h:i:s A')}}</small></p>
                    
                    <a href="{{$notification->data['web_link']}}" class="btn btn-theme btn-sm text-white">Back</a>
                </div> 
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    
</script>
@endsection