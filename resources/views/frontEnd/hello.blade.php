@extends('frontEnd.layouts.app')
@section('title', 'Transaction')
@section('content')
<div class="container">
        <div class="card mb-3 transaction">
            <div class="card-body">
                <h6><i class="fas fa-sort-amount-up-alt"></i> Filter</h6>
                <div class="row">
                    <div class="col-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <label class="input-group-text type-padding">Date</label>
                            </div>
                            <input type="text" class="form-control date" value="{{request()->date  }}" placeholder="All">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <label class="input-group-text type-padding">Type</label>
                            </div>
                            <select class="custom-select type">
                                <option value="">All</option>
                                <option value="1" @if(request()->type == 1) selected @endif >Income</option>
                                <option value="2" @if(request()->type == 2) selected @endif>Expense</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>
        </div>
<h6 class="ml-3">Transactions</h6>
    <div class="infinite-scroll">
            @foreach ($transactions as $transaction)
                <a href="{{ url("/transactionDetail/".$transaction->trx_id) }}">
                    <div class="card mb-2">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-1"><span>Trx ID : </span>{{ $transaction->trx_id }}</h6>
                                <p class="mb-1 @if ($transaction->type == 1) text-success
                                @elseif($transaction->type == 2) text-danger @endif">
                                    @if ($transaction->type == 1)
                                        +
                                    @elseif ($transaction->type==2)
                                        -
                                    @endif
                                    {{ $transaction->amount }} <small>MMK</small>
                                </p>
                            </div>
                            <p class="mb-1 text-muted">
                                @if ($transaction->type == 1)
                                    From -
                                @elseif($transaction->type == 2)
                                    To -
                                @endif
                                {{ $transaction->source ? $transaction->source->name : '-' }}
                            </p>
                            <p class="text-muted mb-1">
                                {{ $transaction->created_at }}
                            </p>
                        </div>
                    </div>
                </a>
        {{ $transactions->links() }}
        @endforeach
    </div>  
</div>
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
            $('.date').daterangepicker({
                "singleDatePicker": true,
                "autoApply": false,
                "autoUpdateInput" :false,
                "locale": {
                    "format": "YYYY/MM/DD",
            },
            });
            
            $('.date').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD'));
                var date = $('.date').val();
                var type = $('.type').val();
                history.pushState(null, '' , `?date=${date}&type=${type}`);
                window.location.reload();
            });
            $('.date').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                var date = $('.date').val();
                var type = $('.type').val();
                history.pushState(null, '' , `?date=${date}&type=${type}`);
                window.location.reload();
            });
        
            $('.type').change(function(){
                var date = $('.date').val();
                var type = $('.type').val();
                history.pushState(null, '' , `?date=${date}&type=${type}`);
                window.location.reload();
            })
        </script>
@endsection