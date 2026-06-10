<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <title>Document</title>
    <style>
        body {
            font-family: "Urbanist", sans-serif !important;
            color: #1F1F39;
        }

        .container {
            width: 100%;
            height: 100vh;
            margin: auto;
            position: relative;
        }

        .report {
            width: 100%;
            text-align: center;
        }


        img {
            margin: 0px 0px 8px 0px;
            font-size: 16px;
            font-weight: 600;
        }

        p {
            margin: 0px 0px 16px 0px;
        }

        /* color: #F23E14; */
        th,
        td {
            border-collapse: collapse;
            border: 1px solid #EFF0F6;
            padding: 12px 11px;
            text-align: left;
            font-size: 12px;
            font-weight: 400;
        }

        table {
            border-radius: 8px;
            outline: 1px solid #EFF0F6;
            outline-offset: -1px;
            overflow: hidden;
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #F8FBFB;
        }

        th:first-child {
            text-wrap: nowrap;
        }

        tbody {
            color: #6E7191;
        }

        .date,
        .time {
            text-wrap: nowrap;
        }

        .total {
            color: #1F1F39;
        }

        .footer {
            position: absolute;
            width: 100%;
            text-align: center;
            font-size: 12px;
            font-weight: 400;
            bottom: 20px;
        }
    </style>
</head>

<body>
    @php
        $total = 0;
        $discount = 0;
        $shipping_charge = 0;

    @endphp
    <div class="container">
        <div class="report">
            <img src="{{ $theme_logo }}" width="86" height="30" alt="logo">
            <p style="margin: 0px 0px 8px 0px;font-size: 16px;font-weight: bold">
                {{ App\Libraries\AppLibrary::textShortener($company['company_name'], 60) }}</p>
            <p>{{ App\Libraries\AppLibrary::textShortener($company['company_address'], 50) }}</p>
            <p style="color: #F23E14;margin: 0px 0px 8px 0px;font-size: 16px;font-weight: bold;">
                {{ trans('all.label.sales_report', [], 'en') }}</p>
            <table>
                <thead>
                    <tr>
                        <th>{{ trans('all.label.order_id', [], 'en') }}</th>
                        <th>{{ trans('all.label.date', [], 'en') }}</th>
                        <th>{{ trans('all.label.payment_type', [], 'en') }}</th>
                        <th>{{ trans('all.label.payment_status', [], 'en') }}</th>
                        <th>{{ trans('all.label.discount', [], 'en') }}</th>
                        <th>{{ trans('all.label.shipping_charge', [], 'en') }}</th>
                        <th>{{ trans('all.label.total', [], 'en') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        @php
                            $total += $order->total;
                            $discount += $order->discount;
                            $shipping_charge += $order->shipping_charge;
                        @endphp
                        <tr>
                            <td>{{ $order->order_serial_no }}</td>
                            <td>{{ App\Libraries\AppLibrary::datetime($order->order_datetime) }}</td>
                            <td>{{ $order->order_type == App\Enums\OrderType::POS ? trans('posPaymentMethod.' . $order->pos_payment_method, [], 'en') : $order?->paymentMethod?->name }}
                            </td>
                            <td>{{ trans('payment_status.' . $order->payment_status, [], 'en') }}</td>
                            <td>{{ App\Libraries\AppLibrary::flatAmountFormat($order->discount) }}</td>
                            <td>{{ App\Libraries\AppLibrary::flatAmountFormat($order->shipping_charge) }}</td>
                            <td>{{ App\Libraries\AppLibrary::flatAmountFormat($order->total) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total">
                        <td colspan="4">{{ trans('all.label.total', [], 'en') }}</td>
                        <td>{{ App\Libraries\AppLibrary::reportCurrencyAmountFormat($discount) }}</td>
                        <td>{{ App\Libraries\AppLibrary::reportCurrencyAmountFormat($shipping_charge) }}</td>
                        <td>{{ App\Libraries\AppLibrary::reportCurrencyAmountFormat($total) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="footer">
            {{ $copyright }}
        </div>
    </div>
</body>

</html>
