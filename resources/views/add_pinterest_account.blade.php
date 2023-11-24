@extends('layout.dashboard_basic')

@section('title', 'Add account')

@section('body')
    <section class="section">
        <div class="container">
            <form method="post" action="{{route('pinterest_add_account')}}">
                {{csrf_field()}}
                <div class="field">
                    <label class="label">Cookie Input</label>
                    <div class="control">
                        <input class="input" name="cookie" type="text" placeholder="Enter cookie" id="cookie">
                    </div>
                </div>

                <div class="field">
                    <label class="label">Proxy Input</label>
                    <div class="control">
                        <input class="input" type="text" placeholder="Enter proxy" id="proxy">
                    </div>
                </div>
                <div id="noti" style="display: none" class="notification">
                    <button class="delete"></button>
                    <span id="noti-message"></span>
                </div>
                <div class="field">
                    <input class="button" id="check-cookie-input" type="button" onclick="check_pin_account()" name="cookie-input" value="Check Cookie">
                </div>
                <div class="field is-grouped">

                    <div class="control">
                        <button id="submit-button" type="submit" class="button is-link">Add</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('end_body')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            (document.querySelectorAll('.notification .delete') || []).forEach(($delete) => {
                const $notification = $delete.parentNode;

                $delete.addEventListener('click', () => {
                    $notification.parentNode.removeChild($notification);
                });
            });
        });

        function check_pin_account() {
            const cookie = document.querySelector('#cookie');
            const proxy = document.querySelector('#proxy');
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // The data we are going to send in our request
            let data = {
                cookie: cookie.value,
                proxy: proxy.value
            };

            // The parameters we are gonna pass to the fetch function
            let fetchData = {
                method: 'POST',
                body: JSON.stringify(data),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token   // Include the CSRF token in the request header
                }
            };

            fetch('{{route('check_account_pinterest')}}', fetchData)
                .then(response => {
                    if (!response.ok) {
                        throw new Error("HTTP error " + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data);
                    document.getElementById('noti').style.display = 'block'
                    document.getElementById('noti').classList.remove('is-danger')
                    document.getElementById('noti').classList.add('is-success')
                    document.getElementById('noti-message').innerText = JSON.stringify(data)
                })
                .catch(function(error) {
                    console.log("Fetch failed: ", error);
                    document.getElementById('noti').style.display = 'block'
                    document.getElementById('noti').classList.remove('is-success')
                    document.getElementById('noti').classList.add('is-danger')
                    document.getElementById('noti-message').innerText = error
                });
        }
    </script>
@endsection
