@extends('frontEnd.layouts.app')
@section('title','profile')
@section('extra_css')

    <style>
        body {
            background: #EDEDF5;
            font-family: "Oswald", sans-serif;
        }
        a {
            text-decoration: none;
            color: #333;
        }
        a:hover {
            text-decoration: none;
            color: #584283;
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
        <div class="d-flex justify-content-center">
            <div class="col-md-8">
                <div class="account">
                    <div class="profile  mb-3">
                        <img src="https://ui-avatars.com/api/?background=584283&color=fff&name={{$user->name}}" alt="">
                    </div>
                    <div class="card mb-3">
                        <div class="card-body pr-0">
                            <div class="d-flex justify-content-between">
                                <span>Username</span>
                                <span class="mr-3">{{$user->name}}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span>Phone</span>
                                <span class="mr-3">{{$user->phone}}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span>Email</span>
                                <span class="mr-3">{{$user->email}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body pr-0">
                            <a href="{{route('updatePassword')}}" class="d-flex justify-content-between">
                                <span>Update Password</span>
                                <span class="mr-3"><i class="fas fa-angle-right"></i></span>
                            </a>
                            <hr>
                            <a href="" class="d-flex justify-content-between logout">
                                <span>Logout</span>
                                <span class="mr-3"><i class="fas fa-angle-right"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    $(document).ready(function(){
        $(document).on('click', '.logout', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Do you want to Logout?',
                    showCancelButton: true,
                    confirmButtonText: `Confirm`,
                    reverseButtons : true,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{route('logout')}}",
                            type: 'POST',
                            success: function(res) {
                                window.location.replace('{{route('profile')}}')
                            }
                        })
                    }
                })
            });
    })
</script>   
@endsection
