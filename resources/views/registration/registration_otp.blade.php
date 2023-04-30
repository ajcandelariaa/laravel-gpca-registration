<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<style>
    #redirectTo3ds1AcsSimple {
        height: 90vh;
        margin: 0px auto;
    }

    .top-message {
        width: 100%;
        max-width: 940px;
        margin: 10px auto;
        text-align: center;
        font-size: 20px;
        line-height: 22px;
    }
    
</style>

<body>

    <div class="top-message">
        <p>Bank payment authorization is in process please do not close or refresh this page at any case after the authorization process is complete the page will be redirected automatically to the payment confirmation page.</p>
    </div>

    {!! $htmlCode !!}

    <script>
        if (self === top) {

        } else {
            top.location = self.location;
        }
    </script>
</body>

</html>
