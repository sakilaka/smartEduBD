<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('menus')->count();
        if ($count == 0) {
            $menus = [
                // master setup
                [
                    'menu_name'     => "Master Setup",
                    'icon'          => "<i class='bx bx-cube'></i>",
                    'route_name'    => null,
                    'params'        => null,
                    'looks_type'    => 'megamenu',
                    'show_dasboard' => 0,
                    'children'      => [
                        [
                            'menu_name'     => "Systems",
                            'icon'          => "",
                            'route_name'    => "masterSetup.systems",
                            'params'        => null,
                            'looks_type'    => 'single',
                            'show_dasboard' => 0,
                            'children'      => [
                                [
                                    'menu_name'     => "Package",
                                    'icon'          => "<i class='bx bx-purchase-tag' ></i>",
                                    'route_name'    => "package.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Institution",
                                    'icon'          => "<i class='bx bx-purchase-tag' ></i>",
                                    'route_name'    => "institution.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Mobile App Version",
                                    'icon'          => "<i class='bx bx-purchase-tag' ></i>",
                                    'route_name'    => "mobileAppVersion.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                            ],
                        ],
                        [
                            'menu_name'     => "Acadmic",
                            'icon'          => "",
                            'route_name'    => "masterSetup.acadmic",
                            'params'        => null,
                            'looks_type'    => 'single',
                            'show_dasboard' => 0,
                            'children'      => [
                                [
                                    'menu_name'     => "Campus",
                                    'icon'          => "<i class='bx bx-purchase-tag' ></i>",
                                    'route_name'    => "campus.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Shift",
                                    'icon'          => "<i class='bx bx-purchase-tag' ></i>",
                                    'route_name'    => "shift.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Medium",
                                    'icon'          => "<i class='bx bx-purchase-tag' ></i>",
                                    'route_name'    => "medium.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Academic Session",
                                    'icon'          => "<i class='bx bx-purchase-tag' ></i>",
                                    'route_name'    => "academicSession.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Academic Class",
                                    'icon'          => "<i class='bx bx-purchase-tag' ></i>",
                                    'route_name'    => "academicClass.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Group",
                                    'icon'          => "<i class='bx bx-purchase-tag' ></i>",
                                    'route_name'    => "group.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Section",
                                    'icon'          => "<i class='bx bx-purchase-tag' ></i>",
                                    'route_name'    => "section.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Exam",
                                    'icon'          => "<i class='bx bx-purchase-tag' ></i>",
                                    'route_name'    => "exam.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Subject",
                                    'icon'          => "<i class='bx bx-purchase-tag' ></i>",
                                    'route_name'    => "subject.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ]
                            ],
                        ],
                        // [
                        //     'menu_name'     => "SMS",
                        //     'icon'          => "",
                        //     'route_name'    => "masterSetup.smsSystems",
                        //     'params'        => null,
                        //     'looks_type'    => 'single',
                        //     'show_dasboard' => 0,
                        //     'children'      => [
                        //         [
                        //             'menu_name'     => "Send SMS",
                        //             'icon'          => "<i class='bx bx-list-ul'></i>",
                        //             'route_name'    => "smsHistory.index",
                        //             'params'        => null,
                        //             'looks_type'    => 'single',
                        //             'show_dasboard' => 0,
                        //         ],
                        //         [
                        //             'menu_name'     => "SMS Template",
                        //             'icon'          => "<i class='bx bx-list-ul'></i>",
                        //             'route_name'    => "smsTemplate.index",
                        //             'params'        => null,
                        //             'looks_type'    => 'single',
                        //             'show_dasboard' => 0,
                        //         ],
                        //         [
                        //             'menu_name'     => "Recharge Balance",
                        //             'icon'          => "<i class='bx bx-list-ul'></i>",
                        //             'route_name'    => "smsTransaction.index",
                        //             'params'        => null,
                        //             'looks_type'    => 'single',
                        //             'show_dasboard' => 0,
                        //         ],
                        //     ],
                        // ],
                    ],
                ],


                // Student
                [
                    'menu_name'     => "Student",
                    'icon'          => "<i class='bx bxs-group'></i>",
                    'route_name'    => null,
                    'params'        => null,
                    'looks_type'    => 'megamenu',
                    'show_dasboard' => 0,
                    'children'      => [
                        [
                            'menu_name'     => "Student",
                            'icon'          => "",
                            'route_name'    => "student.schoolStudent",
                            'params'        => null,
                            'looks_type'    => 'single',
                            'show_dasboard' => 0,
                            'children'      => [
                                [
                                    'menu_name'     => "Student List",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "student.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Student Create",
                                    'icon'          => "<i class='bx bx-plus'></i>",
                                    'route_name'    => "student.create",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Student Promotion",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "studentPromotion.create",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Student Import",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "student.import",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Student ID Card",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "idcard.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                            ],
                        ],
                        [
                            'menu_name'     => "Attendance",
                            'icon'          => "",
                            'route_name'    => "student.attendance",
                            'params'        => null,
                            'looks_type'    => 'single',
                            'show_dasboard' => 0,
                            'children'      => [

                                [
                                    'menu_name'     => "Attendance",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "attendance.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Attendance Report",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "attendance.attendanceReport",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Attendance Sheet",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "attendance.attendanceSheet",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                // [
                                //     'menu_name'     => "Admit Card",
                                //     'icon'          => "<i class='bx bx-list-ul'></i>",
                                //     'route_name'    => "admitCard.index",
                                //     'params'        => null,
                                //     'looks_type'    => 'single',
                                //     'show_dasboard' => 0,
                                // ],
                                // [
                                //     'menu_name'     => "Attendance Summary",
                                //     'icon'          => "<i class='bx bx-list-ul'></i>",
                                //     'route_name'    => "attendanceSummary.index",
                                //     'params'        => null,
                                //     'looks_type'    => 'single',
                                //     'show_dasboard' => 0,
                                // ],
                            ],
                        ],
                        [
                            'menu_name'     => "Card Management",
                            'icon'          => "",
                            'route_name'    => "student.card",
                            'params'        => null,
                            'looks_type'    => 'single',
                            'show_dasboard' => 0,
                            'children'      => [

                                [
                                    'menu_name'     => "Admin Admit Card",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "adminAdmit.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Student Admit Card",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "studentAdmit.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Sear Card",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "seat.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],

                            ],
                        ],
                    ],
                ],


                // Reports
                [
                    'menu_name'     => "Payment Reports",
                    'icon'          => "<i class='bx bx-money'></i>",
                    'route_name'    => null,
                    'params'        => null,
                    'looks_type'    => 'megamenu',
                    'show_dasboard' => 0,
                    'children'      => [
                        [
                            'menu_name'     => "Student Payments",
                            'icon'          => "<i class='bx bx-money'></i>",
                            'route_name'    => "reports.studentPayments",
                            'params'        => null,
                            'looks_type'    => 'single',
                            'show_dasboard' => 0,
                            'children'      => [
                                [
                                    'menu_name'     => "Payment History",
                                    'icon'          => "<i class='bx bx-money'></i>",
                                    'route_name'    => "invoice.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Student Discount",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "student.discount",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Student Payment",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "studentPayment.student",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Tuition Fees Dues Report",
                                    'icon'          => "<i class='bx bx-money'></i>",
                                    'route_name'    => "invoice.tuitionFeesDues",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Acc. Head Wise Payments",
                                    'icon'          => "<i class='bx bx-money'></i>",
                                    'route_name'    => "invoice.accountHeadWise",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                // [
                                //     'menu_name'     => "Account Wise Payments",
                                //     'icon'          => "<i class='bx bx-money'></i>",
                                //     'route_name'    => "invoice.accountWise",
                                //     'params'        => null,
                                //     'looks_type'    => 'single',
                                //     'show_dasboard' => 0,
                                // ],
                                // [
                                //     'menu_name'     => "Settlement Summary",
                                //     'icon'          => "<i class='bx bx-money'></i>",
                                //     'route_name'    => "invoice.accountSummary",
                                //     'params'        => null,
                                //     'looks_type'    => 'single',
                                //     'show_dasboard' => 0,
                                // ],
                                // [
                                //     'menu_name'     => "Refund Amount Report",
                                //     'icon'          => "<i class='bx bx-money'></i>",
                                //     'route_name'    => "invoice.refundAmount",
                                //     'params'        => null,
                                //     'looks_type'    => 'single',
                                //     'show_dasboard' => 0,
                                // ],
                                // [
                                //     'menu_name'     => "Invoice Create",
                                //     'icon'          => "<i class='bx bx-money'></i>",
                                //     'route_name'    => "invoice.create",
                                //     'params'        => null,
                                //     'looks_type'    => 'single',
                                //     'show_dasboard' => 0,
                                // ],
                            ],
                        ],
                    ],
                ],

                // Reports
                [
                    'menu_name'     => "Result",
                    'icon'          => "<i class='bx bx-news'></i>",
                    'route_name'    => null,
                    'params'        => null,
                    'looks_type'    => 'megamenu',
                    'show_dasboard' => 0,
                    'children'      => [
                        [
                            'menu_name'     => "Primary Result",
                            'icon'          => "<i class='bx bx-money'></i>",
                            'route_name'    => "result.primaryResult",
                            'params'        => null,
                            'looks_type'    => 'single',
                            'show_dasboard' => 0,
                            'children'      => [
                                [
                                    'menu_name'     => "Grade Management",
                                    'icon'          => "<i class='bx bx-money'></i>",
                                    'route_name'    => "primaryGradeManagement.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Subject Assign",
                                    'icon'          => "<i class='bx bx-money'></i>",
                                    'route_name'    => "primarySubjectAssign.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Class Test",
                                    'icon'          => "<i class='bx bx-money'></i>",
                                    'route_name'    => "primaryClassTestResult.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Mark Entry",
                                    'icon'          => "<i class='bx bx-money'></i>",
                                    'route_name'    => "primaryResult.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Result",
                                    'icon'          => "<i class='bx bx-money'></i>",
                                    'route_name'    => "primaryResult.result",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Class Wise Result",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "primaryResult.classwiseResult",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Subject Wise Result",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "primaryResult.subjectwiseResult",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Tabulation Sheet",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "primaryResult.tabulationSheet",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Grade Summary",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "primaryResult.gradeSummary",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Number Sheet",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "primaryResult.numberSheet",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                            ],
                        ],
                        [
                            'menu_name'     => "Secondary Result",
                            'icon'          => "<i class='bx bx-money'></i>",
                            'route_name'    => "result.secondaryResult",
                            'params'        => null,
                            'looks_type'    => 'single',
                            'show_dasboard' => 0,
                            'children'      => [
                                [
                                    'menu_name'     => "Grade Management",
                                    'icon'          => "<i class='bx bx-money'></i>",
                                    'route_name'    => "secondaryGradeManagement.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Subject Assign",
                                    'icon'          => "<i class='bx bx-money'></i>",
                                    'route_name'    => "secondarySubjectAssign.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Class Test",
                                    'icon'          => "<i class='bx bx-money'></i>",
                                    'route_name'    => "secondaryClassTestResult.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Mark Entry",
                                    'icon'          => "<i class='bx bx-money'></i>",
                                    'route_name'    => "secondaryResult.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Result",
                                    'icon'          => "<i class='bx bx-money'></i>",
                                    'route_name'    => "secondaryResult.result",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Class Wise Result",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "secondaryResult.classwiseResult",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Subject Wise Result",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "secondaryResult.subjectwiseResult",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Tabulation Sheet",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "secondaryResult.tabulationSheet",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Grade Summary",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "secondaryResult.gradeSummary",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Number Sheet",
                                    'icon'          => "<i class='bx bx-list-ul'></i>",
                                    'route_name'    => "secondaryResult.numberSheet",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                            ],
                        ],
                    ],
                ],

                // Fee Setup
                [
                    'menu_name'     => "Fee Setup",
                    'icon'          => "<i class='bx bxs-food-menu'></i>",
                    'route_name'    => null,
                    'params'        => null,
                    'looks_type'    => 'single',
                    'show_dasboard' => 0,
                    'children'      => [
                        [
                            'menu_name'     => "Payment Gateway",
                            'icon'          => "<i class='bx bx-list-ul'></i>",
                            'route_name'    => "paymentGateway.index",
                            'params'        => null,
                            'looks_type'    => 'single',
                            'show_dasboard' => 0,
                        ],
                        [
                            'menu_name'     => "Account Head",
                            'icon'          => "<i class='bx bx-list-ul'></i>",
                            'route_name'    => "accountHead.index",
                            'params'        => null,
                            'looks_type'    => 'single',
                            'show_dasboard' => 0,
                        ],
                        [
                            'menu_name'     => "Fee Setup",
                            'icon'          => "<i class='bx bx-list-ul'></i>",
                            'route_name'    => "feeSetup.index",
                            'params'        => null,
                            'looks_type'    => 'single',
                            'show_dasboard' => 0,
                        ],
                        [
                            'menu_name'     => "Tuition Fee Generate",
                            'icon'          => "<i class='bx bx-list-ul'></i>",
                            'route_name'    => "tuitionFeeGenerate.index",
                            'params'        => null,
                            'looks_type'    => 'single',
                            'show_dasboard' => 0,
                        ],
                    ],
                ],

                // Website
                [
                    'menu_name'     => "Website",
                    'icon'          => "<i class='bx bxl-windows'></i>",
                    'route_name'    => null,
                    'params'        => null,
                    'looks_type'    => 'megamenu',
                    'show_dasboard' => 0,
                    'children'      => [
                        [
                            'menu_name'     => "Content",
                            'icon'          => "",
                            'route_name'    => "content.content",
                            'params'        => null,
                            'looks_type'    => 'single',
                            'show_dasboard' => 0,
                            'children'      => [
                                [
                                    'menu_name'     => "About Us",
                                    'icon'          => "<i class='bx bx-news'></i>",
                                    'route_name'    => "content.create",
                                    'params'        => "aboutus",
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "User Manual",
                                    'icon'          => "<i class='bx bx-news'></i>",
                                    'route_name'    => "content.create",
                                    'params'        => "usermanual",
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Privacy Policy",
                                    'icon'          => "<i class='bx bx-news'></i>",
                                    'route_name'    => "content.create",
                                    'params'        => "privacy-policy",
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Support Policy",
                                    'icon'          => "<i class='bx bx-news'></i>",
                                    'route_name'    => "content.create",
                                    'params'        => "support-policy",
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Refund & Return Policy",
                                    'icon'          => "<i class='bx bx-news'></i>",
                                    'route_name'    => "content.create",
                                    'params'        => "refund-Return-policy",
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Terms & Conditions",
                                    'icon'          => "<i class='bx bx-news'></i>",
                                    'route_name'    => "content.create",
                                    'params'        => "terms-conditions",
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                            ],
                        ],
                        [
                            'menu_name'     => "Gallery / Images",
                            'icon'          => "",
                            'route_name'    => "gallery.galleryImages",
                            'params'        => null,
                            'looks_type'    => 'single',
                            'show_dasboard' => 0,
                            'children'      => [
                                [
                                    'menu_name'     => "Sliders",
                                    'icon'          => "<i class='bx bx-image'></i>",
                                    'route_name'    => "slider.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 1,
                                ],
                                [
                                    'menu_name'     => "Album",
                                    'icon'          => "<i class='bx bx-purchase-tag'></i>",
                                    'route_name'    => "album.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 1,
                                ],
                                [
                                    'menu_name'     => "Images",
                                    'icon'          => "<i class='bx bx-image-add'></i>",
                                    'route_name'    => "photo.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Videos",
                                    'icon'          => "<i class='bx bx-videos'></i>",
                                    'route_name'    => "video.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                            ],
                        ],
                        [
                            'menu_name'     => "Others",
                            'icon'          => "",
                            'route_name'    => "website.websiteOthers",
                            'params'        => null,
                            'looks_type'    => 'single',
                            'show_dasboard' => 0,
                            'children'      => [
                                [
                                    'menu_name'     => "News",
                                    'icon'          => "<i class='bx bx-news' ></i>",
                                    'route_name'    => "news.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                                [
                                    'menu_name'     => "Notice",
                                    'icon'          => "<i class='bx bx-news' ></i>",
                                    'route_name'    => "notice.index",
                                    'params'        => null,
                                    'looks_type'    => 'single',
                                    'show_dasboard' => 0,
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            $this->insertMenuItems($menus, null);
        }
    }

    private function insertMenuItems($menuItems, $parentId)
    {
        if (is_array($menuItems) && count($menuItems) > 0) {
            foreach ($menuItems as $key => $item) {
                $children = $item['children'] ?? [];
                unset($item['children']);

                $item['sorting']   = $key;
                $item['parent_id'] = $parentId;
                $id                = DB::table('menus')->insertGetId($item);

                if (is_array($children) && count($children) > 0) {
                    $this->insertMenuItems($children, $id);
                }
            }
        }
    }
}
