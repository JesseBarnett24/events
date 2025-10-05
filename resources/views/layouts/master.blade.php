<!DOCTYPE html>
<html>
<head>
  <title>@Yield('Title')</title>
  <meta charset="utf-8">
  @vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body>
  @auth
    {{Auth::user()->name}}
    <form method ="POST" action ="{{url('/logout')}}">
      {{csrf_field()}}
      <input type="submit" value="Logout">
    </form>
  @else
    <a href="{{route('login')}}">Log in</a>
    <a href="{{route('register')}}">Register</a>
  @endauth
    @yield('content')
    @include('layouts.greetingFooter')
</body>
</html>
