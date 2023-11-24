@extends('layout.dashboard_basic')

@section('header')
    <style>
        table
        {
            table-layout:fixed;
            width:100%;
        }
        .table th:not([align]) {
            text-align: center;
        }
    </style>
@endsection

@section('body')
    <table class="table">
        <thead>
        <tr>
            <th><abbr title="ID">ID</abbr></th>
            <th><abbr title="Username">Username</abbr></th>
            <th><abbr title="Account cookie">Cookie</abbr></th>
            <th><abbr title="Delete">Delete</abbr></th>
        </tr>
        </thead>
        <tbody>
            <?php
                $pinterest_accounts = \App\Models\PinterestAccount::where('user_id', '=', \Illuminate\Support\Facades\Auth::user()['id'])->get();
            ?>
            @foreach($pinterest_accounts as $account)
                <tr style="overflow: scroll">
                    <th>{{$account['id']}}</th>
                    <th>{{$account['username']}}</th>
                    <th style="overflow: scroll">{{$account['cookie']}}</th>
                    <th style="text-align: center"><i data-pinterest-id="{{$account['id']}}" onclick="deleteAccount(this)" class="fa-solid fa-trash"></i></th>
                </tr>
            @endforeach

        </tbody>
    </table>
@endsection

@section('end_body')
    <script>
        function deleteAccount(el) {
            const form = document.createElement('form');
            form.method = 'post';
            form.action = '/dashboard/pinterest/delete'
            const csrf = document.createElement('input');
            csrf.type = 'hidden'
            csrf.name = '_token';
            csrf.value = '{{csrf_token()}}'
            form.appendChild(csrf)
            const hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = 'deletePinterest';
            hiddenField.value = el.getAttribute('data-pinterest-id')
            form.appendChild(hiddenField);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endsection
