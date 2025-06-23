<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Admit Card</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style type="text/css">
        body {
            position: relative;
            width: 100%;
            height: 100%;
            font-size: 18px;
        }

        .admit-card-body img {
            height: 100%;
            width: 100%;
        }

        .admit_card_name {
            position: absolute;
            top: 150px;
            left: 10px;
            font-size: 22px;
            font-weight: bold;
            width: 97.5%;
            text-align: center;
        }

        .admit-card-body .session {
            position: absolute;
            right: 67px;
            top: 207px;
        }

        .admit-card-body .reg_no {
            position: absolute;
            left: 73.5%;
            top: 243px;
        }

        .admit-card-body .student_info {
            position: absolute;
            left: 200px;
            top: 277px;
            width: 71.4%;
        }

        .admit-card-body .student_info td {
            height: 35.5px;
        }

        .admit-card-body .student_info .img-td {
            width: 150px;
        }

        .admit-card-body .student_info .student_id {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
        }

        .admit-card-body .student_info .profile {
            height: 148px;
            width: 147px;
            float: right
        }

        .admit-card-body .issue_date {
            position: absolute;
            left: 140px;
            bottom: 12px;
            font-size: 16px;
        }

        @page {
            margin: 0;
        }
    </style>

</head>

<body>
    <div class="admit-card-body">
        <img src="{{$config['admit_card_image']??''}}" alt="">
        <div class="admit_card_name">
            {{ $admit->name??'' }}
        </div>
        @if(!empty($student))
        <p class="session">{{ $student->academic_session->name??'' }}</p>
        <p class="reg_no">{{ $student->reg_no??'' }}</p>

        <table class="student_info">
            <tr>
                <td>{{ $student->name??'' }}</td>
                <td class="img-td" rowspan="4">
                    @if(!empty($student->profile))
                    <img src="{{ $student->profile }}" class="profile">
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="2">{{ $student->qualification->name??'' }}</td>
            </tr>
            <tr>
                <td colspan="2">{{ $student->department->name??'' }}</td>
            </tr>
            <tr>
                <td colspan="2">{{ $student->academic_class->name??'' }}</td>
            </tr>
            <tr>
                <td>{{ $student->college_roll }}</td>
                <td class="student_id">{{ $student->student_id }}</td>
            </tr>
            <tr>
                <td colspan="2">{{ $student->fathers_name }}</td>
            </tr>
            <tr>
                <td colspan="2">{{ $student->mothers_name }}</td>
            </tr>
        </table>
        @endif

        @if(!empty($admit->issue_date))
        <p class="issue_date"> {{ date('d-m-Y',strtotime($admit->issue_date)) }}</p>
        @endif
    </div>
</body>

</html>