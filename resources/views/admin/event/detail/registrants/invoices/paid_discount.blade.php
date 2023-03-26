 @extends('admin.event.detail.registrants.invoices.master')

 @section('content')

    <div class="logo">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/assets/images/invoice_logo.png'))) }}" alt="gpca-logo">
    </div>

    <table class="invoice-intro">
        <tr>
            <td>
                <h1>Tax Invoice</h1>
            </td>
        </tr>
        <tr>
            <td class="invoice-intro-left">
                <p>Gulf Chemicals and Industrial Oils Company</p>
                <p>P.O.Box: 3942, Second Industrial City</p>
                <p>Dammam 31481</p>
                <p>Saudi Arabia</p>
            </td>
            <td class="invoice-intro-right">
                <table align="right">
                    <tr>
                        <td class="keys"> 
                            <p>Invoice Date</p>
                            <p>Date of Supply</p>
                            <p>Invoice No</p>
                            <p>Reference No</p>
                            <p>Payment Terms</p>
                            <p class="gpca-trn">GPCA TRN</p>
                        </td>
                        <td class="values">
                            <p>: 22/03/2023</p>
                            <p>: 22/03/2023</p>
                            <p>: VAT02/2023-237</p>
                            <p>: PC23/D02-005</p>
                            <p>: Immediate Payment</p>
                            <p class="gpca-trn">: 100316599800003</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="invoice-body">
        <tr class="tr-head">
            <td class="first-col">Description</td>
            <td class="second-col">Qty</td>
            <td class="third-col">Unit Price</td>
            <td class="fourth-col">Discount</td>
            <td class="fifth-col">Net <br> Amount <br> (USD)</td>
        </tr>
        @php
             $count = 1;
         @endphp

        @foreach ($invoiceDetails as $delegatInvoiceDetail)
            
            @if ($count == 1)
                <tr class="tr-body tr-body-main">
                    <td class="first-col">
                        <p><strong>{{ $eventName }} – {{ $finalEventStartDate . ' - ' . $finalEventEndDate }} at {{ $eventLocation }}</strong></p>
                    </td>
                    <td class="second-col">&nbsp;</td>
                    <td class="third-col">&nbsp;</td>
                    <td class="fourth-col">&nbsp;</td>
                    <td class="fifth-col">&nbsp;</td>
                </tr>
            @endif


            <tr class="tr-body tr-body-main">
                <td class="first-col">
                    <p>{{ $delegatInvoiceDetail['delegateDescription'] }}</p>
                    <ol>
                        @foreach ($delegatInvoiceDetail['delegateNames'] as $name)
                             <li>{{ $name }}</li>
                         @endforeach
                    </ol>
                </td>
                <td class="second-col">
                    <p>{{ $delegatInvoiceDetail['quantity'] }}</p>
                </td>
                <td class="third-col">
                    <p>$ {{ number_format($unit_price, 2, '.', ',') }}</p>
                </td>
                <td class="fourth-col">
                    <p>$ {{ number_format($delegatInvoiceDetail['totalDiscount'], 2, '.', ',') }}</p>
                </td>
                <td class="fifth-col">
                    <p>$ {{ number_format($delegatInvoiceDetail['totalNetAmount'], 2, '.', ',') }}</p>
                </td>
            </tr>

            
            @if (count($invoiceDetails) == $count)
                <tr class="tr-body tr-body-main">
                    <td class="first-col">
                            <p><em>(Note: Please quote the invoice number during payment and ensure that all bank charges and withholding taxes (if any) should be borne by the sender to avoid underpayments)</em></p>
                    </td>
                    <td class="second-col">&nbsp;</td>
                    <td class="third-col">&nbsp;</td>
                    <td class="fourth-col">&nbsp;</td>
                    <td class="fifth-col">&nbsp;</td>
                </tr>
            @endif
        
        @php
             $count++;
         @endphp

         @endforeach
        
        {{-- INVOICE FOOTER --}}
        <tr class="tr-body invoice-footer first-tr-footer">
            <td class="first-col">
                <p>Total Amount USD: {{ $total_amount_string }}</p>
            </td>
            <td colspan="3" class="second-col">
                <p>Total before VAT </p>
            </td>
            <td class="third-col">
                <p>$ {{ number_format($net_amount, 2, '.', ',') }}</p>
            </td>
        </tr>

        <tr class="tr-body invoice-footer">
            <td class="first-col">&nbsp;</td>
            <td colspan="3" class="second-col">
                <p>VAT {{ $eventVat }}%</p>
            </td>
            <td class="third-col">
                <p>$ {{ number_format($vat_price, 2, '.', ',') }}</p>
            </td>
        </tr>

        <tr class="tr-body invoice-footer">
            <td class="first-col">&nbsp;</td>
            <td colspan="3" class="second-col">
                <p>Total</p>
            </td>
            <td class="third-col">
                <p>$ {{ number_format($total_amount, 2, '.', ',') }}</p>
            </td>
        </tr>
        
        <tr class="tr-body invoice-footer">
            <td class="first-col">&nbsp;</td>
            <td colspan="3" class="second-col">
                <p>Paid</p>
            </td>
            <td class="third-col">
                <p>$ {{ number_format($total_amount, 2, '.', ',') }}</p>
            </td>
        </tr>
        
        <tr class="tr-body invoice-footer">
            <td class="first-col">&nbsp;</td>
            <td colspan="3" class="second-col">
                <p>Balance</p>
            </td>
            <td class="third-col">
                <p>$ {{ number_format(0, 2, '.', ',') }}</p>
            </td>
        </tr>
    </table>

    <div class="payment-instruction">
        <p style="text-decoration: underline;">Payment Instruction</p>
        <p>Please remit to:</p>
        <p>Mashreq Bank</p>
        <p>Riqa Branch, Deira, P.O. Box 5511, Dubai</p>
        <p>USD Acct No. 0104-48-47064-5</p>
        <p>IBAN No. AE290330000010448470645</p>
        <p>Swift Code BOMLAEAD</p>
        <p>In favor of: Gulf Petrochemicals & Chemicals Association</p>
    </div>

    <div class="terms-and-condition">
        <p>Terms and Conditions</p>
        <ol>
            <li> For any cancellation, please notify us within 15 days from the receipt of the invoice. Any cancellation made after 15 days shall not be accepted hence the invoice has to be settled</li>
            <li> If any delegate is unable to attend, we will accept a substitute delegate at no extra cost. Please notify us in writing an email to: registration@gpca.org.ae with the name, job title, email address and telephone number of both the registered and substitute delegate.</li>
            <li>Refund Policy
                <p class="inside-li">3.1 If delegate/s cancelled their registration 31 days before the event, they will get a refund of 75% on the amount paid for the registration fee.</p>
                <p class="inside-li">3.2 If delegate/s cancelled their registration less than 31 days before the event, NO refund will be given.</p>
                <p class="inside-li">3.3 The refund will be on the net amount excluding 5% VAT.</p>
            </li>
        </ol>
        <p class="note">Note: Please quote the invoice number during payment and ensure that all bank charges should be borne by the sender to avoid underpayments.</p>
    </div>

    <p style="text-align: center; font-size: 11px; margin-top: 30px"><em>This is an autogenerated invoice no signature required.</em></p>

    <table class="invoice-main-footer">
        <tr>
            <td class="left">
                <p><strong>Gulf Petrochemicals & Chemicals Association</strong></p>
                <p>P.O. Box 123055, Dubai, United Arab Emirates</p>
                <p>Tel:  +97144510666, Fax: +97144510777 </p>
                <p>Email: info@gpca.org.ae </p>
                <p>Web: www.gpca.org.ae</p>
            </td>
            <td class="right">
                <p><strong>الاتحـاد الخليجـي  للبتروكيماويـات والكيماويـات</strong></p>
                <p>ص . ب . ١٢٣٠٥٥،  دبي ،   ألامارات العربية المتحدة</p>
                <p>هاتف : ٩٧١٤٤٥١٠٦٦٦+ ، فاكس : ٩٧١٤٤٥١٠٧٧٧ +</p>
                <p> info@gpca.org.ae البريد الالكتروني :   </p>
                <p>www.gpca.org.ae :   موقع اﻹانترنت </p>
            </td>
        </tr>
    </table>
 @endsection
