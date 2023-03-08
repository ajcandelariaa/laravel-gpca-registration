<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard</title>
</head>
<body>
    <h1>Admin Dashboard</h1>
    @if(Session::has('success'))
    <div class="alert alert-success">
        {{Session::get('success')}}
    </div>
    @endif

    <a href="/admin/event">Manage Events</a>
    <br>
    <a href="/admin/member">Manage Members</a>
    <br>
    <a href="/admin/logout">Logout</a>
</body>
</html>