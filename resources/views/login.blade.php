@extends('layout.base')

@section('title', 'Login')

@section('body')
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
            <div class="columns is-centered">
                <div class="column is-one-third">
                    <h1 class="title has-text-centered">Login</h1>
                    <form method="post" action="/login">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <div class="field">
                            <label class="label">Email</label>
                            <div class="control">
                                <input class="input" name="email" type="text" placeholder="Email">
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">Password</label>
                            <div class="control">
                                <input class="input" name="password" type="password" placeholder="Password">
                            </div>
                        </div>

                        <div class="field">
                            <div class="control">
                                <label class="checkbox">
                                    <input name="remember" type="checkbox"> Remember me
                                </label>
                            </div>
                        </div>

                        <div class="field is-grouped">
                            <div class="control">
                                <button class="button is-link">Login</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <div class="has-text-centered">
        <a href="{{route('register')}}">If you dont have a account, try register</a>
    </div>
@endsection
