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
            font-family: "Urbanist", sans-serif;
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
    @endphp
    <div class="container">
        <div class="report">
            <img src="{{ $theme_logo }}" width="86" height="30" alt="logo">
            <p style="margin: 0px 0px 8px 0px;font-size: 16px;font-weight: bold">
                {{ App\Libraries\AppLibrary::textShortener($company['company_name'], 60) }}</p>
            <p>{{ App\Libraries\AppLibrary::textShortener($company['company_address'], 50) }}</p>
            <h5 style="color: #F23E14;margin: 0px 0px 8px 0px;font-size: 16px;font-weight: bold;">
                {{ trans('all.label.product_report', [], 'en') }}</h5>
            <table>
                <thead>
                    <tr>
                        <th>{{ trans('all.label.name', [], 'en') }}</th>
                        <th>{{ trans('all.label.category', [], 'en') }}</th>
                        <th>{{ trans('all.label.sold_quantity', [], 'en') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        @php
                            $total += abs($product?->productOrders->sum('quantity'));
                        @endphp
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product?->category->name }}</td>
                            <td>{{ abs($product?->productOrders->sum('quantity')) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total">
                        <td colspan="2">{{ trans('all.label.total', [], 'en') }}</td>
                        <td>{{ $total }}</td>
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
