<?php

use App\Http\Controllers\Backend\Student\StudentController;
use App\Http\Controllers\Backend\Teacher\TeacherController;
use Illuminate\Support\Facades\Route;

// /*-----ADMIN LOGIN-----*/
Route::get('loginme', 'Auth\AdminLoginController@login')->name('admin.loginme');
Route::post('system-admin', 'Auth\AdminLoginController@login');
Route::get('login-check', 'Auth\AdminLoginController@loginCheck')->name('admin.loginCheck');
Route::post('logout', 'Auth\AdminLoginController@logout')->name('admin.logout');

Route::group(['middleware' => 'auth:admin'], function () {

    Route::get('table-sorting', 'Base\SortingController@sorting');
    Route::get('get-last-sorting', 'Base\SortingController@lastSorting');

    Route::group(['namespace' => 'Backend'], function () {
        Route::get('get-exam', 'MasterSetup\ExamController@index');

        /*----- Result-----*/
        Route::group(['namespace' => 'Result'], function () {
            Route::group(['namespace' => 'Primary'], function () {
                Route::get('primary-classwise-subjects',   'PrimarySubjectAssignController@subjectLists');
                Route::get('primary-students-for-class-test-marks-entry', 'PrimaryClassTestResultController@studentsForMarksEntry');
                Route::get('primary-students-for-marks-entry', 'PrimaryResultController@studentsForMarksEntry');
                Route::get('primary-download-marksheet/{id}', 'PrimaryResultReportController@downloadMarksheet');
                Route::get('primary-download-bulk-marksheet',  'PrimaryResultReportController@downloadBulkMarksheet');
            });

            Route::group(['namespace' => 'Secondary'], function () {
                Route::get('secondary-classwise-subjects',   'SecondarySubjectAssignController@subjectLists');
                Route::get('secondary-students-for-class-test-marks-entry', 'SecondaryClassTestResultController@studentsForMarksEntry');
                Route::get('secondary-students-for-marks-entry', 'SecondaryResultController@studentsForMarksEntry');
                Route::get('secondary-download-marksheet/{id}', 'SecondaryResultReportController@downloadMarksheet');
                Route::get('secondary-download-bulk-marksheet',  'SecondaryResultReportController@downloadBulkMarksheet');
            });
        });
    });

    // test controller methods
    // Route::get('send-sms', 'TestController@sendSmsToTest');
    // Route::get('session-update', 'TestController@sessionUpdate');
    // Route::get('delete-institution', 'TestController@deleteInstitutions');
    // Route::get('delete-result', 'TestController@deleteResult');
    Route::get('sync-result-roll', 'TestController@syncResultRoll');
});

