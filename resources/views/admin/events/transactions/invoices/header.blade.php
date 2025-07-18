<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Invoice</title>

    <style>
        html,
        body {
            margin: 15px;
            padding: 10px;
            font-family: sans-serif;
        }
        
        /* GPCA LOGO */
        .logo {
            text-align: right;
            margin-right: 20px;
        }

        .logo img {
            width: 140px;
        }
    </style>
</head>

<body>
    <div class="logo">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/assets/images/invoice_logo.png'))) }}"
            alt="gpca-logo">
    </div>
</body>

</html>
