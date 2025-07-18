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
        
        /* INVOICE MAIN FOOTER */
        .invoice-main-footer {
            margin-top: 20px;
            width: 100%;
            font-size: 9px;
            color: #0804fc;
        }

        .invoice-main-footer tr {
            vertical-align: top;
        }

        .invoice-main-footer p {
            margin: 2px 0px;
        }

        .invoice-main-footer .right {
            text-align: right;
            font-family: DejaVu Sans, sans-serif;
            line-height: 8px;
        }
    </style>
</head>

<body>
    <table class="invoice-main-footer">
        <tr>
            <td class="left">
                <img src="data:image/PNG;base64,{{ base64_encode(file_get_contents(public_path('/assets/images/invoice_footer_left.PNG'))) }}"
                    alt="invoice-footer" width="200">
            </td>
            <td class="right">
                <img src="data:image/PNG;base64,{{ base64_encode(file_get_contents(public_path('/assets/images/invoice_footer_right.PNG'))) }}"
                    alt="invoice-footer" width="150">
            </td>
        </tr>
    </table>
</body>

</html>
