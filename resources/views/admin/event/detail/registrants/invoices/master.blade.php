<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Invoice</title>

    <style>
        html, body {
            margin: 15px;
            padding: 10px;
            font-family: sans-serif;
        }



        /* GPCA LOGO */
        .logo{
            text-align: right;
            margin-right: 20px;
        }
        
        .logo img{
            width: 140px;
        }



        /* INVOICE INTRO DETAILS */
        .invoice-intro{
            width: 100%;
            font-size: 12px;
        }
        .invoice-intro tr{
            vertical-align: top;
        }
        .invoice-intro h1{
            color: #b85c04;
            font-size: 26px;
            text-transform: uppercase;
        }
        .invoice-intro-left p{
            font-weight: bold;
            margin: 4px 0px;
        }
        .invoice-intro-right{
            padding-right: 15px;
        }
        .invoice-intro-right p{
            margin: 4px 0px;
        }
        .invoice-intro-right .keys{
            padding-right: 20px;
        }
        .gpca-trn{
            font-weight: bold;
        }





        /* INVOICE BODY DETAILS */
        .invoice-body{
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        .invoice-body .tr-head{
            vertical-align: bottom;
            text-align: center;
            font-weight: bold;
            background-color: #cfcfcf;
        }
        .invoice-body .tr-body tr{
            vertical-align: top;
        }
        .invoice-body td{
            padding: 8px;
        }
        .invoice-body .tr-head td{
            border: 1px solid black;
        }
        .invoice-body .tr-body-main:nth-child(2) td, 
        .invoice-body .tr-body-main:nth-child(3) td, 
        .invoice-body .tr-body-main:nth-child(4) td, 
        .invoice-body .tr-body-main:nth-child(5) td,
        .invoice-body .tr-body-main:nth-child(6) td,
        .invoice-body .tr-body-main:nth-child(7) td,
        .invoice-body .tr-body-main:nth-child(8) td{
            border-left: 1px solid black;
            border-right: 1px solid black;
        }
        .invoice-body .first-col{
            width: 60%;
        }
        .invoice-body .second-col, .invoice-body .third-col, .invoice-body .fourth-col, .invoice-body .fifth-col{
            /* width: 8%; */
            text-align: center;
        }
        .invoice-body .first-col ol{
            margin: 0px;
            padding-left: 15px;
        }


        


        /* INVOICE FOOTER DETAILS */
        .invoice-footer td{
            border: none;
        }
        .first-tr-footer td{
            border-top: 1px solid black;
        }
        .invoice-footer .second-col{
            text-align: right;
        }
        .invoice-footer .third-col{
            /* width: 8%; */
            border: 1px solid black;
        }
        .invoice-footer .first-col p{
            margin: 2px;
        }
        .invoice-footer .second-col p{
            margin: 2px;
        }
        .invoice-footer .third-col p{
            margin: 2px;
        }
        .invoice-footer p{
            font-weight: bold;
        }
        .invoice-footer .word-amount{
            padding: 8px 0px;
        }


        /* PAYMENT INSTRUCTION */
        .payment-instruction p{
            margin: 0;
            font-weight: bold;
            font-size: 10px;
        }



        /* TERMS AND CONDITION */
        .terms-and-condition{
            font-size: 9px;
            margin-top: 10px;
        }
        .terms-and-condition p{
            font-weight: bold;
            text-decoration: underline;
            margin: 2px 0px;
        }
        .terms-and-condition .note{
            text-decoration: none;
            margin: 10px 0px;
        }
        .terms-and-condition ol{
            margin: 0px;
            padding: 0px 20px;
        }
        .terms-and-condition .inside-li {
            text-decoration: none;
            font-weight: normal;
        }



        /* INVOICE MAIN FOOTER */
        .invoice-main-footer{
            width: 100%;
            font-size: 9px;
            color: #0804fc;
        }
        .invoice-main-footer tr{
            vertical-align: top;
        }
        .invoice-main-footer p{
            margin: 2px 0px;
        }
        .invoice-main-footer .right{
            text-align: right;
            font-family: DejaVu Sans, sans-serif;
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>