@extends('layout.dashboard_basic')

@section('title', 'Woocommerce manager')

@section('body')
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th><abbr title="Website">Website</abbr></th>
                    <th><abbr title="CK">CK</abbr></th>
                    <th><abbr title="CS">CS</abbr></th>
                    <th><abbr title="Sync">Sync</abbr></th>
                    <th><abbr title="Delete">Delete</abbr></th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $web)
                    <tr>
                        <td>{{$web->base_url}}</td>
                        <td>{{$web->ck}}</td>
                        <td>{{$web->cs}}</td>
                        <td><a onclick="sync(this)" data-id="{{$web->id}}">Sync</a></td>
                        <td><a onclick="delete1(this)" data-id="{{$web->id}}">Delete</a></td>
                    </tr>
                @endforeach
        
            </tbody>
        </table>
    </div>
    
@endsection

@section('end_body')
    <script>
        function sync(element) {
            const form = document.createElement('form');
            form.method = 'post';
            form.action = "{{route('woocommerce_sync_site')}}"
            const csrf = document.createElement('input');
            csrf.type = 'hidden'
            csrf.name = '_token';
            csrf.value = '{{csrf_token()}}'
            form.appendChild(csrf)
            const hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = 'syncId';
            hiddenField.value = element.dataset.id
            form.appendChild(hiddenField);
            document.body.appendChild(form);
            form.submit();
        }
        function delete1(element) {
            const form = document.createElement('form');
            form.method = 'post';
            form.action = "{{route('woocommerce_delete_site')}}"
            const csrf = document.createElement('input');
            csrf.type = 'hidden'
            csrf.name = '_token';
            csrf.value = '{{csrf_token()}}'
            form.appendChild(csrf)
            const hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = 'delete';
            hiddenField.value = element.dataset.id
            form.appendChild(hiddenField);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endsection