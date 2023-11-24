@extends('layout.base')

{{--@section('title', 'Register')--}}

@section("body")
    @if ( $errors->count() > 0 )
        <article class="message is-danger">
            <div class="message-header">
                <p>The following errors have occurred:</p>
                <button class="delete" aria-label="delete"></button>
            </div>
            <div class="message-body">
                <ul>
                    @foreach( $errors->all() as $message )
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        </article>
    @endif
    <section class="section">
        <div class="container">
            <div class="columns">
                <div class="column is-half is-offset-one-quarter">
                    <h2 class="title has-text-centered">Register</h2>

                    <form action="/register" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <div class="field">
                            <label class="label" for="name">Name</label>
                            <div class="control">
                                <input class="input" type="text" id="name" name="name" placeholder="Full Name">
                            </div>
                        </div>

                        <div class="field">
                            <label class="label" for="email">Email</label>
                            <div class="control">
                                <input class="input" type="email" id="email" name="email" placeholder="Email Address">
                            </div>
                        </div>

                        <div class="field">
                            <label class="label" for="password">Password</label>
                            <div class="control">
                                <input class="input" type="password" onkeyup="check()" id="password" name="password" placeholder="Password">
                            </div>
                        </div>

                        <div class="field">
                            <label class="label" for="confirm_password">Confirm Password</label>
                            <div class="control">
                                <input class="input" type="password" onkeyup="check()" id="confirm_password" name="confirm_password" placeholder="Confirm Password">
                                <span id="message"></span>
                            </div>

                        </div>

                        <div class="field">
                            <div class="control">
                                <button disabled id="register" class="button is-link">Register</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <div class="has-text-centered">
        <a href="{{route('login')}}">If you have a account, try login</a>
    </div>
    <script>
        var check = function() {
            if (document.getElementById('password').value == '' || document.getElementById('confirm_password').value == '') {
                document.getElementById('register').disabled = true;
            }

            if (document.getElementById('password').value ==
                document.getElementById('confirm_password').value) {
                document.getElementById('message').style.display = 'none';
                document.getElementById('message').innerHTML = '';
                document.getElementById('register').disabled = false;
            } else {
                document.getElementById('message').style.color = 'red';
                document.getElementById('message').style.display = '';
                document.getElementById('message').innerHTML = 'Password not matching';
                document.getElementById('register').disabled = true;
            }


        }



    </script>
@endsection
