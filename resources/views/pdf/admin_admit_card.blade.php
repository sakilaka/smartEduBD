<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Admit Card</title>
    <style>
        @page {
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
            height: 48%;
            z-index: 1;
        }

        .top-left {
            top: 0;
            left: 0;
        }

        .top-right {
            top: 0;
            right: 22px;
        }

        .bottom-left {
            bottom: 18px;
            left: 0;
        }

        .bottom-right {
            bottom: 18px;
            right: 22px;
        }

        /* Student information fields - adjust these positions as needed */
        .exam_name{
            position: absolute;
            left: 190px;
            top: 92px;
            font-size: 18px;
        }
        .student-name {
            position: absolute;
            left: 130px;
            top: 162px;
        }

        .fathers-name {
            position: absolute;
            left: 130px;
            top: 180px;
        }

        .mothers-name {
            position: absolute;
            left: 130px;
            top: 200px;
        }

        .academic-class {
            position: absolute;
            left: 130px;
            top: 218px;
        }

        .shift {
            position: absolute;
            left: 350px;
            top: 218px;
        }

        .group {
            position: absolute;
            left: 130px;
            top: 237px;
        }

        .section {
            position: absolute;
            left: 350px;
            top: 237px;
        }

        .college-roll {
            position: absolute;
            left: 130px;
            top: 256px;
        }

        .student-id {
            position: absolute;
            left: 350px;
            top: 256px;
        }

        .campus {
            position: absolute;
            left: 130px;
            top: 274px;
        }

        /* Profile image - keep your working version */
        .profile {
            position: absolute;
            left: 450px;
            top: 123px;
            width: 80px;
            height: 80px;
            object-fit: cover;
        }
    </style>
</head>

<body>
    @php
        $positions = ['top-left', 'top-right', 'bottom-left', 'bottom-right'];
    @endphp

    @foreach ($students->chunk(4) as $chunkIndex => $group)
        <div class="page">
            <img class="bg" src="{{ config('app.do_asset_url') . '/' . $group->first()->institution->admin_admit_card }}" alt="Background">

            @foreach ($group as $i => $student)
                <div class="student-block {{ $positions[$i % 4] }}">
                    <!-- Student information fields -->
                    <div class="exam_name">{{ $searchParams['exam_name'] }}</div>
                    <div class="student-name"> {{ $student->name_en ?? '' }}</div>
                    <div class="fathers-name"> {{ $student->profile->fathers_name_en ?? 'Azad' }}</div>
                    <div class="mothers-name"> {{ $student->profile->mothers_name_en ?? 'Hasina' }}</div>
                    <div class="academic-class"> {{ $student->academic_class->name ?? '' }}</div>
                    <div class="shift"> {{ $student->shift->name ?? '' }}</div>
                    <div class="group"> {{ $student->group->name ?? '' }}</div>
                    <div class="section"> {{ $student->section->name ?? '' }}</div>
                    <div class="college-roll"> {{ $student->profile->roll_number }}</div>
                    <div class="campus"> {{ $student->campus->name }}</div>
                    <div class="student-id"> {{ $student->software_id }}</div>

                    <!-- Profile image - kept as your original working version -->
                    <div class="profile">
                        @if (!empty($student->profile))
                            <img class="" width="80" src="{{ $student->profile->profile }}" />
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</body>

</html>