Route::group(['namespace' => 'Backend', 'middleware' => 'auth:admin'], function () {
    Route::view('ckfinder-custom', 'ckfinder');
    Route::get('search-result', 'SearchController@index');
    Route::get('get-designation', 'MasterSetup\DesignationController@index');
    Route::resource('designation',      'MasterSetup\DesignationController');



    /*-----SYSTEM PORTION-----*/
    Route::get('systems-update', 'System\RoleController@systemsRoleUpdate');
    Route::get('get-menus/{any?}', 'System\MenuController@menus');
    Route::get('initialize-systems', 'System\LibController@systems');
    Route::get('get-permissions', 'System\RoleController@getPermissions');

    /*-----ADMIN PORTION-----*/
    Route::post('check-old-password', 'AdminController@checkOldPassword');
    Route::post('change-password', 'AdminController@passwordChange');

    /* -----FEES SETUP PORTION------*/
    Route::get('get-paymentGateway', 'PaymentGatewayController@index');
    Route::get('exist-feeSetup',    'FeeSetupController@existSetup');
    Route::get('fees-lists',        'FeeSetupController@feesLists');
    Route::get('exist-tuitionFeeSetup', 'TuitionFeeGenerateController@existSetup');

    /*-----MODULE PORTION-----*/
    Route::match(['get', 'post'], 'module/create', 'System\ModuleController@create')->name('module.create');
    Route::view('module', 'layouts.backend_app')->name('module.index');
    Route::get('module/check-model', 'System\ModuleController@checkModel');

    /*-----DASHBOARD PATH-----*/
    Route::view('dashboard', 'layouts.backend_app')->name('admin.dashboard');
    Route::view('sitemap', 'layouts.backend_app');
    Route::get('get-notifications', 'NotificationController@notifications');
    Route::get('read-notifications', 'NotificationController@readNotifications');

    Route::get('get-dashboard-info', 'AdminController@dashboardInfo');
    Route::get('today-payments', 'InvoiceController@index');

    /* -----Frontend Parent Menu && Content------*/
    Route::get('parent-menus', 'Website\FrontMenuController@getParentMenu');
    Route::get('get-content', 'Website\ContentController@index');
    Route::get('get-album/{type}', 'Website\Gallery\AlbumController@album');


    /*-----PERMITTED USER CAN ACCESS THIS PAGE-----*/
    Route::group(['middleware' => ['auth.access']], function () {

        // PARENT MENU FOR PERMISSION (only for 2nd level parent menu)
        Route::get('msAcadmic', 'ParentMenuController@acadmic')->name('masterSetup.acadmic');
        Route::get('msSystems', 'ParentMenuController@systems')->name('masterSetup.systems');
        Route::get('msSmsSystems', 'ParentMenuController@smsSystems')->name('masterSetup.smsSystems');
        Route::get('cContent', 'ParentMenuController@content')->name('content.content');
        Route::get('gImage', 'ParentMenuController@galleryImages')->name('gallery.galleryImages');
        Route::get('wOthers', 'ParentMenuController@websiteOthers')->name('website.websiteOthers');
        Route::get('sSchoolStudent', 'ParentMenuController@schoolStudent')->name('student.schoolStudent');
        Route::get('sattendance', 'ParentMenuController@attendance')->name('student.attendance');
        Route::get('scard', 'ParentMenuController@cardManagement')->name('student.card');
        Route::get('preports', 'ParentMenuController@studentPayments')->name('reports.studentPayments');
        Route::get('priResult', 'ParentMenuController@primaryResult')->name('result.primaryResult');
        Route::get('secResult', 'ParentMenuController@secondaryResult')->name('result.secondaryResult');

        /*-----System Settings-----*/
        Route::resource('admin',    'AdminController');
        // Route::resource('teacher', 'Teacher\TeacherController');
        // Index - List all teachers


        Route::match(['get', 'post'], 'teacher-import', 'Teacher\TeacherController@import')->name('teacher.import');
        Route::resource('leaveApplication', 'Teacher\LeaveApplicationController');
        Route::resource('teacherAttendance', 'Teacher\TeacherAttendanceController');
        Route::resource('role',     'System\RoleController');
        Route::get('admin-profile', 'AdminController@profile')->name('admin.profile');
        Route::resource('siteSetting', 'System\SiteSettingController')->except('edit', 'update', 'destroy');
        Route::resource('menu',     'System\MenuController');
        Route::resource('mobileAppVersion', 'System\MobileAppVersionController')->except('destroy', 'edit');

        /*-----Master Setup-----*/
        Route::group(['namespace' => 'MasterSetup'], function () {
            Route::resource('accountHead',      'AccountHeadController');
            Route::resource('package',          'PackageController');
            Route::resource('institution',      'InstitutionController');
            Route::resource('campus',           'CampusController');
            Route::resource('shift',            'ShiftController');
            Route::resource('medium',           'MediumController');
            Route::resource('academicSession',  'AcademicSessionController');
            Route::resource('academicClass',    'AcademicClassController');
            Route::resource('group',            'GroupController');
            Route::resource('section',          'SectionController');
            Route::resource('academicClassMapping', 'AcademicClassMappingController')->except('create', 'store', 'destroy');
            Route::resource('exam',             'ExamController');
            Route::resource('subject',          'SubjectController');
        });

        /*-----SMS PORTION-----*/
        // Route::resource('smsTransaction',   'SMS\SmsTransactionController');
        // Route::resource('smsTemplate',      'SMS\SmsTemplateController');
        // Route::resource('smsHistory',       'SMS\SmsHistoryController');


        /*-----PAYMENTS PORTION-----*/
        Route::resource('paymentGateway', 'PaymentGatewayController');
        Route::resource('feeSetup', 'FeeSetupController');
        Route::resource('tuitionFeeGenerate', 'TuitionFeeGenerateController');

        Route::resource('invoice', 'InvoiceController');
        // Route::get('account-wise-payment', 'InvoiceController@accountWise')->name("invoice.accountWise");
        Route::get('account-head-wise-payment', 'InvoiceController@accountHeadWise')->name("invoice.accountHeadWise");
        Route::get('tuition-fees-dues', 'InvoiceController@tuitionFeesDues')->name("invoice.tuitionFeesDues");
        // Route::get('department-wise-payment', 'InvoiceController@departmentWise')->name("invoice.departmentWise");
        // Route::get('refund-amount', 'InvoiceController@refundAmount')->name("invoice.refundAmount");
        // Route::get('account-summary', 'InvoiceController@accountSummary')->name("invoice.accountSummary");
        Route::get('invoice-service-charge', 'InvoiceController@serviceCharge')->name("invoice.serviceCharge");


        /*-----STUDENT-----*/
        Route::group(['namespace' => 'Student'], function () {
            Route::resource('student', 'StudentController');
            Route::match(['get', 'post'], 'student-discount', 'StudentController@discount')->name('student.discount');
            Route::match(['get', 'post'], 'student-import', 'StudentController@import')->name('student.import');
            Route::get('studentPromotion/create', 'StudentPromotionController@create')->name("studentPromotion.create");
            Route::get('studentPromotion', 'StudentPromotionController@index')->name("studentPromotion.index");
            Route::post('studentPromotion', 'StudentPromotionController@store')->name("studentPromotion.store");

            Route::get('student-idcard', 'IDCardController@index')->name('idcard.index');

            Route::get('studentPayment', 'StudentPaymentController@student')->name('studentPayment.student');
            Route::post('studentPayment/payment', 'StudentPaymentController@payment')->name('studentPayment.payment');

            /*-----ATTENDANCE PORTION-----*/
            Route::resource('attendance',   'AttendanceController');
            Route::get('attendance-report', 'AttendanceController@attendanceReport')->name('attendance.attendanceReport');
            Route::get('attendance-sheet',  'AttendanceController@attendanceSheet')->name('attendance.attendanceSheet');

            Route::get('admin-admit-card',  'StudentController@adminAdmitCard')->name('adminAdmit.index');
        });

        /*-----GUARDIAN-----*/
        Route::resource('guardian',   'GuardianController');


        /*----- RESULT-----*/
        Route::group(['namespace' => 'Result'], function () {
            /*----- Primary-----*/
            Route::group(['namespace' => 'Primary'], function () {
                Route::resource('primaryGradeManagement',   'PrimaryGradeManagementController');
                Route::resource('primarySubjectAssign', 'PrimarySubjectAssignController');

                Route::resource('primaryClassTestResult', 'PrimaryClassTestResultController');
                Route::post('primaryClassTestResult-published', 'PrimaryClassTestResultController@published')->name('primaryClassTestResult.published');
                Route::get('primaryClassTestResult-sync/{primaryClassTestResult}', 'PrimaryClassTestResultController@syncResult')->name('primaryClassTestResult.syncResult');
                Route::get('primaryClassTestResult-marksheet/{id}', 'PrimaryClassTestResultController@marksheet')->name('primaryClassTestResult.marksheet');

                Route::resource('primaryResult',                'PrimaryResultController');
                Route::post('primaryResult-published',          'PrimaryResultController@published')->name('primaryResult.published');
                Route::get('primaryResult-sync/{primaryResult}/{convert}',  'PrimaryResultController@syncResult')->name('primaryResult.syncResult');
                Route::get('primaryResult-report',              'PrimaryResultReportController@result')->name('primaryResult.result');
                Route::get('primaryResult-classwise',         'PrimaryResultReportController@classwiseResult')->name('primaryResult.classwiseResult');
                Route::get('primaryResult-subjectwise',         'PrimaryResultReportController@subjectwiseResult')->name('primaryResult.subjectwiseResult');
                Route::get('primaryResult-marksheet/{id}',      'PrimaryResultReportController@marksheet')->name('primaryResult.marksheet');
                Route::get('primaryResult-tabulation-sheet',    'PrimaryResultReportController@tabulationSheet')->name('primaryResult.tabulationSheet');
                Route::get('primaryResult-grade-summary',       'PrimaryResultReportController@gradeSummary')->name('primaryResult.gradeSummary');
                Route::get('primaryResult-number-sheet',        'PrimaryResultReportController@numberSheet')->name('primaryResult.numberSheet');
            });

            /*----- Secondary-----*/
            Route::group(['namespace' => 'Secondary'], function () {
                Route::resource('secondaryGradeManagement',   'SecondaryGradeManagementController');
                Route::resource('secondarySubjectAssign', 'SecondarySubjectAssignController');

                Route::resource('secondaryClassTestResult', 'SecondaryClassTestResultController');
                Route::post('secondaryClassTestResult-published', 'SecondaryClassTestResultController@published')->name('secondaryClassTestResult.published');
                Route::get('secondaryClassTestResult-sync/{secondaryClassTestResult}', 'SecondaryClassTestResultController@syncResult')->name('secondaryClassTestResult.syncResult');
                Route::get('secondaryClassTestResult-marksheet/{id}', 'SecondaryClassTestResultController@marksheet')->name('secondaryClassTestResult.marksheet');

                Route::resource('secondaryResult',                'SecondaryResultController');
                Route::post('secondaryResult-published',          'SecondaryResultController@published')->name('secondaryResult.published');
                Route::get('secondaryResult-sync/{secondaryResult}/{convert}',  'SecondaryResultController@syncResult')->name('secondaryResult.syncResult');
                Route::get('secondaryResult-report',              'SecondaryResultReportController@result')->name('secondaryResult.result');
                Route::get('secondaryResult-classwise',         'SecondaryResultReportController@classwiseResult')->name('secondaryResult.classwiseResult');
                Route::get('secondaryResult-subjectwise',         'SecondaryResultReportController@subjectwiseResult')->name('secondaryResult.subjectwiseResult');
                Route::get('secondaryResult-marksheet/{id}',      'SecondaryResultReportController@marksheet')->name('secondaryResult.marksheet');
                Route::get('secondaryResult-tabulation-sheet',    'SecondaryResultReportController@tabulationSheet')->name('secondaryResult.tabulationSheet');
                Route::get('secondaryResult-grade-summary',       'SecondaryResultReportController@gradeSummary')->name('secondaryResult.gradeSummary');
                Route::get('secondaryResult-number-sheet',        'SecondaryResultReportController@numberSheet')->name('secondaryResult.numberSheet');
            });

            /*----- Higher Secondary-----*/
        });


        /*-----FRONT-END-----*/
        Route::group(['namespace' => 'Website'], function () {
            /*-----CONTENT PORTION-----*/
            Route::get('content/{content}', 'ContentController@show')->name('content.show');
            Route::get('content/{slug}/create', 'ContentController@create')->name('content.create');
            Route::get('content-file/{slug}', 'ContentController@file')->name('content.file');
            Route::post('content', 'ContentController@store')->name('content.store');
            Route::post('content-file/{content}', 'ContentController@storeFile')->name('content.storeFile');
            Route::delete('content/{contentFile}', 'ContentController@destroy')->name('content.destroy');


            Route::resource('notice',       'NoticeController');
            Route::resource('news',         'NewsController');
            Route::resource('album',        'Gallery\AlbumController');
            Route::resource('photo',        'Gallery\PhotoController');
            Route::resource('video',        'Gallery\VideoController');
            Route::resource('slider',       'Gallery\SliderController');
            Route::resource('frontMenu',    'FrontMenuController');
        });

        /*-----ACTIVITY LOG-----*/
        Route::get('activityLog', 'System\ActivityLogController@index')->name('activityLog.index');
        Route::get('activityLog/{id}', 'System\ActivityLogController@show')->name('activityLog.show');
        Route::get('allRead', 'System\ActivityLogController@allRead')->name('activityLog.allRead');
        Route::delete('activityLog/{id}', 'System\ActivityLogController@destroy')->name('activityLog.destroy');

        Route::resource('notification', 'NotificationController');
    });
});


Route::get('teacher', [TeacherController::class, 'index'])->name('teacher.index');
Route::get('teacher/create', [TeacherController::class, 'create'])->name('teacher.create');
Route::post('/teacher', [TeacherController::class, 'store'])->name('teacher.store');
Route::get('teacher/{teacher}', [TeacherController::class, 'show'])->name('teacher.show');
Route::get('teacher/{teacher}/edit', [TeacherController::class, 'edit'])->name('teacher.edit');
Route::put('teacher/{teacher}', [TeacherController::class, 'update'])->name('teacher.update');
Route::delete('teacher/{teacher}', [TeacherController::class, 'destroy'])->name('teacher.destroy');


Route::get('admit-card-bulk', [StudentController::class, 'downloadBulkAdminAdmit'])->name('admit.card');
Route::get('seat-card-bulk', [StudentController::class, 'downloadBulkSeatCard'])->name('seat.card');
// Route::get('admit-card-bulk',  'Backend\Student\Studentcontroller@downloadBulkAdminAdmit');
// Route::get('seat-card-bulk',  'Backend\Student\Studentcontroller@downloadBulkSeatCard');
