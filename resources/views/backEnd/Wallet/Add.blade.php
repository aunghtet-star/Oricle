@extends('backEnd.layouts.app')
@section('title', 'Add Money')
@section('wallet', 'mm-active')
@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-user icon-gradient bg-mean-fruit">
                        </i>
                    </div>
                    <div>Add Money</div>
                </div>
            </div>
        </div>
        <div class="content pt-3">
            <div class="card">
                <div class="card-body">
                    @include('frontEnd.layouts.flash')
                    <form action="{{route('admin.wallet.add.store')}}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="">User</label>
                        <select name="user_id" class="form-control add">
                            <option value="">Select User</option>
                            
                            @foreach ($users as $user)
                                <option value="{{$user->id}}">{{$user->name}} ({{$user->phone}})</option>
                            @endforeach
                        </select>
                        </div>
                        <div class="form-group">
                            <label for="">Amount</label>
                            <input type="number" name="amount" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">description</label>
                            <textarea name="description" rows="4" class="form-control"></textarea>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button class="btn btn-secondary mr-3 back-btn">Cancel</button>
                            <button type="submit" class="btn btn-primary">Confirm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    {!! JsValidator::formRequest('App\Http\Requests\StoreAdminUser', '#create') !!}

    <script>
        $(document).ready(function() {
            $('.add').select2();
            theme: 'bootstrap4',
        });

    </script>
@endsection
