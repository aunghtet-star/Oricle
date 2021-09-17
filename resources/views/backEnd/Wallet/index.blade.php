@extends('backEnd.layouts.app')
@section('title', 'Wallet')
@section('wallet', 'mm-active')
@section('content')
    <div class="app-main__inner">
        <div class="app-page-title mb-3">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-wallet icon-gradient bg-mean-fruit">
                        </i>
                    </div>
                    <div>Wallets</div>
                </div>
            </div>
        </div>

        <a href="{{route('admin.wallet.add')}}" class="btn btn-success btn-sm"><i class="fas fa-plus-circle"></i> Add money</a>
        <a href="{{route('admin.wallet.reduce')}}" class="btn btn-danger btn-sm"><i class="fas fa-minus-circle"></i> Reduce money</a>

        <div class="content py-3">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered Datatable">
                        <thead>
                            <tr class="bg-primary">
                                <th>Account Number</th>
                                <th>Account Person</th>
                                <th>Amount(MMK)</th>
                                <th>Created at</th>
                                <th>Updated at</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let token = document.head.querySelector('meta[name="csrf-token"]')
            if (token) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF_TOKEN': token.content
                    }
                })
            }
            var table = $('.Datatable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": "/admin/wallet/datatables/ssd",
                columns: [{
                        data: "account_numbers",
                        name: "account_numbers"
                    },
                    {
                        data: "account_person",
                        name: "account_person"
                    },
                    {
                        data: "amount",
                        name: "amount"
                    },
                    {
                        data: "created_at",
                        name: "created_at"
                    },
                    {
                        data: "updated_at",
                        name: "updated_at"
                    },
                ],
                order: [
                    [4, "desc"]
                ]

            });
        });

    </script>
@endsection
