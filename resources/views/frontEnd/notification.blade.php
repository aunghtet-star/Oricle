@extends('frontEnd.layouts.app')
@section('title','Notification')
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
                <div class="infinite-scroll">
                    @foreach ($notifications as $notification)
                        <a href="{{ url("/notificationDetail/$notification->id") }}">
                            <div class="card mb-2">
                                <div class="card-body p-2">
                                    <div class="d-flex">
                                        <div class="col-10 p-0">
                                            <h6>{{Illuminate\Support\Str::limit($notification->data['title'],40)}}</h6>
                                        </div>
                                        <div class="col-2 pl-4 pr-0 ">
                                            <i class="fas fa-bell @if(is_null($notification->read_at)) text-danger @endif"></i>
                                        </div>
                                    </div>
                                    <p class="mb-1">{{Illuminate\Support\Str::limit($notification->data['message'],100)}}</p>
                                    <p class="mb-1 text-muted">{{Carbon\Carbon::parse($notification->created_at)->format('Y-m-d h:i:s A')}}</p>
                                    
                                </div>
                            </div>
                        </a>
                        {{ $notifications->links() }}
                 @endforeach
              </div> 
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    $('ul.pagination').hide();
            $(function() {
                $('.infinite-scroll').jscroll({
                    autoTrigger: true,
                    loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
                    padding: 0,
                    nextSelector: '.pagination li.active + li a',
                    contentSelector: 'div.infinite-scroll',
                    callback: function() {
                        $('ul.pagination').remove();
                    }
                });
            });
</script>
@endsection