<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Payment Invoice</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <style type="text/css">
        .box {
            display: table;
            width: 100%;
        }

        .col-3 {
            display: table-cell;
            width: 25%;
        }

        .col-5 {
            display: table-cell;
            width: 41.6666666667%;
        }

        .col-7 {
            display: table-cell;
            width: 58.3333333333%;
        }

        .col-9 {
            display: table-cell;
            width: 75%;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right !important;
        }

        .table tr th,
        .table tr td {
            padding: 5px;
        }

        /* INVOICE */
        .invoice {
            background: #fff;
            padding: 30px 35px;
            border-radius: 5px;
            width: 100%;
            height: 500px;
        }

        .invoice:first-child {
            border-bottom: 2px dashed red;
        }
        .invoice-tuition {
            background: #fff;
            padding: 30px 35px;
            border-radius: 5px;
            width: 100%;
            height: 1000px;
        }

        .logo {
            margin-top: 5px;
        }

        .invoice-text {
            border: 2px solid;
            padding: 8px 20px;
            border-radius: 4px;
            width: 200px;
            margin: 0 auto;
            font-size: 16px;
        }

        .site-name {
            text-align: right;
            color: #444;
            font-size: 23px;
            margin: 0px;
            padding: 0px;
        }

        .student-info {
            font-size: 14px;
        }

        .table {
            width: 100%;
            font-size: 14px;
        }

        .inword {
            width: 100%;
            text-transform: uppercase;
            font-size: 13px;
            margin-top: -10px;
        }

        .powered-by {
            margin-top: 40px;
            margin-bottom: 0px;
            font-size: 12px;
        }

        .invoice:last-child {
            margin-bottom: 0;
        }

        @page {
            margin: 0;
        }
    </style>

</head>

<body>
    <div class="invoice-main" style="width: 720px;">
        <?php for ($i = 0; $i < 2; $i++) { ?>
        <div class="{{$invoice->head->type == 'tuition'?'invoice-tuition':'invoice'}}">
            <div class="box" style="border-bottom: 2px solid #ccc; padding-bottom: 10px; margin-bottom: 10px;">
                <div class="col-3">
                    @if($logo)
                    <img src="{{ $logo }}" style="height: 70px; width: 70px" class="logo" />
                    @endif
                </div>
                <div class="col-9">
                    <h3 class="site-name">
                        {{ $invoice->institution->name }}
                    </h3>
                    <p class="text-end invoice-date">
                        <strong>
                            {{ date('M d, Y', strtotime($invoice->invoice_date)) }}
                            &nbsp;&nbsp;|&nbsp;&nbsp; #{{$invoice->invoice_number }}
                        </strong>
                    </p>
                </div>
            </div>
            <div class="box mt-3">
                <div class="col-9 student-info">
                    <b>Name :</b> {{ $invoice->student->name_en ?? '' }} <br />
                    <b>Roll Number :</b> {{ $invoice->student->profile->roll_number ?? '' }}
                    <br />
                    <b>Campus :</b>
                    {{ $invoice->campus->name ?? "" }}
                    ({{ $invoice->shift->name ?? "" }})
                    <br />
                    <b>Medium/Version. :</b>
                    {{ $invoice->medium->name ?? "" }}
                    ({{ $invoice->academic_class->name ?? "" }})
                    <br />
                    <b>Group. :</b>
                    {{ $invoice->group->name ?? "" }}
                    ({{ $invoice->section->name ?? "" }})
                    <br />
                </div>
                <div class="col-3 text-center">
                    @if($invoice->status=='success')
                    <img src="{{$paid_logo}}" style="height: 90px; width: 90px" class="paid-image" />
                    @endif
                </div>
            </div>
            <div style="width: 100%; margin:15px;">
                <h5 class="text-center invoice-text">
                    PAYMENT INVOICE
                </h5>
            </div>

            <table class="table table-bordered table-striped text-center">
                <thead>
                    <tr>
                        <th style="width: 90px">Sl No.</th>
                        @if($invoice->head->type == 'tuition')
                        <th>Month</th>
                        @endif
                        <th>Description</th>
                        <th style="width: 130px">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @if($invoice->head->type == 'school')
                        <tr>
                            <td>{{ "01" }}</td>
                            <td class="">{{ $invoice->head->name_en }}</td>
                            <td>{{ number_format($invoice->discount_amount + $invoice->amount,2) }}</td>
                        </tr>
                    @else
                        @foreach($invoice->details??[] as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ date('F', strtotime($item->invoice_date)) }}</td>
                            <td class="">{{ $item->head->name_en }}</td>
                            <td>{{ number_format($item->amount,2) }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="3" class="text-end">Sub-Total</td>
                            <td class="fw-bold">{{ number_format($invoice->details()->sum('amount'),2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="{{$invoice->head->type == 'tuition'?'3':'2'}}" class="text-end">Discount</td>
                        <td class="fw-bold">{{ number_format($invoice->discount_amount,2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="{{$invoice->head->type == 'tuition'?'3':'2'}}" class="text-end">Paid Amount</td>
                        <td class="fw-bold">{{ number_format($invoice->amount,2) }}</td>
                    </tr>
                </tbody>
            </table>
            <p class="inword">
                Inword :
                <?php echo App\Models\NumberToWord::convert_number_to_words($invoice->amount); ?> taka only
            </p>
            <p class="powered-by text-center">
                Powered By Dynamic Host BD
            </p>
        </div>
        <?php } ?>
    </div>
</body>

</html>