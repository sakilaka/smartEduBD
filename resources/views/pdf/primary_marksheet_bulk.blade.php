<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Marksheet</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style type="text/css">
        body {
            position: relative;
            width: 100%;
            height: 100%;
            font-size: 18px;
        }

        .marksheet-body img {
            height: 100%;
            width: 100%;
        }

        .marksheet-body {
            page-break-after: always;
        }

        .exam_name {
            position: absolute;
            top: 148px;
            left: 10px;
            font-size: 23px;
            font-weight: bold;
            width: 98%;
            text-align: center;
        }

        .profile {
            position: absolute;
            top: 78.5px;
            right: 241.2px;
        }

        .profile img {
            height: 93.5px;
            width: 94.5px;
        }

        .marksheet-body .student_info {
            position: absolute;
            left: 167px;
            top: 185px;
            width: 84%;
            font-size: 15px;
        }

        .marksheet-body .student_info td {
            height: 31px;
        }

        .marksheet-body .student_info td.left {
            width: 43%;
        }

        .marksheet-body .student_info td.center {
            width: 21.5%;
        }

        .marksheet-body .marks_info {
            position: absolute;
            left: 65px;
            top: 318px;
            width: 91.5%;
        }

        .marksheet-body .fourth_marks_info {
            position: absolute;
            left: 65px;
            bottom: 324px;
            width: 91.5%;
        }

        .marksheet-body .marks_info td,
        .marksheet-body .fourth_marks_info td {
            height: 23.2px;
            font-size: 14px;
        }

        .marksheet-body .merit_info {
            position: absolute;
            left: 230px;
            bottom: 265px;
            width: 77%;
        }

        .marksheet-body .merit_info td {
            font-size: 13px;
            text-align: center;
            font-weight: bold
        }

        .marksheet-body .published_date {
            position: absolute;
            left: 128px;
            bottom: 29.5px;
            font-size: 14px;
            font-weight: bold;
        }

        @page {
            margin: 0;
        }
    </style>

</head>

