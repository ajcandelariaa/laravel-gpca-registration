@extends('admin.events.transactions.invoices.master')

@section('content')
    <div class="logo">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/assets/images/invoice_logo.png'))) }}"
            alt="gpca-logo">
    </div>

    <table class="invoice-intro">
        <tr>
            <td>
                <h1>Tax Invoice</h1>
            </td>
        </tr>
        <tr>
            <td class="invoice-intro-left">
                <p>{{ $fullname }}</p>
                <p>{{ $address }}</p>
                <p>{{ $city }}</p>
                <p>{{ $country }}</p>
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
                            <p>: {{ $invoiceDate }}</p>
                            <p>: {{ $invoiceDate }}</p>
                            <p>: {{ $invoiceNumber }}</p>
                            <p>: {{ $bookRefNumber }}</p>
                            <p>: Immediate Payment</p>
                            <p class="gpca-trn">: 100316599800003</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="invoice-body">
        <tr class="tr-header">
            <td class="first-col">Description</td>
            <td class="second-col">Qty</td>
            <td class="third-col">Unit Price</td>
            <td class="fourth-col">Discount</td>
            <td class="fifth-col">Net <br> Amount <br> (USD)</td>
        </tr>

        <tr class="tr-description">
            <td class="first-col">
                <p><strong>{{ $eventName }} – {{ $eventFormattedData }}</strong></p>
            </td>
            <td class="second-col">&nbsp;</td>
            <td class="third-col">&nbsp;</td>
            <td class="fourth-col">&nbsp;</td>
            <td class="fifth-col">&nbsp;</td>
        </tr>

        <tr class="tr-delegates">
            <td class="first-col">
                <ol>
                    @if ($day_one)
                        <li>December 4, 2023 (Monday)</li>
                    @endif

                    @if ($day_two)
                        <li>December 5, 2023 (Tuesday)</li>
                    @endif

                    @if ($day_three)
                        <li>December 6, 2023 (Wednesday)</li>
                    @endif

                    @if ($day_four)
                        <li>December 7, 2023 (Thursday)</li>
                    @endif
                </ol>
            </td>
            <td class="second-col">
                @if ($day_one)
                    <div class="bg-white p-4">1</div>
                @endif

                @if ($day_two)
                    <div class="bg-white p-4">1</div>
                @endif

                @if ($day_three)
                    <div class="bg-white p-4">1</div>
                @endif

                @if ($day_four)
                    <div class="bg-white p-4">1</div>
                @endif
            </td>
            <td class="third-col">
                @if ($day_one)
                    <div class="bg-white p-4">$ 200.00</div>
                @endif

                @if ($day_two)
                    <div class="bg-white p-4">$ 220.00</div>
                @endif

                @if ($day_three)
                    <div class="bg-white p-4">$ 200.00</div>
                @endif

                @if ($day_four)
                    <div class="bg-white p-4">$ 200.00</div>
                @endif
            </td>
            <td class="fourth-col">
                @if ($day_one)
                    <div class="bg-white p-4">$ 0.00</div>
                @endif

                @if ($day_two)
                    <div class="bg-white p-4">$ 0.00</div>
                @endif

                @if ($day_three)
                    <div class="bg-white p-4">$ 0.00</div>
                @endif

                @if ($day_four)
                    <div class="bg-white p-4">$ 0.00</div>
                @endif
            </td>
            <td class="fifth-col">
                @if ($day_one)
                    <div class="bg-white p-4">$ 200.00</div>
                @endif

                @if ($day_two)
                    <div class="bg-white p-4">$ 220.00</div>
                @endif

                @if ($day_three)
                    <div class="bg-white p-4">$ 200.00</div>
                @endif

                @if ($day_four)
                    <div class="bg-white p-4">$ 200.00</div>
                @endif
            </td>
        </tr>

        <tr class="tr-note">
            <td class="first-col">
                <p><em>(Note: Please quote the invoice number during payment and ensure that all bank charges and
                        withholding taxes (if any) should be borne by the sender to avoid underpayments)</em></p>
            </td>
            <td class="second-col">&nbsp;</td>
            <td class="third-col">&nbsp;</td>
            <td class="fourth-col">&nbsp;</td>
            <td class="fifth-col">&nbsp;</td>
        </tr>

        {{-- INVOICE FOOTER --}}
        <tr class="tr-totals totals-first-row">
            <td class="first-col" rowspan="5">
                <table>
                    <tr>
                        <td class="total-amount-words">
                            <p><strong>Total Amount USD: {{ $total_amount_string }} Only</strong></p>
                        </td>
                    </tr>
                    <tr>
                        <td class="exchange-rate">
                            <p><strong>Exchange rate: 1 USD = 3.675 AED</strong></p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="payment-instruction">
                                <p style="text-decoration: underline;">Payment Instruction</p>
                                <p>Please remit to:</p>
                                <p>Mashreq Bank</p>
                                <p>Riqa Branch, Deira, P.O. Box 5511, Dubai</p>
                                <p>USD Acct No. {{ $bankDetails['accountNumber'] }}</p>
                                <p>IBAN No. {{ $bankDetails['ibanNumber'] }}</p>
                                <p>Swift Code BOMLAEAD</p>
                                <p>In favor of: Gulf Petrochemicals & Chemicals Association</p>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td colspan="3" class="second-col">
                <p>Total before VAT </p>
            </td>
            <td class="third-col">
                <p>$ {{ number_format($net_amount, 2, '.', ',') }}</p>
            </td>
        </tr>

        <tr class="tr-totals">
            <td colspan="3" class="second-col">
                <p>VAT {{ $eventVat }}%</p>
            </td>
            <td class="third-col">
                <p>$ {{ number_format($vat_price, 2, '.', ',') }}</p>
            </td>
        </tr>

        <tr class="tr-totals">
            <td colspan="3" class="second-col">
                <p>Total</p>
            </td>
            <td class="third-col">
                <p>$ {{ number_format($total_amount, 2, '.', ',') }}</p>
            </td>
        </tr>

        <tr class="tr-totals tr-totals-unpaid">
            <td colspan="3">&nbsp;</td>
            <td>&nbsp;</td>
        </tr>

        <tr class="tr-totals tr-totals-unpaid">
            <td colspan="3">&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    </table>

    <div class="terms-and-condition">
        <p>Terms and Conditions</p>
        <ol>
            <li> For any cancellation, please notify us within 15 days from the receipt of the invoice. Any cancellation
                made after 15 days shall not be accepted hence the invoice has to be settled</li>
            <li> If any delegate is unable to attend, we will accept a substitute delegate at no extra cost. Please notify
                us in writing an email to: registration@gpca.org.ae with the name, job title, email address and telephone
                number of both the registered and substitute delegate.</li>
            <li>Refund Policy
                <p class="inside-li">3.1 If delegate/s cancelled their registration 31 days before the event, they will get
                    a refund of 75% on the amount paid for the registration fee.</p>
                <p class="inside-li">3.2 If delegate/s cancelled their registration less than 31 days before the event, NO
                    refund will be given.</p>
                <p class="inside-li">3.3 The refund will be on the net amount excluding 5% VAT.</p>
            </li>
        </ol>
    </div>

    <p style="text-align: center; font-size: 11px; margin-top: 30px"><em>This is an autogenerated invoice no signature
            required.</em></p>

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
@endsection
