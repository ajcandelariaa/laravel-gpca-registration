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
        p{
            margin: 0;
            padding: 0;
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
        .invoice-body .first-col{
            width: 60%;
        }
        .invoice-body .second-col, .invoice-body .third-col, .invoice-body .fourth-col, .invoice-body .fifth-col{
            text-align: center;
        }


        .invoice-body .tr-header{
            vertical-align: bottom;
            text-align: center;
            font-weight: bold;
            background-color: #cfcfcf;
        }
        .invoice-body .tr-header td{
            padding: 8px;
            border: 1px solid black;
        }


        .invoice-body .tr-description td{
            border-left: 1px solid black;
            border-right: 1px solid black;
        }
        .invoice-body .tr-description{
            border-left: 1px solid black;
            border-right: 1px solid black;
        }
        .invoice-body .tr-description .first-col{
            padding-top: 5px;
            padding-bottom: 10px;
        }


        .invoice-body .tr-delegates td{
            border-left: 1px solid black;
            border-right: 1px solid black;
            vertical-align: top;
        }
        .invoice-body .tr-delegates .first-col ol{
            margin-top: 5px;
            padding-left: 15px;
        }


        .invoice-body .tr-note td{
            border-left: 1px solid black;
            border-right: 1px solid black;
        }
        .invoice-body .tr-note .first-col{
            padding-bottom: 5px;
        }


        .tr-description td,
        .tr-delegates td,
        .tr-note td{
            padding-left: 5px;
            padding-right: 5px;
        }



        .invoice-body .tr-totals .third-col{
            border: 1px solid black;
        }
        .invoice-body .totals-first-row{
            border-top:  1px solid black;
        }
        .invoice-body .tr-totals .second-col{
            text-align: right;
            padding: 5px 8px;
        }
        .invoice-body .tr-totals .exchange-rate{
            padding-top: 10px;
            padding-bottom: 10px;
        }
        .invoice-body .tr-totals .exchange-rate p{
            border: 1px solid black;
            padding: 3px 10px;
            display: inline-block;
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
            margin-top: 20px;
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
            line-height: 8px;
        }


        /* Others */
        .tr-totals-unpaid .third-col{
            border: none;
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>