<body>

    @foreach ($result->details as $key => $detail)
        <div class="marksheet-body">
            <img src="{{ $detail->result->institution->primary_term_marksheet ?? '' }}" alt="">
            <div class="exam_name">
                {{ $detail->result->exam->name ?? '' }}
            </div>
            <div class="profile">
                @php
                    $path = "{$storage_url}/{$detail->student->profile}";
                    $headers = @get_headers($path);
                    $fileExists = $headers && strpos($headers[0], '200') !== false;
                @endphp
                <img src="{{ $fileExists ? $path : public_path('images/user.png') }}" alt="">
            </div>

            {{-- student info --}}
            @if (!empty($detail->student))
                <table class="student_info">
                    <tr>
                        <td class="left">{{ $detail->student->name_en ?? '' }}</td>
                        <td class="center">{{ $detail->result->academic_class->name ?? '' }}</td>
                        <td>{{ $detail->result->section->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>{{ $detail->student->fathers_name_en ?? '' }}</td>
                        <td>{{ $detail->student->roll_number ?? '' }}</td>
                        <td>{{ $detail->result->shift->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>{{ $detail->student->mothers_name_en ?? '' }}</td>
                        <td>{{ $detail->result->academic_session->name ?? '' }}</td>
                    </tr>
                </table>
            @endif

            {{-- subject marks --}}
            @php
                $totalCt = 0;
                $totalCq = 0;
                $totalMcq = 0;
            @endphp
            @if (!empty($detail->except_fourth_marks))
                <table class="marks_info">
                    <tbody style="text-align:center">
                        @foreach ($detail->except_fourth_marks as $key => $mark)
                            @php
                                $totalCt += $mark->ct_mark;
                                $totalCq += $mark->cq_mark;
                                $totalMcq += $mark->mcq_mark;
                            @endphp

                            @if ($mark->fourth_subject == 1)
                                @php continue; @endphp
                            @endif

                            <tr>
                                <td style="width:155px; text-align:left; padding-left: 10px;">
                                    {{ $mark->subject->name_en ?? '' }}
                                </td>
                                <td style="width:55px; text-align:center;">{{ number_format($mark->ct_mark, 0) }}</td>
                                <td style="width:55px; text-align:center;">{{ number_format($mark->cq_mark, 0) }}</td>
                                <td style="width:55px; text-align:center;">{{ number_format($mark->mcq_mark, 0) }}</td>
                                <td style="width:55px; text-align:center;">{{ number_format($mark->obtained_mark, 0) }}
                                </td>
                                <td style="width:55px; text-align:center;">
                                    {{ number_format($highest_marks[$mark->subject_id] ?? 0, 0) }}
                                </td>
                                <td style="width:55px; text-align:center;">{{ $mark->gpa }}</td>
                                <td style="width:55px; text-align:center;">{{ $mark->letter_grade }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            {{-- fourth subject --}}
            <table class="fourth_marks_info">
                <tbody style="text-align:center">
                    <tr style="font-weight: bold;">
                        <td style="width:155px;"></td>
                        <td style="width:55px; text-align:center;font-size: 16px;">
                            {{ $totalCt > 0 ? number_format($totalCt, 0) : '' }}
                        </td>
                        <td style="width:55px; text-align:center; font-size: 16px;">
                            {{ $totalCq > 0 ? number_format($totalCq, 0) : '' }}
                        </td>
                        <td style="width:55px; text-align:center; font-size: 16px;">
                            {{ $totalMcq > 0 ? number_format($totalMcq, 0) : '' }}
                        </td>
                        <td style="width:55px; text-align:center; font-size: 16px;">
                            {{ number_format($detail->total_mark_without_additional, 0) }}
                        </td>
                        <td style="width:55px;"></td>
                        <td style="width:55px; text-align:center; font-size: 16px;">
                            {{ $detail->gpa }}
                        </td>
                        <td style="width:55px; text-align:center; font-size: 16px;">
                            {{ $detail->letter_grade }}
                        </td>
                    </tr>

                    @if ($detail->fourth_marks)
                        <tr>
                            <td style="width:155px; text-align:left; padding-left: 10px;">
                                {{ $detail->fourth_marks->subject->name_en ?? '' }}
                            </td>
                            <td style="width:55px; text-align:center;">
                                {{ number_format($detail->fourth_marks->ct_mark, 0) }}
                            </td>
                            <td style="width:55px; text-align:center;">
                                {{ number_format($detail->fourth_marks->cq_mark, 0) }}</td>
                            <td style="width:55px; text-align:center;">
                                {{ number_format($detail->fourth_marks->mcq_mark, 0) }}</td>
                            <td style="width:55px; text-align:center;">
                                {{ number_format($detail->fourth_marks->obtained_mark, 0) }}
                            </td>
                            <td style="width:55px; text-align:center;">
                                {{ number_format($highest_marks[$detail->fourth_marks->subject_id] ?? 0, 0) }}
                            </td>
                            <td style="width:55px; text-align:center;">{{ $detail->fourth_marks->gpa }}</td>
                            <td style="width:55px; text-align:center;">
                                {{ $detail->fourth_marks->letter_grade }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <table class="merit_info">
                <tbody>
                    <tr>
                        <td style="width: 65px;">
                            @if ($detail->letter_grade != 'F')
                                {{ $detail->merit_position_in_section }}
                            @endif
                        </td>
                        <td></td>
                        <td style="width: 65px;">
                            @if ($detail->letter_grade != 'F')
                                {{ $detail->merit_position_in_shift }}
                            @endif
                        </td>
                        <td></td>
                        <td style="width: 65px;">
                            @if ($detail->letter_grade != 'F')
                                {{ $detail->merit_position_in_class }}
                            @endif
                        </td>
                        <td></td>
                        <td style="width: 65px;">
                            @if ($detail->gpa < 1)
                                {{ $detail->except_fourth_marks->where('letter_grade', 'F')->count() }}
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>

            @if (!empty($detail->result->published_date))
                <p class="published_date"> {{ date('d F, Y', strtotime($detail->result->published_date)) }}</p>
            @endif
        </div>
    @endforeach
</body>

</html>
