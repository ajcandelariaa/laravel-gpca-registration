<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Login</title>
    @vite('resources/css/app.css')
</head>
<body>
    <h1>Admin Login</h1>
    <form action="/admin/login" method="POST">
        @csrf
        <input type="text" name="username" id="" placeholder="username">
        <input type="password" name="password" id="" placeholder="password">
        <input type="submit" value="Login">
    </form>

    @if(Session::has('fail'))
        <div class="alert alert-danger">
            {{Session::get('fail')}}
        </div>
    @endif

    @if(Session::has('success'))
        <div class="alert alert-danger">
            {{Session::get('success')}}
        </div>
    @endif
</body>
</html>