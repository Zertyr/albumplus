<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'album') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('css')
</head>
<body>
    <div id="app">
        <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        @admin
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle{{ currentRoute(route('category.create'),route('category.index'),route('category.edit', request()->category?: 0))}}" href="#" id="navbarDropdownGestCat" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    @lang('Administration')
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdownGestCat">
                                    <a class="dropdown-item" href="{{ route('category.create') }}">
                                        <i class="fas fa-plus fa-lg"></i> @lang('Ajouter une catégorie')
                                    </a>
                                    <a class="dropdown-item" href="{{ route('category.index') }}">
                                        <i class="fas fa-wrench fa-lg"></i> @lang('Gérer les catégorie')
                                    </a>
                                </div>
                            </li>
                        @endadmin
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item{{ currentRoute(route('login')) }}">
                                <a class="nav-link" href="{{ route('login') }}">@lang('Connexion')</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item{{ currentRoute(route('register')) }}">
                                    <a class="nav-link" href="{{ route('register') }}">@lang('Inscription')</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        @lang('Déconnexion')
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @if (session('ok'))
                <div class="container">
                    <div class="alert alert-dismissible alert-success fade show" role="alert">
                        {{ session('ok') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
    @yield('script')
  <script>
      $(() => {
          $('#logout').click((e) => {
              e.preventDefault()
              $('#logout-form').submit()
          })
          $('[data-toggle="tooltip"]').tooltip()
      })
  </script>
</body>
</html>
