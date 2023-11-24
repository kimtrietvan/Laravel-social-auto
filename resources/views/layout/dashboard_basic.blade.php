<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard</title>
    @yield('header')
</head>
<body>
    <nav class="navbar is-success">
        <div class="navbar-menu">
            <div class="navbar-end">
                <div class="navbar-item has-dropdown is-hoverable">
                    <a class="navbar-link">
                        {{Auth::user()['name']}}
                    </a>
                    <div class="navbar-dropdown">
                        <a class="navbar-item" href="/logout">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <div class="columns">
        <div class="column is-success is-one-fifth">
            <aside class="menu">
                <p class="menu-label">
                    Basic
                </p>
                <ul class="menu-list">
                    <li><a href="/dashboard">Dashboard</a></li>
                    <li><a>Subscription</a></li>
                </ul>
                <p class="menu-label">
                    Social manager
                </p>
                <ul class="menu-list">

                    <li>
                        <a>Pinterest</a>
                        <ul>
                            <li><a href="{{route('pinterest_add_account')}}">Add account</a></li>
                            <li><a href="{{route('pinterest_account_manager')}}">Manager account</a></li>
{{--                            <li><a>Add a member</a></li>--}}
                        </ul>
                    </li>
                </ul>
                <p class="menu-label">
                    Woocommerce
                </p>
                <ul class="menu-list">
                    <a href="{{route('woocommerce_add_site')}}">Add site</a>
                    <a href="{{route('woocommerce_manager')}}">Manager</a>
                    <a href="{{route('woocommerce_product')}}">Products</a>
                    
                </ul>


            </aside>
        </div>
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
        <div class="column">
            @yield("body")
        </div>
    </div>
    @yield('end_body')
</body>
</html>
