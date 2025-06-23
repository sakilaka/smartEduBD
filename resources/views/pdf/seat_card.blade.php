<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Seat Card</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: sans-serif;
            font-size: 12px;
        }

        .page {
            position: relative;
            width: 100%;
            height: 100vh;
            page-break-after: always;
        }

        .page img.bg {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .student-block {
            position: absolute;
            width: 48%;
            height: 24%;
            z-index: 1;
        }

        .top-left {
            top: 0;
            left: 0;
        }

        .top-right {
            top: 0;
            right: 18px;
        }

        .row2-left {
            top: 24.5%;
            left: 0;
        }

        .row2-right {
            top: 24.5%;
            right: 18px;
        }

        .row3-left {
            top: 49%;
            left: 0;
        }

        .row3-right {
            top: 49%;
            right: 18px;
        }

        .row4-left {
            top: 73.7%;
            left: 0;
        }

        .row4-right {
            top: 73.7%;
            right: 18px;
        }

        /* Student information fields - adjust these positions as needed */
        .exam_name{
            position: absolute;
            left: 135px;
            top: 47px;
        }
        .student-name {
            position: absolute;
            left: 215px;
            top: 104px;
        }

        .fathers-name {
            position: absolute;
            left: 215px;
            top: 123px;
            font-size: 10px !important;
        }

        .mothers-name {
            position: absolute;
            left: 215px;
            top: 141px;
            font-size: 10px !important;
        }

        .academic-class {
            position: absolute;
            left: 215px;
            top: 158px;
        }

        .group {
            position: absolute;
            left: 320px;
            top: 158px;
        }

        .section {
            position: absolute;
            left: 320px;
            top: 175px;
        }

        .shift {
            position: absolute;
            left: 320px;
            top: 193px;
        }




        .college-roll {
            position: absolute;
            left: 215px;
            top: 177px;
        }

        .student-id {
            position: absolute;
            left: 215px;
            top: 194px;
        }

        /* .campus {
            position: absolute;
            left: 130px;
            top: 274px;
        } */

        /* Profile image - keep your working version */
        .profile {
            position: absolute;
            left: 26px;
            top: 109px;
            width: 90px;
            height: 90px;
            object-fit: cover;
        }
    </style>
</head>

<body>
    @php
        $positions = [
            'top-left',
            'top-right',
            'row2-left',
            'row2-right',
            'row3-left',
            'row3-right',
            'row4-left',
            'row4-right',
        ];
    @endphp


    @foreach ($students->chunk(8) as $chunkIndex => $group)
        <div class="page">
            <img class="bg"
                src="{{ config('app.do_asset_url') . '/' . $group->first()->institution->seat_card }}"
                alt="Background">

            @foreach ($group as $i => $student)
                <div class="student-block {{ $positions[$i % 8] }}">
                    <!-- Student information fields -->
                    <div class="exam_name">{{ $searchParams['exam_name'] }}</div>
                    <div class="student-name"> {{ $student->name_en ?? '' }}</div>
                    <div class="fathers-name"> {{ $student->profile->fathers_name_en ?? 'Azad' }}</div>
                    <div class="mothers-name"> {{ $student->profile->mothers_name_en ?? 'Hasina' }}</div>
                    <div class="academic-class"> {{ $student->academic_class->name ?? '' }}</div>
                    <div class="group"> {{ $student->group->name ?? '' }}</div>
                    <div class="section"> {{ $student->section->name ?? '' }}</div>
                    <div class="shift"> {{ $student->shift->name ?? '' }}</div>
                    <div class="college-roll"> {{ $student->profile->roll_number }}</div>
                    {{-- <div class="campus"> {{ $student->campus->name }}</div> --}}
                    <div class="student-id"> {{ $student->software_id }}</div>

                    <!-- Profile image - kept as your original working version -->
                    <div class="profile">
                        @if (!empty($student->profile))
                            <img class="" width="92" src="{{ $student->profile->profile ?? '' }}" />
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</body>

</html>
