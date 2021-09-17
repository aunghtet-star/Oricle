@extends('backEnd.layouts.app')
@section('title', 'AdminUserManagement')
@section('n_user', 'mm-active')
@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-user icon-gradient bg-mean-fruit">
                        </i>
                    </div>
                    <div>User Management</div>
                </div>
            </div>
        </div>

        <div class="pt-3">
            <a href="{{ route('admin.n_users.create') }}" class="btn btn-primary"><i class="fas fa-plus-circle"></i>Create
                User</a>
        </div>
        <div class="content py-3">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered Datatable">
                        <thead>
                            <tr class="bg-primary">
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Ip</th>
                                <th>User_agent</th>
                                <th>Login At</th>
                                <th>Created_at</th>
                                <th>Updated_at</th>
                                <th>Action</th>
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
                "ajax": "/admin/n_users/datatables/ssd",
                columns: [{
                        data: "name",
                        name: "name"
                    },
                    {
                        data: "email",
                        name: "email"
                    },
                    {
                        data: "phone",
                        name: "phone"
                    },
                    {
                        data: "ip",
                        name: "ip"
                    },
                    {
                        data: "user_agent",
                        name: "user_agent",
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: "login_at",
                        name: "login_at"
                    },
                    {
                        data: "created_at",
                        name: "created_at"
                    },
                    {
                        data: "updated_at",
                        name: "updated_at"
                    },
                    {
                        data: "action",
                        name: "action",
                        searchable: false,
                        sortable: false
                    }
                ],
                order: [
                    [6, "desc"]
                ]

            });

            $(document).on('click', '.delete', function(e) {
                e.preventDefault();
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Do you want to delete?',

                    showCancelButton: true,
                    confirmButtonText: `Confirm`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/admin/n_users/' + id,
                            type: 'DELETE',
                            success: function() {
                                table.ajax.reload();
                            }
                        })
                    }
                })
            });
        });

    </script>
@endsection
