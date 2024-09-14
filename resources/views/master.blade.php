<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link href="{{asset('/css/home.css')}}" rel="stylesheet" />
    <link href="{{asset('/css/projects.css')}}" rel="stylesheet" />

    <link href="{{asset('/css/messages.css')}}" rel="stylesheet" />

</head>

<body class="antialiased">
    <div class="title-wrapper">
        <div class="container">
            <h1>@yield('page_title')</h1>
        </div>
    </div>
    <div class="title-buttons-wrapper">
        <div class="container">
            <form action="/">
                <button type="submit">Home</button>
            </form>
            <form action="/projects">
                <button type="submit">Projects</button>
            </form>
            <form action="/messages">
                <button type="submit">Messages</button>
            </form>
            <form action="/gallery">
                <button type="submit">Gallery</button>
            </form>
            <form action="/blog">
                <button type="submit">Blog</button>
            </form>
            <form action="/project_ideas">
                <button type="submit">Project Ideas</button>
            </form>

        </div>

        @if (session()->get('message'))
        <div class="container notifications">
            {!! session()->get('message') !!}
        </div>
        @endif
    </div>

    @yield('content')


</body>

</html>