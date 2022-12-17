<?php

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('dashboard');
    } else {
        return view('auth.login');
    }
});
Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');
Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', 'Admin\DashboardController@index');
    Route::post('dashboard/requestCourseSatatusSummary', 'Admin\DashboardController@requestCourseSatatusSummary');
    Route::post('dashboard/getDsMarkingSummary', 'Admin\DashboardController@getDsMarkingSummary');


    Route::post('setRecordPerPage', 'UserController@setRecordPerPage');

    Route::group(['middleware' => 'dsCi'], function () {
        Route::get('myProfile', 'UserController@myProfile');
    });

    //start::reference archive reports
    // Reference Archive
    Route::get('/documentSearch', 'DocumentSearchReportController@index');
    Route::post('/documentSearch/filter', 'DocumentSearchReportController@filter');
    Route::post('documentSearch/downloadFile', 'DocumentSearchReportController@downloadFile');

    Route::get('/dailyDocReport', 'DailyDocReportController@index');
    Route::post('/dailyDocReport/filter', 'DailyDocReportController@filter');
    Route::post('dailyDocReport/downloadFile', 'DailyDocReportController@downloadFile');

    Route::get('/monthlyDocReport', 'MonthlyDocReportController@index');
    Route::post('/monthlyDocReport/filter', 'MonthlyDocReportController@filter');
    Route::post('monthlyDocReport/downloadFile', 'MonthlyDocReportController@downloadFile');

    Route::get('/catWiseDocReport', 'CatWiseDocReportController@index');
    Route::post('/catWiseDocReport/filter', 'CatWiseDocReportController@filter');
    Route::post('catWiseDocReport/downloadFile', 'CatWiseDocReportController@downloadFile');

    Route::get('/originatorWiseDocReport', 'OriginatorWiseDocReportController@index');
    Route::post('/originatorWiseDocReport/filter', 'OriginatorWiseDocReportController@filter');
    Route::post('originatorWiseDocReport/downloadFile', 'OriginatorWiseDocReportController@downloadFile');

    Route::get('/classificationWiseDocReport', 'ClassificationWiseDocReportController@index');
    Route::post('/classificationWiseDocReport/filter', 'ClassificationWiseDocReportController@filter');
    Route::post('classificationWiseDocReport/downloadFile', 'ClassificationWiseDocReportController@downloadFile');

    Route::get('/courseWiseDocReport', 'CourseWiseDocReportController@index');
    Route::post('/courseWiseDocReport/filter', 'CourseWiseDocReportController@filter');
    Route::post('courseWiseDocReport/downloadFile', 'CourseWiseDocReportController@downloadFile');

//    summary
    Route::get('/catWiseDocSummary', 'CatWiseDocSummaryController@index');
    Route::post('/catWiseDocSummary/filter', 'CatWiseDocSummaryController@filter');

    Route::get('/courseWiseDocSummary', 'CourseWiseDocSummaryController@index');
    Route::post('/courseWiseDocSummary/filter', 'CourseWiseDocSummaryController@filter');


    //end::reference archive reports
    //Start :: GS Feedback Reports
    // Lesson Wise Gs Feedback From Ds
    Route::get('lessonWiseGsFeedbackFromDs', 'LessonWiseGsFeedbackFromDsController@index');
    Route::post('lessonWiseGsFeedbackFromDs/filter', 'LessonWiseGsFeedbackFromDsController@filter');
    Route::post('lessonWiseGsFeedbackFromDs/getCourse', 'LessonWiseGsFeedbackFromDsController@getCourse');
    Route::post('lessonWiseGsFeedbackFromDs/getGs', 'LessonWiseGsFeedbackFromDsController@getGs');
    Route::post('lessonWiseGsFeedbackFromDs/getLesson', 'LessonWiseGsFeedbackFromDsController@getLesson');

    // Lesson Wise Gs Feedback From CM
    Route::get('lessonWiseGsFeedbackFromCm', 'LessonWiseGsFeedbackFromCmController@index');
    Route::post('lessonWiseGsFeedbackFromCm/filter', 'LessonWiseGsFeedbackFromCmController@filter');
    Route::post('lessonWiseGsFeedbackFromCm/getCourse', 'LessonWiseGsFeedbackFromCmController@getCourse');
    Route::post('lessonWiseGsFeedbackFromCm/getGs', 'LessonWiseGsFeedbackFromCmController@getGs');
    Route::post('lessonWiseGsFeedbackFromCm/getLesson', 'LessonWiseGsFeedbackFromCmController@getLesson');
    //End :: GS Feedback Reports

    Route::post('noticeBoard/filter', 'NoticeBoardController@filter');
    Route::get('noticeBoard', 'NoticeBoardController@index')->name('noticeBoard.index');
    Route::group(['middleware' => 'superAdmin'], function() {
        Route::get('noticeBoard/create', 'NoticeBoardController@create')->name('noticeBoard.create');
        Route::post('noticeBoard', 'NoticeBoardController@store')->name('noticeBoard.store');
        Route::get('noticeBoard/{id}/edit', 'NoticeBoardController@edit')->name('noticeBoard.edit');
        Route::patch('noticeBoard/{id}', 'NoticeBoardController@update')->name('noticeBoard.update');
        Route::delete('noticeBoard/{id}', 'NoticeBoardController@destroy')->name('noticeBoard.destroy');
    });

    Route::group(['middleware' => 'dsCiSuperAdmin'], function () {
        Route::post('user/updateFamilyInfo', 'UserController@updateFamilyInfo')->name('user.familyInfoUpdate');
        Route::post('user/updateMaritalStatus', 'UserController@updateMaritalStatus')->name('user.maritialStatusUpdate');
        Route::post('user/updateBrotherSisterInfo', 'UserController@updateBrotherSisterInfo')->name('user.brotherSisterInfoUpdate');
        Route::post('user/rowAddForBrotherSister', 'UserController@rowAddForBrotherSister')->name('user.rowAddForBrotherSister');
        Route::post('user/getDistrict', 'UserController@getDistrict')->name('user.getDistrict');
        Route::post('user/getThana', 'UserController@getThana')->name('user.getThana');
        Route::post('user/updatePermanentAddress', 'UserController@updatePermanentAddress')->name('user.permanentAddressUpdate');
        Route::post('user/updatePhoto', 'UserController@updatePhoto')->name('user.updatePhoto');
        Route::post('user/rowAddForCivilEducation', 'UserController@rowAddForCivilEducation')->name('user.rowAddForCivilEducation');
        Route::post('user/updateCivilEducationInfo', 'UserController@updateCivilEducationInfo')->name('user.civilEducationInfoUpdate');
        Route::post('user/rowAddForServiceRecord', 'UserController@rowAddForServiceRecord')->name('user.rowAddForServiceRecord');
        Route::post('user/updateServiceRecordInfo', 'UserController@updateServiceRecordInfo')->name('user.serviceRecordInfoUpdate');
        Route::post('user/rowAddForAwardRecord', 'UserController@rowAddForAwardRecord')->name('user.rowAddForAwardRecord');
        Route::post('user/updateAwardRecordInfo', 'UserController@updateAwardRecordInfo')->name('user.awardRecordInfoUpdate');
        Route::post('user/rowAddForUnMsn', 'UserController@rowAddForUnMsn')->name('user.rowAddForUnMsn');
        Route::post('user/updateUnMsn', 'UserController@updateUnMsn')->name('user.punishmentRecordInfoUpdate');
        Route::post('user/rowAddForDefenceRelative', 'UserController@rowAddForDefenceRelative')->name('user.rowAddForDefenceRelative');
        Route::post('user/updateDefenceRelativeInfo', 'UserController@updateDefenceRelativeInfo')->name('user.defenceRelativeInfoUpdate');
        Route::post('user/updateNextKin', 'UserController@updateNextKin')->name('user.nextOfKinInfoUpdate');
        Route::post('user/updateMedicalDetails', 'UserController@updateMedicalDetails')->name('user.medicalDetailsUpdate');
        Route::post('user/updateWinterTraining', 'UserController@updateWinterTraining')->name('user.winterTrainingUpdate');
        Route::post('user/updateUserOthersInfo', 'UserController@updateUserOthersInfo')->name('user.userOthersUpdate');
        Route::post('user/updatePersonalInfo', 'UserController@updatePersonalInfo');

        Route::post('user/rowAddForCountry', 'UserController@rowAddForCountry')->name('user.rowAddForCountry');
        Route::post('user/rowAddForChild', 'UserController@rowAddForChild')->name('user.rowAddForChild');
        Route::post('user/rowAddForBank', 'UserController@rowAddForBank')->name('user.rowAddForBank');
        Route::post('user/updateCountryVisit', 'UserController@updateCountryVisit')->name('user.updateVisitCountry');
        Route::post('user/updateBank', 'UserController@updateBank')->name('user.updateBank');
    });
    Route::get('accountSetting', 'UserController@accountSetting');
    Route::get('changePassword', 'UserController@changePassword');
    Route::post('getProfileCenter', 'UserController@getProfileCenter');
    Route::post('updateProfile', 'UserController@updateProfile');
    Route::post('changePassword', 'UserController@updatePassword');

    // Manual
    Route::get('userManual/', 'ManualController@index');
    Route::get('userManual/download/{id}', 'ManualController@manualDownload');
    // Process Manual
    Route::get('processManual/', 'ProcessManualController@index');

    Route::group(['middleware' => 'superAdmin'], function () {

        //GS
        Route::post('gs/filter/', 'GsController@filter');
        Route::get('gs', 'GsController@index')->name('gs.index');
        Route::get('gs/create', 'GsController@create');
        Route::post('gs', 'GsController@store');
        Route::get('gs/{id}/edit', 'GsController@edit');
        Route::patch('gs/{id}', 'GsController@update')->name('gs.update');
        Route::delete('gs/{id}', 'GsController@destroy')->name('gs.destroy');
        Route::post('gs/showGsInfo', 'GsController@showGsInfo');


        //GS Module
        Route::post('gsmodule/filter/', 'GsModuleController@filter');
        Route::get('gsmodule', 'GsModuleController@index');
        Route::get('gsmodule/create', 'GsModuleController@create');
        Route::post('gsmodule', 'GsModuleController@store');
        Route::get('gsmodule/{id}/edit', 'GsModuleController@edit');
        Route::patch('gsmodule/{id}', 'GsModuleController@update')->name('gsmodule.update');
        Route::delete('gsmodule/{id}', 'GsModuleController@destroy')->name('gsmodule.destroy');



        //core Curriculum
        Route::post('coreCurriculum/filter/', 'CoreCurriculumController@filter');
        Route::get('coreCurriculum', 'CoreCurriculumController@index');
        Route::get('coreCurriculum/create', 'CoreCurriculumController@create');
        Route::post('coreCurriculum', 'CoreCurriculumController@store');
        Route::get('coreCurriculum/{id}/edit', 'CoreCurriculumController@edit');
        Route::patch('coreCurriculum/{id}', 'CoreCurriculumController@update')->name('coreCurriculum.update');
        Route::delete('coreCurriculum/{id}', 'CoreCurriculumController@destroy')->name('coreCurriculum.destroy');

        //gsgrading
        Route::post('gsgrading/filter/', 'GsGradingController@filter');
        Route::get('gsgrading', 'GsGradingController@index');
        Route::get('gsgrading/create', 'GsGradingController@create');
        Route::post('gsgrading', 'GsGradingController@store');
        Route::get('gsgrading/{id}/edit', 'GsGradingController@edit');
        Route::patch('gsgrading/{id}', 'GsGradingController@update')->name('gsgrading.update');
        Route::delete('gsgrading/{id}', 'GsGradingController@destroy')->name('gsgrading.destroy');

        //considerations
        Route::post('considerations/filter/', 'ConsiderationsController@filter');
        Route::get('considerations', 'ConsiderationsController@index');
        Route::get('considerations/create', 'ConsiderationsController@create');
        Route::post('considerations', 'ConsiderationsController@store');
        Route::get('considerations/{id}/edit', 'ConsiderationsController@edit');
        Route::patch('considerations/{id}', 'ConsiderationsController@update')->name('considerations.update');
        Route::delete('considerations/{id}', 'ConsiderationsController@destroy')->name('considerations.destroy');

        //comments
        Route::post('comment/filter/', 'CommentController@filter');
        Route::get('comment', 'CommentController@index');
        Route::get('comment/create', 'CommentController@create');
        Route::post('comment', 'CommentController@store');
        Route::get('comment/{id}/edit', 'CommentController@edit');
        Route::patch('comment/{id}', 'CommentController@update')->name('comment.update');
        Route::delete('comment/{id}', 'CommentController@destroy')->name('comment.destroy');




        // Media Content Type
        Route::post('mediaContentType/filter', 'MediaContentTypeController@filter');
        Route::get('mediaContentType', 'MediaContentTypeController@index')->name('mediaContentType.index');


        //Content Classification
        Route::post('contentClassification/filter', 'ContentClassificationController@filter');
        Route::get('contentClassification', 'ContentClassificationController@index')->name('contentClassification.index');

        //Module
        Route::post('module/filter/', 'ModuleController@filter');
        Route::get('module', 'ModuleController@index')->name('module.index');
        Route::get('module/create', 'ModuleController@create')->name('module.create');
        Route::post('module', 'ModuleController@store')->name('module.store');
        Route::get('module/{id}/edit', 'ModuleController@edit')->name('module.edit');
        Route::patch('module/{id}', 'ModuleController@update')->name('module.update');
        Route::delete('module/{id}', 'ModuleController@destroy')->name('module.destroy');


        //Subject
        Route::post('subject/filter/', 'SubjectController@filter');
        Route::get('subject', 'SubjectController@index')->name('subject.index');
        Route::get('subject/create', 'SubjectController@create')->name('subject.create');
        Route::post('subject', 'SubjectController@store')->name('subject.store');
        Route::get('subject/{id}/edit', 'SubjectController@edit')->name('subject.edit');
        Route::patch('subject/{id}', 'SubjectController@update')->name('subject.update');
        Route::delete('subject/{id}', 'SubjectController@destroy')->name('subject.destroy');


//Subject to Ds
        Route::get('subjectToDs', 'SubjectToDsController@index');
        Route::post('subjectToDs/getDsList', 'SubjectToDsController@getDsList');
        Route::post('subjectToDs/store', 'SubjectToDsController@store');
        Route::post('subjectToDs/getAssignedDs', 'SubjectToDsController@getAssignedDs');

// Module to Subject
        Route::get('moduleToSubject', 'ModuleToSubjectController@index');
        Route::post('moduleToSubject/getDsList', 'ModuleToSubjectController@getDsList');
        Route::post('moduleToSubject/store', 'ModuleToSubjectController@store');
        Route::post('moduleToSubject/getAssignedSubject', 'ModuleToSubjectController@getAssignedSubject');
        Route::post('moduleToSubject/deleteModule', 'ModuleToSubjectController@deleteModule');
        Route::post('moduleToSubject/cloneCourse', 'ModuleToSubjectController@cloneCourse');
        Route::post('moduleToSubject/getCourseDetails', 'ModuleToSubjectController@getCourseDetails');
        Route::post('moduleToSubject/clone', 'ModuleToSubjectController@clone');

        // GS feedback activate/deactivate for ds
        Route::get('activateGsFeedbackForDs', 'ActivateGsFeedbackForDsController@index');
        Route::post('activateGsFeedbackForDs/setStat', 'ActivateGsFeedbackForDsController@setStat');

        // GS feedback activate/deactivate for cm
        Route::get('activateGsFeedbackForCm', 'ActivateGsFeedbackForCmController@index');
        Route::post('activateGsFeedbackForCm/setStat', 'ActivateGsFeedbackForCmController@setStat');

        // Unlock Ds Feedback
        Route::get('unlockDsFeedback', 'UnlockDsFeedbackController@index');
        Route::post('unlockDsFeedback/filter', 'UnlockDsFeedbackController@filter');
        Route::post('unlockDsFeedback/unlockRequest', 'UnlockDsFeedbackController@unlockRequest');
        Route::post('unlockDsFeedback/denyRequest', 'UnlockDsFeedbackController@denyRequest');


        // Unlock Cm Feedback
        Route::get('unlockCmFeedback', 'UnlockCmFeedbackController@index');
        Route::post('unlockCmFeedback/filter', 'UnlockCmFeedbackController@filter');
        Route::post('unlockCmFeedback/unlockRequest', 'UnlockCmFeedbackController@unlockRequest');
        Route::post('unlockCmFeedback/denyRequest', 'UnlockCmFeedbackController@denyRequest');

        //Content Category Management
        Route::post('contentCategory/filter', 'ContentCategoryController@filter');
        Route::get('contentCategory', 'ContentCategoryController@index')->name('contentCategory.index');
        Route::get('contentCategory/create', 'ContentCategoryController@create')->name('contentCategory.create');
        Route::post('/contentCategory', 'ContentCategoryController@store')->name('contentCategory.store');
        Route::get('contentCategory/{id}/edit', 'ContentCategoryController@edit')->name('contentCategory.edit');
        Route::patch('contentCategory/{id}', 'ContentCategoryController@update')->name('contentCategory.update');
        Route::delete('contentCategory/{id}', 'ContentCategoryController@destroy')->name('contentCategory.destroy');


        //assessment activate/deactivate
        Route::get('assessmentActDeact', 'AssessmentActDeactController@index');
        Route::post('assessmentActDeact/setStat', 'AssessmentActDeactController@setStat');
        Route::post('assessmentActDeact/getDsMarkingSummary', 'AssessmentActDeactController@getDsMarkingSummary');
        Route::post('assessmentActDeact/getCmActivationState', 'AssessmentActDeactController@getCmActivationState');
        Route::post('assessmentActDeact/setCmMarkingGroupStat', 'AssessmentActDeactController@setCmMarkingGroupStat');
        Route::post('assessmentActDeact/setCmForceSubmit', 'AssessmentActDeactController@setCmForceSubmit');

        //mutual assessment submission state
        Route::get('mutualAssessmentSubmissionState', 'MutualAssessmentSubmissionStateController@index');
        Route::post('mutualAssessmentSubmissionState/getCmMarkingSummary', 'MutualAssessmentSubmissionStateController@getCmMarkingSummary');

        //User Group
        Route::post('userGroup/filter', 'UserGroupController@filter');
        Route::post('userGroup/getOrder', 'UserGroupController@getOrder');
        Route::get('userGroup', 'UserGroupController@index')->name('userGroup.index');

        //rank
        Route::post('rank/filter', 'RankController@filter');
        Route::post('rank/getOrder', 'RankController@getOrder');
        Route::get('rank', 'RankController@index')->name('rank.index');
        Route::get('rank/create', 'RankController@create')->name('rank.create');
        Route::post('rank', 'RankController@store')->name('rank.store');
        Route::get('rank/{id}/edit', 'RankController@edit')->name('rank.edit');
        Route::patch('rank/{id}', 'RankController@update')->name('rank.update');
        Route::delete('rank/{id}', 'RankController@destroy')->name('rank.destroy');
        //Trait
        Route::post('crTrait/filter', 'CrTraitController@filter');
        Route::post('crTrait/getOrder', 'CrTraitController@getOrder');
        Route::get('crTrait', 'CrTraitController@index')->name('crTrait.index');
        Route::get('crTrait/create', 'CrTraitController@create')->name('crTrait.create');
        Route::post('crTrait', 'CrTraitController@store')->name('crTrait.store');
        Route::get('crTrait/{id}/edit', 'CrTraitController@edit')->name('crTrait.edit');
        Route::patch('crTrait/{id}', 'CrTraitController@update')->name('crTrait.update');
        Route::delete('crTrait/{id}', 'CrTraitController@destroy')->name('crTrait.destroy');

        //Sentence to trait
        Route::get('crSentenceToTrait', 'CrSentenceToTraitController@index');
        Route::post('crSentenceToTrait/getCourse', 'CrSentenceToTraitController@getCourse');
        Route::post('crSentenceToTrait/getMarkingSlab', 'CrSentenceToTraitController@getMarkingSlab');
        Route::post('crSentenceToTrait/addSentence', 'CrSentenceToTraitController@addSentence');
        Route::post('crSentenceToTrait/saveSentenceToTrait', 'CrSentenceToTraitController@saveSentenceToTrait');
        Route::post('crSentenceToTrait/getCloneSentenceToTrait', 'CrSentenceToTraitController@getCloneSentenceToTrait');
        Route::post('crSentenceToTrait/getTraitList', 'CrSentenceToTraitController@getTraitList');
        Route::post('crSentenceToTrait/cloneSentenceToTrait', 'CrSentenceToTraitController@cloneSentenceToTrait');
        //course report grouping
        Route::get('crGrouping', 'CrGroupingController@index');
        Route::post('crGrouping/getCourse', 'CrGroupingController@getCourse');
        Route::post('crGrouping/getCmSelectionPanel', 'CrGroupingController@getCmSelectionPanel');
        Route::post('crGrouping/getCmGroupWiseSearchCm', 'CrGroupingController@getCmGroupWiseSearchCm');
        Route::post('crGrouping/getFilterIndividualCm', 'CrGroupingController@getFilterIndividualCm');
        Route::post('crGrouping/setCm', 'CrGroupingController@setCm');
        Route::post('crGrouping/saveGroup', 'CrGroupingController@saveGroup');
        Route::post('crGrouping/removeGroup', 'CrGroupingController@removeGroup');

        //marking reflection
        Route::get('crMarkingReflection', 'CrMarkingReflectionController@index');
        Route::post('crMarkingReflection/getCourse', 'CrMarkingReflectionController@getCourse');
        Route::post('crMarkingReflection/getReflection', 'CrMarkingReflectionController@getReflection');
        Route::post('crMarkingReflection/addSentence', 'CrMarkingReflectionController@addSentence');
        Route::post('crMarkingReflection/saveReflection', 'CrMarkingReflectionController@saveReflection');

        //clear course reports
        Route::post('crClearReport/clear', 'CrClearReportController@clear');
        Route::get('crClearReport', 'CrClearReportController@index');
        Route::post('crClearReport/getCourse', 'CrClearReportController@getCourse');
        Route::post('crClearReport/filter', 'CrClearReportController@filter');

        // Report Activation
        Route::get('crReportactivation', 'ReportActivationController@index');
        Route::post('crReportActivation/setStat', 'ReportActivationController@setStat');
        Route::post('crReportActivation/getCourse', 'ReportActivationController@getCourse');
        Route::post('crReportActivation/getActDeactBtn', 'ReportActivationController@getActDeactBtn');

        //appointment
        Route::post('appointment/filter', 'AppointmentController@filter');
        Route::post('appointment/getOrder', 'AppointmentController@getOrder');
        Route::get('appointment', 'AppointmentController@index')->name('appointment.index');
        Route::get('appointment/create', 'AppointmentController@create')->name('appointment.create');
        Route::post('appointment', 'AppointmentController@store')->name('appointment.store');
        Route::get('appointment/{id}/edit', 'AppointmentController@edit')->name('appointment.edit');
        Route::patch('appointment/{id}', 'AppointmentController@update')->name('appointment.update');
        Route::delete('appointment/{id}', 'AppointmentController@destroy')->name('appointment.destroy');

        //CM appointment
        Route::post('cmAppointment/filter', 'CmAppointmentController@filter');
        Route::post('cmAppointment/getOrder', 'CmAppointmentController@getOrder');
        Route::get('cmAppointment', 'CmAppointmentController@index')->name('cmAppointment.index');
        Route::get('cmAppointment/create', 'CmAppointmentController@create')->name('cmAppointment.create');
        Route::post('cmAppointment', 'CmAppointmentController@store')->name('cmAppointment.store');
        Route::get('cmAppointment/{id}/edit', 'CmAppointmentController@edit')->name('cmAppointment.edit');
        Route::patch('cmAppointment/{id}', 'CmAppointmentController@update')->name('cmAppointment.update');
        Route::delete('cmAppointment/{id}', 'CmAppointmentController@destroy')->name('cmAppointment.destroy');

        //service appointment
        Route::post('serviceAppointment/filter', 'ServiceAppointmentController@filter');
        Route::post('serviceAppointment/getOrder', 'ServiceAppointmentController@getOrder');
        Route::get('serviceAppointment', 'ServiceAppointmentController@index')->name('serviceAppointment.index');
        Route::get('serviceAppointment/create', 'ServiceAppointmentController@create')->name('serviceAppointment.create');
        Route::post('serviceAppointment', 'ServiceAppointmentController@store')->name('serviceAppointment.store');
        Route::get('serviceAppointment/{id}/edit', 'ServiceAppointmentController@edit')->name('serviceAppointment.edit');
        Route::patch('serviceAppointment/{id}', 'ServiceAppointmentController@update')->name('serviceAppointment.update');
        Route::delete('serviceAppointment/{id}', 'ServiceAppointmentController@destroy')->name('serviceAppointment.destroy');

        //arms & sevice
        Route::post('armsService/filter/', 'ArmsServiceController@filter');
        Route::get('armsService', 'ArmsServiceController@index')->name('armsService.index');
        Route::get('armsService/create', 'ArmsServiceController@create')->name('armsService.create');
        Route::post('armsService', 'ArmsServiceController@store')->name('armsService.store');
        Route::get('armsService/{id}/edit', 'ArmsServiceController@edit')->name('armsService.edit');
        Route::patch('armsService/{id}', 'ArmsServiceController@update')->name('armsService.update');
        Route::delete('armsService/{id}', 'ArmsServiceController@destroy')->name('armsService.destroy');

        //cm group
        Route::post('cmGroup/filter', 'CmGroupController@filter');
        Route::get('cmGroup', 'CmGroupController@index')->name('cmGroup.index');
        Route::get('cmGroup/create', 'CmGroupController@create')->name('cmGroup.create');
        Route::post('cmGroup', 'CmGroupController@store')->name('cmGroup.store');
        Route::get('cmGroup/{id}/edit', 'CmGroupController@edit')->name('cmGroup.edit');
        Route::patch('cmGroup/{id}', 'CmGroupController@update')->name('cmGroup.update');
        Route::delete('cmGroup/{id}', 'CmGroupController@destroy')->name('cmGroup.destroy');

        //cm group member template management
        Route::get('cmGroupMemberTemplate', 'CmGroupMemberTemplateController@index');
        Route::post('cmGroupMemberTemplate/getCmGroup', 'CmGroupMemberTemplateController@getCmGroup');
        Route::post('cmGroupMemberTemplate/cmGroupMember', 'CmGroupMemberTemplateController@cmGroupMember');
        Route::post('cmGroupMemberTemplate/saveCmGroupMember', 'CmGroupMemberTemplateController@saveCmGroupMember');
        Route::post('cmGroupMemberTemplate/getAssignedCm', 'CmGroupMemberTemplateController@getAssignedCm');

        //ds group member template management
        Route::get('dsGroupMemberTemplate', 'DsGroupMemberTemplateController@index');
        Route::post('dsGroupMemberTemplate/getDsGroup', 'DsGroupMemberTemplateController@getDsGroup');
        Route::post('dsGroupMemberTemplate/dsGroupMember', 'DsGroupMemberTemplateController@dsGroupMember');
        Route::post('dsGroupMemberTemplate/saveDsGroupMember', 'DsGroupMemberTemplateController@saveDsGroupMember');
        Route::post('dsGroupMemberTemplate/getAssignedDs', 'DsGroupMemberTemplateController@getAssignedDs');

        //ds group
        Route::post('dsGroup/filter', 'DsGroupController@filter');
        Route::get('dsGroup', 'DsGroupController@index')->name('dsGroup.index');
        Route::get('dsGroup/create', 'DsGroupController@create')->name('dsGroup.create');
        Route::post('dsGroup', 'DsGroupController@store')->name('dsGroup.store');
        Route::get('dsGroup/{id}/edit', 'DsGroupController@edit')->name('dsGroup.edit');
        Route::patch('dsGroup/{id}', 'DsGroupController@update')->name('dsGroup.update');
        Route::delete('dsGroup/{id}', 'DsGroupController@destroy')->name('dsGroup.destroy');

        //training year
        Route::post('trainingYear/changeStatus/', 'TrainingYearController@changeStatus');
        Route::post('trainingYear/filter/', 'TrainingYearController@filter');
        Route::get('trainingYear', 'TrainingYearController@index')->name('trainingYear.index');
        Route::get('trainingYear/create', 'TrainingYearController@create')->name('trainingYear.create');
        Route::post('trainingYear', 'TrainingYearController@store')->name('trainingYear.store');
        Route::get('trainingYear/{id}/edit', 'TrainingYearController@edit')->name('trainingYear.edit');
        Route::patch('trainingYear/{id}', 'TrainingYearController@update')->name('trainingYear.update');
        Route::delete('trainingYear/{id}', 'TrainingYearController@destroy')->name('trainingYear.destroy');

        //wing
        Route::post('wing/filter/', 'WingController@filter');
        Route::get('wing', 'WingController@index')->name('wing.index');
        Route::get('wing/create', 'WingController@create')->name('wing.create');
        Route::post('wing', 'WingController@store')->name('wing.store');
        Route::get('wing/{id}/edit', 'WingController@edit')->name('wing.edit');
        Route::patch('wing/{id}', 'WingController@update')->name('wing.update');
        Route::delete('wing/{id}', 'WingController@destroy')->name('wing.destroy');

        //term
        Route::post('term/filter/', 'TermController@filter');
        Route::get('term', 'TermController@index')->name('term.index');
        Route::get('term/create', 'TermController@create')->name('term.create');
        Route::post('term', 'TermController@store')->name('term.store');
        Route::get('term/{id}/edit', 'TermController@edit')->name('term.edit');
        Route::patch('term/{id}', 'TermController@update')->name('term.update');
        Route::delete('term/{id}', 'TermController@destroy')->name('term.destroy');

        //syndicate
        Route::post('syndicate/filter/', 'SyndicateController@filter');
        Route::get('syndicate', 'SyndicateController@index')->name('syndicate.index');
        Route::get('syndicate/create', 'SyndicateController@create')->name('syndicate.create');
        Route::post('syndicate', 'SyndicateController@store')->name('syndicate.store');
        Route::get('syndicate/{id}/edit', 'SyndicateController@edit')->name('syndicate.edit');
        Route::patch('syndicate/{id}', 'SyndicateController@update')->name('syndicate.update');
        Route::delete('syndicate/{id}', 'SyndicateController@destroy')->name('syndicate.destroy');

        // sub syndicate
        Route::post('subSyndicate/filter/', 'SubSyndicateController@filter');
        Route::get('subSyndicate', 'SubSyndicateController@index')->name('subSyndicate.index');
        Route::get('subSyndicate/create', 'SubSyndicateController@create')->name('subSyndicate.create');
        Route::post('subSyndicate', 'SubSyndicateController@store')->name('subSyndicate.store');
        Route::get('subSyndicate/{id}/edit', 'SubSyndicateController@edit')->name('subSyndicate.edit');
        Route::patch('subSyndicate/{id}', 'SubSyndicateController@update')->name('subSyndicate.update');
        Route::delete('subSyndicate/{id}', 'SubSyndicateController@destroy')->name('subSyndicate.destroy');

        //course Id
        Route::post('courseId/filter', 'CourseIdController@filter');
        Route::get('courseId', 'CourseIdController@index')->name('courseId.index');
        Route::get('courseId/create', 'CourseIdController@create')->name('courseId.create');
        Route::post('courseId', 'CourseIdController@store')->name('courseId.store');
        Route::get('courseId/{id}/edit', 'CourseIdController@edit')->name('courseId.edit');
        Route::patch('courseId/{id}', 'CourseIdController@update')->name('courseId.update');
        Route::delete('courseId/{id}', 'CourseIdController@destroy')->name('courseId.destroy');
        Route::post('courseId/close', 'CourseIdController@close');
        Route::post('courseId/reactive', 'CourseIdController@reactive');
        Route::post('courseId/requestCourseSatatusSummary', 'CourseIdController@requestCourseSatatusSummary');
        Route::post('courseId/getDsMarkingSummary', 'CourseIdController@getDsMarkingSummary');
        Route::post('courseId/getCloneEvent', 'CourseIdController@getCloneEvent');
        Route::post('courseId/setCloneEvent', 'CourseIdController@setCloneEvent');
        Route::post('courseId/getPrevCourseEvent', 'CourseIdController@getPrevCourseEvent');

        //user
        Route::post('user/filter/', 'UserController@filter');
        Route::post('user/getInstitue', 'UserController@getInstitue');
        Route::post('user/getDteBr', 'UserController@getDteBr');
        Route::post('user/getWing', 'UserController@getWing');
        Route::post('user/getServiceWiseRankAppPnp', 'UserController@getServiceWiseRankAppPnp');
        Route::get('user', 'UserController@index')->name('user.index');
        Route::get('user/create', 'UserController@create')->name('user.create');
        Route::post('user', 'UserController@store')->name('user.store');
        Route::get('user/{id}/edit', 'UserController@edit')->name('user.edit');
        Route::patch('user/{id}', 'UserController@update')->name('user.update');
        Route::delete('user/{id}', 'UserController@destroy')->name('user.destroy');
        Route::post('user/getRank', 'UserController@getRank');
        Route::post('user/getCommisioningDate', 'UserController@getCommisioningDate');

        //user Profile
        Route::get('user/{id}/profile', 'UserController@profile')->name('user.profile');

        //cm
        Route::post('cm/filter/', 'CmController@filter');
        Route::post('cm/getRank', 'CmController@getRank');
        Route::post('cm/getCommisioningDate', 'CmController@getCommisioningDate');
        Route::get('cm', 'CmController@index')->name('cm.index');
        Route::get('cm/create', 'CmController@create')->name('cm.create');
        Route::post('cm', 'CmController@store')->name('cm.store');
        Route::get('cm/{id}/edit', 'CmController@edit')->name('cm.edit');
        Route::patch('cm/{id}', 'CmController@update')->name('cm.update');
        Route::delete('cm/{id}', 'CmController@destroy')->name('cm.destroy');

        Route::get('cm/{id}/profile', 'CmController@profile')->name('cm.profile');
        Route::post('cm/updateFamilyInfo', 'CmController@updateFamilyInfo')->name('cm.familyInfoUpdate');
        Route::post('cm/updateMaritalStatus', 'CmController@updateMaritalStatus')->name('cm.maritialStatusUpdate');
        Route::post('cm/updateBrotherSisterInfo', 'CmController@updateBrotherSisterInfo')->name('cm.brotherSisterInfoUpdate');
        Route::post('cm/rowAddForBrotherSister', 'CmController@rowAddForBrotherSister')->name('cm.rowAddForBrotherSister');
        Route::post('cm/getDistrict', 'CmController@getDistrict')->name('cm.getDistrict');
        Route::post('cm/getThana', 'CmController@getThana')->name('cm.getThana');
        Route::post('cm/updatePermanentAddress', 'CmController@updatePermanentAddress')->name('cm.permanentAddressUpdate');
        Route::post('cm/rowAddForCivilEducation', 'CmController@rowAddForCivilEducation')->name('cm.rowAddForCivilEducation');
        Route::post('cm/updateCivilEducationInfo', 'CmController@updateCivilEducationInfo')->name('cm.civilEducationInfoUpdate');
        Route::post('cm/rowAddForServiceRecord', 'CmController@rowAddForServiceRecord')->name('cm.rowAddForServiceRecord');
        Route::post('cm/updateServiceRecordInfo', 'CmController@updateServiceRecordInfo')->name('cm.serviceRecordInfoUpdate');
        Route::post('cm/rowAddForAwardRecord', 'CmController@rowAddForAwardRecord')->name('cm.rowAddForAwardRecord');
        Route::post('cm/updateAwardRecordInfo', 'CmController@updateAwardRecordInfo')->name('cm.awardRecordInfoUpdate');
        Route::post('cm/rowAddForUnMsn', 'CmController@rowAddForUnMsn')->name('cm.rowAddForUnMsn');
        Route::post('cm/updateUnMsn', 'CmController@updateUnMsn')->name('cm.punishmentRecordInfoUpdate');
        Route::post('cm/rowAddForDefenceRelative', 'CmController@rowAddForDefenceRelative')->name('cm.rowAddForDefenceRelative');
        Route::post('cm/updateDefenceRelativeInfo', 'CmController@updateDefenceRelativeInfo')->name('cm.defenceRelativeInfoUpdate');
        Route::post('cm/updateNextKin', 'CmController@updateNextKin')->name('cm.nextOfKinInfoUpdate');
        Route::post('cm/updateMedicalDetails', 'CmController@updateMedicalDetails')->name('cm.medicalDetailsUpdate');
        Route::post('cm/updateWinterTraining', 'CmController@updateWinterTraining')->name('cm.winterTrainingUpdate');
        Route::post('cm/updateCmOthersInfo', 'CmController@updateCmOthersInfo')->name('cm.cmOthersUpdate');

        Route::post('cm/rowAddForCountry', 'CmController@rowAddForCountry')->name('cm.rowAddForCountry');
        Route::post('cm/rowAddForChild', 'CmController@rowAddForChild')->name('cm.rowAddForChild');
        Route::post('cm/rowAddForBank', 'CmController@rowAddForBank')->name('cm.rowAddForBank');
        Route::post('cm/updateCountryVisit', 'CmController@updateCountryVisit')->name('cm.updateVisitCountry');
        Route::post('cm/updateBank', 'CmController@updateBank')->name('cm.updateBank');

        //Staff
        Route::post('staff/filter/', 'StaffController@filter');
        Route::get('staff', 'StaffController@index')->name('staff.index');
        Route::post('staff/getRank', 'StaffController@getRank');
        Route::get('staff/create', 'StaffController@create')->name('staff.create');
        Route::post('staff/store', 'StaffController@store')->name('staff.store');
        Route::get('staff/{id}/edit', 'StaffController@edit')->name('staff.edit');
        Route::patch('staff/{id}', 'StaffController@update')->name('staff.update');
        Route::delete('staff/{id}', 'StaffController@destroy')->name('staff.destroy');

        //Mutual Assessment
        Route::get('mutualAssessment/markingSheet', 'MutualAssessmentController@markingSheet');
        Route::post('mutualAssessment/getTerm', 'MutualAssessmentController@getTerm');
        Route::post('mutualAssessment/getCmAndSubSyndicate', 'MutualAssessmentController@getCmAndSubSyndicate');
        Route::post('mutualAssessment/getSyn', 'MutualAssessmentController@getSyn');
        Route::post('mutualAssessment/getSubEvent', 'MutualAssessmentController@getSubEvent');
        Route::post('mutualAssessment/getSubSubEvent', 'MutualAssessmentController@getSubSubEvent');
        Route::post('mutualAssessment/getSubSubSubEvent', 'MutualAssessmentController@getSubSubSubEvent');
        Route::post('mutualAssessment/getEventGroup', 'MutualAssessmentController@getEventGroup');
        Route::post('mutualAssessment/getFactor', 'MutualAssessmentController@getFactor');
        Route::post('mutualAssessment/getCmbySubSyn', 'MutualAssessmentController@getCmbySubSyn');
        Route::post('mutualAssessment/changeDeliverStatus', 'MutualAssessmentController@changeDeliverStatus');
        Route::post('mutualAssessment/getPreviewBtn', 'MutualAssessmentController@getPreviewBtn');
        Route::post('mutualAssessment/previewMarkingSheet', 'MutualAssessmentController@previewMarkingSheet');
        Route::post('mutualAssessment/generate', 'MutualAssessmentController@generate');

        Route::get('mutualAssessment/importMarkingSheet', 'MutualAssessmentController@importMarkingSheet');
        Route::post('mutualAssessment/getSubsynAndCmList', 'MutualAssessmentController@getSubsynAndCmList');
        Route::post('mutualAssessment/getCmListBySubSyn', 'MutualAssessmentController@getCmListBySubSyn');
        Route::post('mutualAssessment/getFileUploader', 'MutualAssessmentController@getFileUploader');
        Route::post('mutualAssessment/import', 'MutualAssessmentController@import');
        Route::post('mutualAssessment/saveImportedData', 'MutualAssessmentController@saveImportedData');

        //Marking Group management
        Route::get('markingGroup', 'MarkingGroupController@index');
        Route::post('markingGroup/getTerm', 'MarkingGroupController@getTerm');
        Route::post('markingGroup/getEvent', 'MarkingGroupController@getEvent');
        Route::post('markingGroup/getSubEventCmDs', 'MarkingGroupController@getSubEventCmDs');
        Route::post('markingGroup/getSubSubEventCmDs', 'MarkingGroupController@getSubSubEventCmDs');
        Route::post('markingGroup/getSubSubSubEventCmDs', 'MarkingGroupController@getSubSubSubEventCmDs');
        Route::post('markingGroup/getCmDs', 'MarkingGroupController@getCmDs');
        Route::post('markingGroup/getCmDsSelection', 'MarkingGroupController@getCmDsSelection');
        Route::post('markingGroup/getSubSyn', 'MarkingGroupController@getSubSyn');
        Route::post('markingGroup/getGroupTemplateWiseSearchCm', 'MarkingGroupController@getGroupTemplateWiseSearchCm');
        Route::post('markingGroup/setCm', 'MarkingGroupController@setCm');
        Route::post('markingGroup/getFilterIndividualFullCm', 'MarkingGroupController@getFilterIndividualFullCm');
        Route::post('markingGroup/getFilterIndividualCm', 'MarkingGroupController@getFilterIndividualCm');
        Route::post('markingGroup/getSynWiseSearchCm', 'MarkingGroupController@getSynWiseSearchCm');
        Route::post('markingGroup/getGroupTemplateWiseSearchDs', 'MarkingGroupController@getGroupTemplateWiseSearchDs');
        Route::post('markingGroup/getFilterIndividualDs', 'MarkingGroupController@getFilterIndividualDs');
        Route::post('markingGroup/setDs', 'MarkingGroupController@setDs');
        Route::post('markingGroup/saveMarkingGroup', 'MarkingGroupController@saveMarkingGroup');
        Route::post('markingGroup/removeMarkingGroup', 'MarkingGroupController@removeMarkingGroup');

        //syn to sub syn
        Route::get('synToSubSyn', 'SynToSubSynController@index');
        Route::post('synToSubSyn/getSyn', 'SynToSubSynController@getSyn');
        Route::post('synToSubSyn/getSubSyn', 'SynToSubSynController@getSubSyn');
        Route::post('synToSubSyn/saveSubSyn', 'SynToSubSynController@saveSubSyn');

        //syn to course
        Route::post('synToCourse/getSyn', 'SynToCourseController@getSyn');
        Route::post('synToCourse/saveSyn', 'SynToCourseController@saveSyn');
        Route::get('synToCourse', 'SynToCourseController@index');


        //cm group to course
        Route::post('cmGroupToCourse/getCmGroup', 'CmGroupToCourseController@getCmGroup');
        Route::post('cmGroupToCourse/saveCmGroup', 'CmGroupToCourseController@saveCmGroup');
        Route::get('cmGroupToCourse', 'CmGroupToCourseController@index');

        // commissioning course
        Route::post('commissioningCourse/close', 'CommissioningCourseController@close');
        Route::post('commissioningCourse/filter', 'CommissioningCourseController@filter');
        Route::get('commissioningCourse', 'CommissioningCourseController@index')->name('commissioningCourse.index');
        Route::get('commissioningCourse/create', 'CommissioningCourseController@create')->name('commissioningCourse.create');
        Route::post('commissioningCourse', 'CommissioningCourseController@store')->name('commissioningCourse.store');
        Route::get('commissioningCourse/{id}/edit', 'CommissioningCourseController@edit')->name('commissioningCourse.edit');
        Route::patch('commissioningCourse/{id}', 'CommissioningCourseController@update')->name('commissioningCourse.update');
        Route::delete('commissioningCourse/{id}', 'CommissioningCourseController@destroy')->name('commissioningCourse.destroy');



        //ds group to course
        Route::post('dsGroupToCourse/getDsGroup', 'DsGroupToCourseController@getDsGroup');
        Route::post('dsGroupToCourse/saveDsGroup', 'DsGroupToCourseController@saveDsGroup');
        Route::get('dsGroupToCourse', 'DsGroupToCourseController@index');


        //event group
        Route::post('eventGroup/filter', 'EventGroupController@filter');
        Route::get('eventGroup', 'EventGroupController@index')->name('eventGroup.index');
        Route::get('eventGroup/create', 'EventGroupController@create')->name('eventGroup.create');
        Route::post('eventGroup', 'EventGroupController@store')->name('eventGroup.store');
        Route::get('eventGroup/{id}/edit', 'EventGroupController@edit')->name('eventGroup.edit');
        Route::patch('eventGroup/{id}', 'EventGroupController@update')->name('eventGroup.update');
        Route::delete('eventGroup/{id}', 'EventGroupController@destroy')->name('eventGroup.destroy');

        //event group to course
        Route::post('eventGroupToCourse/getEventGroup', 'EventGroupToCourseController@getEventGroup');
        Route::post('eventGroupToCourse/saveEventGroup', 'EventGroupToCourseController@saveEventGroup');
        Route::get('eventGroupToCourse', 'EventGroupToCourseController@index');

        //event  to event group
        Route::post('eventToEventGroup/getEventGroup', 'EventToEventGroupController@getEventGroup');
        Route::post('eventToEventGroup/saveEventGroup', 'EventToEventGroupController@saveEventGroup');
        Route::get('eventToEventGroup', 'EventToEventGroupController@index');


        //term to course (term scheduling & activation/closing)
        Route::post('termToCourse/courseSchedule', 'TermToCourseController@courseSchedule');
        Route::post('termToCourse/getActiveOrClose', 'TermToCourseController@getActiveOrClose');
        Route::get('termToCourse/activationOrClosing', 'TermToCourseController@activationOrClosing');
        Route::post('termToCourse/activeInactive', 'TermToCourseController@activeInactive');
        Route::post('termToCourse/redioAcIn', 'TermToCourseController@redioAcIn');
        Route::post('termToCourse/getTerm', 'TermToCourseController@getTerm');
        Route::post('termToCourse/saveCourse', 'TermToCourseController@saveCourse');
        Route::get('termToCourse', 'TermToCourseController@index');
        Route::post('termToCourse/requestCourseSatatusSummary', 'TermToCourseController@requestCourseSatatusSummary');
        Route::post('termToCourse/getDsMarkingSummary', 'TermToCourseController@getDsMarkingSummary');


        //event
        Route::post('event/filter/', 'EventController@filter');
        Route::get('event', 'EventController@index')->name('event.index');
        Route::get('event/create', 'EventController@create')->name('event.create');
        Route::post('event', 'EventController@store')->name('event.store');
        Route::get('event/{id}/edit', 'EventController@edit')->name('event.edit');
        Route::patch('event/{id}', 'EventController@update')->name('event.update');
        Route::delete('event/{id}', 'EventController@destroy')->name('event.destroy');
        Route::post('event/getCheckEntrance', 'EventController@getCheckEntrance');

        //sub event
        Route::post('subEvent/filter/', 'SubEventController@filter');
        Route::get('subEvent', 'SubEventController@index')->name('subEvent.index');
        Route::get('subEvent/create', 'SubEventController@create')->name('subEvent.create');
        Route::post('subEvent', 'SubEventController@store')->name('subEvent.store');
        Route::get('subEvent/{id}/edit', 'SubEventController@edit')->name('subEvent.edit');
        Route::patch('subEvent/{id}', 'SubEventController@update')->name('subEvent.update');
        Route::delete('subEvent/{id}', 'SubEventController@destroy')->name('subEvent.destroy');

        //sub event
        Route::post('subEvent/hideShow/', 'SubEventController@hideShow');

//sub sub event
        Route::post('subSubEvent/hideShow/', 'SubSubEventController@hideShow');

//sub sub sub event
        Route::post('subSubSubEvent/hideShow/', 'SubSubSubEventController@hideShow');

//event group
        Route::post('eventGroup/hideShow', 'EventGroupController@hideShow');

        //sub sub event
        Route::post('subSubEvent/filter/', 'SubSubEventController@filter');
        Route::get('subSubEvent', 'SubSubEventController@index')->name('subSubEvent.index');
        Route::get('subSubEvent/create', 'SubSubEventController@create')->name('subSubEvent.create');
        Route::post('subSubEvent', 'SubSubEventController@store')->name('subSubEvent.store');
        Route::get('subSubEvent/{id}/edit', 'SubSubEventController@edit')->name('subSubEvent.edit');
        Route::patch('subSubEvent/{id}', 'SubSubEventController@update')->name('subSubEvent.update');
        Route::delete('subSubEvent/{id}', 'SubSubEventController@destroy')->name('subSubEvent.destroy');

        //sub sub sub event
        Route::post('subSubSubEvent/filter/', 'SubSubSubEventController@filter');
        Route::get('subSubSubEvent', 'SubSubSubEventController@index')->name('subSubSubEvent.index');
        Route::get('subSubSubEvent/create', 'SubSubSubEventController@create')->name('subSubSubEvent.create');
        Route::post('subSubSubEvent', 'SubSubSubEventController@store')->name('subSubSubEvent.store');
        Route::get('subSubSubEvent/{id}/edit', 'SubSubSubEventController@edit')->name('subSubSubEvent.edit');
        Route::patch('subSubSubEvent/{id}', 'SubSubSubEventController@update')->name('subSubSubEvent.update');
        Route::delete('subSubSubEvent/{id}', 'SubSubSubEventController@destroy')->name('subSubSubEvent.destroy');

        //event tree
        Route::post('eventTree/saveEventTree', 'EventTreeController@saveEventTree');
        Route::get('eventTree', 'EventTreeController@index');
        Route::post('eventTree/getPrevEvent', 'EventTreeController@getPrevEvent');
        Route::post('eventTree/getSubEvent', 'EventTreeController@getSubEvent');
        Route::post('eventTree/getSubSubEvent', 'EventTreeController@getSubSubEvent');
        Route::post('eventTree/getSubSubSubEvent', 'EventTreeController@getSubSubSubEvent');


        //grading System Management
        Route::post('gradingSystem/filter/', 'GradingSystemController@filter');
        Route::get('gradingSystem', 'GradingSystemController@index')->name('gradingSystem.index');
        Route::get('gradingSystem/create', 'GradingSystemController@create')->name('gradingSystem.create');
        Route::post('gradingSystem', 'GradingSystemController@store')->name('gradingSystem.store');
        Route::get('gradingSystem/{id}/edit', 'GradingSystemController@edit')->name('gradingSystem.edit');
        Route::patch('gradingSystem/{id}', 'GradingSystemController@update')->name('gradingSystem.update');
        Route::delete('gradingSystem/{id}', 'GradingSystemController@destroy')->name('gradingSystem.destroy');
        Route::post('gradingSystem/getCheckEntrance', 'GradingSystemController@getCheckEntrance');

        //Marking Slab (for Course Report)
        Route::post('crMarkingSlab/filter/', 'CrMarkingSlabController@filter');
        Route::get('crMarkingSlab', 'CrMarkingSlabController@index')->name('crMarkingSlab.index');
        Route::get('crMarkingSlab/create', 'CrMarkingSlabController@create')->name('crMarkingSlab.create');
        Route::post('crMarkingSlab', 'CrMarkingSlabController@store')->name('crMarkingSlab.store');
        Route::get('crMarkingSlab/{id}/edit', 'CrMarkingSlabController@edit')->name('crMarkingSlab.edit');
        Route::patch('crMarkingSlab/{id}', 'CrMarkingSlabController@update')->name('crMarkingSlab.update');
        Route::delete('crMarkingSlab/{id}', 'CrMarkingSlabController@destroy')->name('crMarkingSlab.destroy');




        // mutual Assessment Event Management
        Route::post('mutualAssessmentFactor/filter/', 'MutualAssessmentEventController@filter');
        Route::get('mutualAssessmentFactor', 'MutualAssessmentEventController@index')->name('mutualAssessmentFactor.index');
        Route::get('mutualAssessmentFactor/create', 'MutualAssessmentEventController@create')->name('mutualAssessmentFactor.create');
        Route::post('mutualAssessmentFactor', 'MutualAssessmentEventController@store')->name('mutualAssessmentFactor.store');
        Route::get('mutualAssessmentFactor/{id}/edit', 'MutualAssessmentEventController@edit')->name('mutualAssessmentFactor.edit');
        Route::patch('mutualAssessmentFactor/{id}', 'MutualAssessmentEventController@update')->name('mutualAssessmentFactor.update');
        Route::delete('mutualAssessmentFactor/{id}', 'MutualAssessmentEventController@destroy')->name('mutualAssessmentFactor.destroy');
        Route::post('mutualAssessmentFactor/getCheckEntrance', 'MutualAssessmentEventController@getCheckEntrance');

        //term to MA event
        Route::get('termToMAEvent', 'TermToMAEventController@index');
        Route::post('termToMAEvent/getTerm', 'TermToMAEventController@getTerm');
        Route::post('termToMAEvent/getEvent', 'TermToMAEventController@getEvent');
        Route::post('termToMAEvent/saveTermToMAEvent', 'TermToMAEventController@saveTermToMAEvent');
        Route::post('termToMAEvent/getAddGrouping', 'TermToMAEventController@getAddGrouping');
        Route::post('termToMAEvent/setAddGrouping', 'TermToMAEventController@setAddGrouping');
        Route::post('termToMAEvent/deleteGrouping', 'TermToMAEventController@deleteGrouping');
        Route::post('termToMAEvent/getSubEventOrGroup', 'TermToMAEventController@getSubEventOrGroup');
        Route::post('termToMAEvent/getSubSubEventOrGroup', 'TermToMAEventController@getSubSubEventOrGroup');
        Route::post('termToMAEvent/getSubSubSubEventOrGroup', 'TermToMAEventController@getSubSubSubEventOrGroup');
        Route::post('termToMAEvent/getGroup', 'TermToMAEventController@getGroup');
        Route::post('termToMAEvent/getGroupingCm', 'TermToMAEventController@getGroupingCm');
        Route::post('termToMAEvent/setGroupingCm', 'TermToMAEventController@setGroupingCm');

        //unlock Ma Request
        Route::get('unlockMaRequest', 'UnlockMaRequestController@index')->name('unlockMaRequest');
        Route::post('acceptMaUnlockRequest', 'UnlockMaRequestController@acceptMaUnlockRequest')->name('acceptMaUnlockRequest');
        Route::post('denyMaUnlockRequest', 'UnlockMaRequestController@denyMaUnlockRequest')->name('denyMaUnlockRequest');

        //event to sub event
        Route::get('eventToSubEvent', 'EventToSubEventController@index');
        Route::post('eventToSubEvent/getSubEvent', 'EventToSubEventController@getSubEvent');
        Route::post('eventToSubEvent/saveEventToSubEvent', 'EventToSubEventController@saveEventToSubEvent');
        Route::post('eventToSubEvent/getAssignedSubEvent', 'EventToSubEventController@getAssignedSubEvent');

        //event to sub sub event
        Route::get('eventToSubSubEvent', 'EventToSubSubEventController@index');
        Route::post('eventToSubSubEvent/getSubEvent', 'EventToSubSubEventController@getSubEvent');
        Route::post('eventToSubSubEvent/getSubSubEvent', 'EventToSubSubEventController@getSubSubEvent');
        Route::post('eventToSubSubEvent/saveEventToSubSubEvent', 'EventToSubSubEventController@saveEventToSubSubEvent');
        Route::post('eventToSubSubEvent/getAssignedSubSubEvent', 'EventToSubSubEventController@getAssignedSubSubEvent');

        //event to sub sub sub event
        Route::get('eventToSubSubSubEvent', 'EventToSubSubSubEventController@index');
        Route::post('eventToSubSubSubEvent/getSubEvent', 'EventToSubSubSubEventController@getSubEvent');
        Route::post('eventToSubSubSubEvent/getSubSubEvent', 'EventToSubSubSubEventController@getSubSubEvent');
        Route::post('eventToSubSubSubEvent/getSubSubSubEvent', 'EventToSubSubSubEventController@getSubSubSubEvent');
        Route::post('eventToSubSubSubEvent/saveEventToSubSubSubEvent', 'EventToSubSubSubEventController@saveEventToSubSubSubEvent');
        Route::post('eventToSubSubSubEvent/getAssignedSubSubSubEvent', 'EventToSubSubSubEventController@getAssignedSubSubSubEvent');

        //term to event
        Route::get('termToEvent', 'TermToEventController@index');
        Route::post('termToEvent/getTerm', 'TermToEventController@getTerm');
        Route::post('termToEvent/getEvent', 'TermToEventController@getEvent');
        Route::post('termToEvent/saveTermToEvent', 'TermToEventController@saveTermToEvent');
        Route::post('termToEvent/getAssignedEvent', 'TermToEventController@getAssignedEvent');

        //term to sub event
        Route::get('termToSubEvent', 'TermToSubEventController@index');
        Route::post('termToSubEvent/getTerm', 'TermToSubEventController@getTerm');
        Route::post('termToSubEvent/getEvent', 'TermToSubEventController@getEvent');
        Route::post('termToSubEvent/getSubEvent', 'TermToSubEventController@getSubEvent');
        Route::post('termToSubEvent/saveTermToSubEvent', 'TermToSubEventController@saveTermToSubEvent');
        Route::post('termToSubEvent/getAssignedSubEvent', 'TermToSubEventController@getAssignedSubEvent');

        //term to sub sub event
        Route::get('termToSubSubEvent', 'TermToSubSubEventController@index');
        Route::post('termToSubSubEvent/getTerm', 'TermToSubSubEventController@getTerm');
        Route::post('termToSubSubEvent/getEvent', 'TermToSubSubEventController@getEvent');
        Route::post('termToSubSubEvent/getSubEvent', 'TermToSubSubEventController@getSubEvent');
        Route::post('termToSubSubEvent/getSubSubEvent', 'TermToSubSubEventController@getSubSubEvent');
        Route::post('termToSubSubEvent/saveTermToSubSubEvent', 'TermToSubSubEventController@saveTermToSubSubEvent');
        Route::post('termToSubSubEvent/getAssignedSubSubEvent', 'TermToSubSubEventController@getAssignedSubSubEvent');

        //term to sub sub sub event
        Route::get('termToSubSubSubEvent', 'TermToSubSubSubEventController@index');
        Route::post('termToSubSubSubEvent/getTerm', 'TermToSubSubSubEventController@getTerm');
        Route::post('termToSubSubSubEvent/getEvent', 'TermToSubSubSubEventController@getEvent');
        Route::post('termToSubSubSubEvent/getSubEvent', 'TermToSubSubSubEventController@getSubEvent');
        Route::post('termToSubSubSubEvent/getSubSubEvent', 'TermToSubSubSubEventController@getSubSubEvent');
        Route::post('termToSubSubSubEvent/getSubSubSubEvent', 'TermToSubSubSubEventController@getSubSubSubEvent');
        Route::post('termToSubSubSubEvent/saveTermToSubSubSubEvent', 'TermToSubSubSubEventController@saveTermToSubSubSubEvent');
        Route::post('termToSubSubSubEvent/getAssignedSubSubSubEvent', 'TermToSubSubSubEventController@getAssignedSubSubSubEvent');

        //cm to syn
        Route::get('cmToSyn', 'CmToSynController@index');
        Route::post('cmToSyn/getTerm', 'CmToSynController@getTerm');
        Route::post('cmToSyn/getSyn', 'CmToSynController@getSyn');
        Route::post('cmToSyn/getSubSynCm', 'CmToSynController@getSubSynCm');
        Route::post('cmToSyn/getCm', 'CmToSynController@getCm');
        Route::post('cmToSyn/saveCmToSyn', 'CmToSynController@saveCmToSyn');
        Route::post('cmToSyn/assignedCm', 'CmToSynController@assignedCmDetails');
        Route::post('cmToSyn/getAssignedCm', 'CmToSynController@getAssignedCm');

        //cm to sub syn
//        Route::get('cmToSubSyn', 'CmToSubSynController@index');
//        Route::post('cmToSubSyn/getTerm', 'CmToSubSynController@getTerm');
//        Route::post('cmToSubSyn/getSyn', 'CmToSubSynController@getSyn');
//        Route::post('cmToSubSyn/getSubSyn', 'CmToSubSynController@getSubSyn');
//        Route::post('cmToSubSyn/getCm', 'CmToSubSynController@getCm');
//        Route::post('cmToSubSyn/saveCmToSubSyn', 'CmToSubSynController@saveCmToSubSyn');
//        Route::post('cmToSubSyn/assignedCm', 'CmToSubSynController@assignedCmDetails');
//        Route::post('cmToSubSyn/getAssignedCm', 'CmToSubSynController@getAssignedCm');
        //unit
//        Route::post('unit/filter', 'UnitController@filter');
//        Route::post('unit/getOrder', 'UnitController@getOrder');
//        Route::get('unit', 'UnitController@index')->name('unit.index');
//        Route::get('unit/create', 'UnitController@create')->name('unit.create');
//        Route::post('unit', 'UnitController@store')->name('unit.store');
//        Route::get('unit/{id}/edit', 'UnitController@edit')->name('unit.edit');
//        Route::patch('unit/{id}', 'UnitController@update')->name('unit.update');
//        Route::delete('unit/{id}', 'UnitController@destroy')->name('unit.destroy');
        //formation
//        Route::post('formation/filter', 'FormationController@filter');
//        Route::post('formation/getOrder', 'FormationController@getOrder');
//        Route::get('formation', 'FormationController@index')->name('formation.index');
//        Route::get('formation/create', 'FormationController@create')->name('formation.create');
//        Route::post('formation', 'FormationController@store')->name('formation.store');
//        Route::get('formation/{id}/edit', 'FormationController@edit')->name('formation.edit');
//        Route::patch('formation/{id}', 'FormationController@update')->name('formation.update');
//        Route::delete('formation/{id}', 'FormationController@destroy')->name('formation.destroy');
        // Mil course
        Route::post('milCourse/close', 'MilCourseController@close');
        Route::post('milCourse/filter', 'MilCourseController@filter');
        Route::get('milCourse', 'MilCourseController@index')->name('milCourse.index');
        Route::get('milCourse/create', 'MilCourseController@create')->name('milCourse.create');
        Route::post('milCourse', 'MilCourseController@store')->name('milCourse.store');
        Route::get('milCourse/{id}/edit', 'MilCourseController@edit')->name('milCourse.edit');
        Route::patch('milCourse/{id}', 'MilCourseController@update')->name('milCourse.update');
        Route::delete('milCourse/{id}', 'MilCourseController@destroy')->name('milCourse.destroy');

        //corps/regt/br
//        Route::post('corpsRegtBr/filter', 'CorpsRegtBrController@filter');
//        Route::post('corpsRegtBr/getOrder', 'CorpsRegtBrController@getOrder');
//        Route::get('corpsRegtBr', 'CorpsRegtBrController@index')->name('corpsRegtBr.index');
//        Route::get('corpsRegtBr/create', 'CorpsRegtBrController@create')->name('corpsRegtBr.create');
//        Route::post('corpsRegtBr', 'CorpsRegtBrController@store')->name('corpsRegtBr.store');
//        Route::get('corpsRegtBr/{id}/edit', 'CorpsRegtBrController@edit')->name('corpsRegtBr.edit');
//        Route::patch('corpsRegtBr/{id}', 'CorpsRegtBrController@update')->name('corpsRegtBr.update');
//        Route::delete('corpsRegtBr/{id}', 'CorpsRegtBrController@destroy')->name('corpsRegtBr.destroy');
        //decoration
        Route::post('decoration/filter', 'DecorationController@filter');
        Route::post('decoration/getOrder', 'DecorationController@getOrder');
        Route::get('decoration', 'DecorationController@index')->name('decoration.index');
        Route::get('decoration/create', 'DecorationController@create')->name('decoration.create');
        Route::post('decoration', 'DecorationController@store')->name('decoration.store');
        Route::get('decoration/{id}/edit', 'DecorationController@edit')->name('decoration.edit');
        Route::patch('decoration/{id}', 'DecorationController@update')->name('decoration.update');
        Route::delete('decoration/{id}', 'DecorationController@destroy')->name('decoration.destroy');

        //award
//        Route::post('award/filter', 'AwardController@filter');
//        Route::post('award/getOrder', 'AwardController@getOrder');
//        Route::get('award', 'AwardController@index')->name('award.index');
//        Route::get('award/create', 'AwardController@create')->name('award.create');
//        Route::post('award', 'AwardController@store')->name('award.store');
//        Route::get('award/{id}/edit', 'AwardController@edit')->name('award.edit');
//        Route::patch('award/{id}', 'AwardController@update')->name('award.update');
//        Route::delete('award/{id}', 'AwardController@destroy')->name('award.destroy');
        //hobby
        Route::post('hobby/filter', 'HobbyController@filter');
        Route::post('hobby/getOrder', 'HobbyController@getOrder');
        Route::get('hobby', 'HobbyController@index')->name('hobby.index');
        Route::get('hobby/create', 'HobbyController@create')->name('hobby.create');
        Route::post('hobby', 'HobbyController@store')->name('hobby.store');
        Route::get('hobby/{id}/edit', 'HobbyController@edit')->name('hobby.edit');
        Route::patch('hobby/{id}', 'HobbyController@update')->name('hobby.update');
        Route::delete('hobby/{id}', 'HobbyController@destroy')->name('hobby.destroy');


        //event to appt matrix
        Route::get('eventToApptMatrix', 'EventToApptMatrixController@index');
        Route::post('eventToApptMatrix/getTerm', 'EventToApptMatrixController@getTerm');
        Route::post('eventToApptMatrix/getEvent', 'EventToApptMatrixController@getEvent');
        Route::post('eventToApptMatrix/getSubEventApptMatrix', 'EventToApptMatrixController@getSubEventApptMatrix');
        Route::post('eventToApptMatrix/getSubSubEventApptMatrix', 'EventToApptMatrixController@getSubSubEventApptMatrix');
        Route::post('eventToApptMatrix/getSubSubSubEventApptMatrix', 'EventToApptMatrixController@getSubSubSubEventApptMatrix');
        Route::post('eventToApptMatrix/getApptMatrix', 'EventToApptMatrixController@getApptMatrix');
        Route::post('eventToApptMatrix/getAppt', 'EventToApptMatrixController@getAppt');
        Route::post('eventToApptMatrix/saveEventToApptMatrix', 'EventToApptMatrixController@saveEventToApptMatrix');

        //appt to cm
        Route::get('apptToCm', 'ApptToCmController@index');
        Route::post('apptToCm/getTermEvent', 'ApptToCmController@getTermEvent');
        Route::post('apptToCm/getSubEventCmAppt', 'ApptToCmController@getSubEventCmAppt');
        Route::post('apptToCm/getSubSubEventCmAppt', 'ApptToCmController@getSubSubEventCmAppt');
        Route::post('apptToCm/getSubSubSubEventCmAppt', 'ApptToCmController@getSubSubSubEventCmAppt');
        Route::post('apptToCm/getCmAppt', 'ApptToCmController@getCmAppt');
        Route::post('apptToCm/getAppt', 'ApptToCmController@getAppt');
        Route::post('apptToCm/saveApptToCm', 'ApptToCmController@saveApptToCm');
        Route::post('apptToCm/getAssignedAppt', 'ApptToCmController@getAssignedAppt');

        //Criteria wise wt distribution
        Route::get('criteriaWiseWt', 'CriteriaWiseWtController@index');
        Route::post('criteriaWiseWt/getCriteriaWt', 'CriteriaWiseWtController@getCriteriaWt');
        Route::post('criteriaWiseWt/saveCriteriaWt', 'CriteriaWiseWtController@saveCriteriaWt');

        //Event MKS & WT Distribution
        Route::get('eventMksWt', 'EventMksWtController@index');
        Route::post('eventMksWt/getEventMksWt', 'EventMksWtController@getEventMksWt');
        Route::post('eventMksWt/saveEventMksWt', 'EventMksWtController@saveEventMksWt');

        //Sub Event MKS & WT Distribution
        Route::get('subEventMksWt', 'SubEventMksWtController@index');
        Route::post('subEventMksWt/getEvent', 'SubEventMksWtController@getEvent');
        Route::post('subEventMksWt/getSubEventMksWt', 'SubEventMksWtController@getSubEventMksWt');
        Route::post('subEventMksWt/saveSubEventMksWt', 'SubEventMksWtController@saveSubEventMksWt');

        //Sub Sub Event MKS & WT Distribution
        Route::get('subSubEventMksWt', 'SubSubEventMksWtController@index');
        Route::post('subSubEventMksWt/getEvent', 'SubSubEventMksWtController@getEvent');
        Route::post('subSubEventMksWt/getSubEvent', 'SubSubEventMksWtController@getSubEvent');
        Route::post('subSubEventMksWt/getSubSubEventMksWt', 'SubSubEventMksWtController@getSubSubEventMksWt');
        Route::post('subSubEventMksWt/saveSubSubEventMksWt', 'SubSubEventMksWtController@saveSubSubEventMksWt');

        //Sub Sub Sub Event MKS & WT Distribution
        Route::get('subSubSubEventMksWt', 'SubSubSubEventMksWtController@index');
        Route::post('subSubSubEventMksWt/getEvent', 'SubSubSubEventMksWtController@getEvent');
        Route::post('subSubSubEventMksWt/getSubEvent', 'SubSubSubEventMksWtController@getSubEvent');
        Route::post('subSubSubEventMksWt/getSubSubEvent', 'SubSubSubEventMksWtController@getSubSubEvent');
        Route::post('subSubSubEventMksWt/getSubSubSubEventMksWt', 'SubSubSubEventMksWtController@getSubSubSubEventMksWt');
        Route::post('subSubSubEventMksWt/saveSubSubSubEventMksWt', 'SubSubSubEventMksWtController@saveSubSubSubEventMksWt');

        // CI/Comdt Moderation Marking Limit
        Route::get('ciComdtModerationMarkingLimit', 'CiComdtModerationMarkingLimitController@index');
        Route::post('ciComdtModerationMarkingLimit/getMarkingLimit', 'CiComdtModerationMarkingLimitController@getMarkingLimit');
        Route::post('ciComdtModerationMarkingLimit/saveMarkingLimit', 'CiComdtModerationMarkingLimitController@saveMarkingLimit');

        // DS Obsn Marking Limit
        Route::get('dsObsnMarkingLimit', 'DsObsnMarkingLimitController@index');
        Route::post('dsObsnMarkingLimit/getMarkingLimit', 'DsObsnMarkingLimitController@getMarkingLimit');
        Route::post('dsObsnMarkingLimit/saveMarkingLimit', 'DsObsnMarkingLimitController@saveMarkingLimit');
        // CI/Comdt Obsn Marking Limit
        Route::get('ciComdtObsnMarkingLimit', 'CiComdtObsnMarkingLimitController@index');
        Route::post('ciComdtObsnMarkingLimit/saveMarkingLimit', 'CiComdtObsnMarkingLimitController@saveMarkingLimit');

        // CI/Comdt Moderation Marking Limit
        Route::get('maProcess', 'MaProcessController@index');
        Route::post('maProcess/saveProcess', 'MaProcessController@saveProcess');


        //IP Blocker
        Route::get('ipBlocker', 'IpBlockerController@index');
        Route::post('ipBlocker/saveIP', 'IpBlockerController@saveIP')->name('ipBlocker.saveIP');
        //IP Configurable
        Route::post('ipBlocker/configure', 'IpBlockerController@configure')->name('ipBlocker.configure');

        Route::post('termToEvent/deleteTermToEvent', 'TermToEventController@deleteTermToEvent');

        Route::post('termToSubEvent/deleteTermToSubEvent', 'TermToSubEventController@deleteTermToSubEvent');

        Route::post('termToSubSubEvent/deleteTermToSubSubEvent', 'TermToSubSubEventController@deleteTermToSubSubEvent');

        Route::post('termToSubSubSubEvent/deleteTermToSubSubSubEvent', 'TermToSubSubSubEventController@deleteTermToSubSubSubEvent');

        Route::post('criteriaWiseWt/deleteCriteriaWt', 'CriteriaWiseWtController@deleteCriteriaWt');

        Route::post('eventMksWt/deleteEventMksWt', 'EventMksWtController@deleteEventMksWt');

        Route::post('subEventMksWt/deleteSubEventMksWt', 'SubEventMksWtController@deleteSubEventMksWt');

        Route::post('subSubEventMksWt/deleteSubSubEventMksWt', 'SubSubEventMksWtController@deleteSubSubEventMksWt');

        Route::post('subSubSubEventMksWt/deleteSubSubSubEventMksWt', 'SubSubSubEventMksWtController@deleteSubSubSubEventMksWt');
    });


    // Start:: DS Access
    Route::group(['middleware' => 'ds'], function() {
        //lesson
        Route::post('lesson/filter/', 'LessonController@filter');
        Route::get('lesson', 'LessonController@index');
        Route::get('lesson/create', 'LessonController@create');
        Route::post('lesson', 'LessonController@store');
        Route::get('lesson/{id}/edit', 'LessonController@edit');
        Route::patch('lesson/{id}', 'LessonController@update')->name('lesson.update');
        Route::delete('lesson/{id}', 'LessonController@destroy')->name('lesson.destroy');
        Route::get('lesson/{id}/manageLesson', 'LessonController@manageLesson');
        Route::post('lesson/saveObjective', 'LessonController@saveObjective')->name('lesson.saveObjective');
        Route::post('lesson/saveConsideration', 'LessonController@saveConsideration')->name('lesson.saveConsideration');
        Route::post('lesson/saveGrading', 'LessonController@saveGrading')->name('lesson.saveGrading');
        Route::post('lesson/saveCmnt', 'LessonController@saveCmnt')->name('lesson.saveCmnt');
        Route::post('lesson/showProfileCompitionStatus', 'LessonController@showProfileComplitionStatus')->name('lesson.showProfileComplitionStatus');

        //objective
        Route::post('objective/filter/', 'ObjectiveController@filter');
        Route::get('objective', 'ObjectiveController@index');
        Route::get('objective/create', 'ObjectiveController@create');
        Route::post('objective', 'ObjectiveController@store');
        Route::get('objective/{id}/edit', 'ObjectiveController@edit');
        Route::patch('objective/{id}', 'ObjectiveController@update')->name('objective.update');
        Route::delete('objective/{id}', 'ObjectiveController@destroy')->name('objective.destroy');

        // Subject To Lesson
        Route::get('subjectToLesson', 'SubjectToLessonController@index');
        Route::post('subjectToLesson/getLessonList', 'SubjectToLessonController@getLessonList')->name('subjectToLesson.getLessonList');
        Route::post('subjectToLesson/saveLesson', 'SubjectToLessonController@saveLesson')->name('subjectToLesson.saveLesson');
        Route::post('subjectToLesson/getAssignedLesson', 'SubjectToLessonController@getAssignedLesson')->name('subjectToLesson.getAssignedLesson');


        // gs to lesson
        Route::get('gsToLesson', 'GsToLessonController@index');
        Route::post('gsToLesson/getLesson', 'GsToLessonController@getLesson');
        Route::post('gsToLesson/saveGsToLesson', 'GsToLessonController@saveGsToLesson');
        Route::post('gsToLesson/getAssignedLesson', 'GsToLessonController@getAssignedLesson');

        // DS Eval of GS
        Route::get('dsEvalOfGs', 'DsEvalOfGsController@index');
        Route::post('dsEvalOfGs/getSubject', 'DsEvalOfGsController@getSubject');
        Route::post('dsEvalOfGs/getLesson', 'DsEvalOfGsController@getLesson');
        Route::post('dsEvalOfGs/getGenerateButton', 'DsEvalOfGsController@getGenerateButton');
        Route::post('dsEvalOfGs/filter', 'DsEvalOfGsController@filter');
        Route::post('dsEvalOfGs/storeGrading', 'DsEvalOfGsController@storeGrading');
        Route::post('dsEvalOfGs/getRequestForUnlockModal', 'DsEvalOfGsController@getRequestForUnlockModal');
        Route::post('dsEvalOfGs/saveRequestForUnlock', 'DsEvalOfGsController@saveRequestForUnlock');

        // Event Assessment
        Route::get('eventAssessmentMarking', 'EventAssessmentMarkingController@index');
        Route::post('eventAssessmentMarking/getTermEvent', 'EventAssessmentMarkingController@getTermEvent');
        Route::post('eventAssessmentMarking/getSubEvent', 'EventAssessmentMarkingController@getSubEvent');
        Route::post('eventAssessmentMarking/getSubSubEvent', 'EventAssessmentMarkingController@getSubSubEvent');
        Route::post('eventAssessmentMarking/getSubSubSubEvent', 'EventAssessmentMarkingController@getSubSubSubEvent');
        Route::post('eventAssessmentMarking/showMarkingCmList', 'EventAssessmentMarkingController@showMarkingCmList');
        Route::post('eventAssessmentMarking/saveEventAssessmentMarking', 'EventAssessmentMarkingController@saveEventAssessmentMarking');
        Route::post('eventAssessmentMarking/saveRequestForUnlock', 'EventAssessmentMarkingController@saveRequestForUnlock');
        Route::post('eventAssessmentMarking/getRequestForUnlockModal', 'EventAssessmentMarkingController@getRequestForUnlockModal');
        Route::post('eventAssessmentMarking/deleteEventAssessmentMarking', 'EventAssessmentMarkingController@deleteEventAssessmentMarking');

        //DS Rmks on CM
        Route::post('dsRemarks/filter', 'DsRemarksController@filter');
        Route::get('dsRemarks', 'DsRemarksController@index');
        Route::get('dsRemarks/create', 'DsRemarksController@create');
        Route::get('dsRemarks/{id}/edit', 'DsRemarksController@edit');
        Route::post('dsRemarks/update', 'DsRemarksController@update');
        Route::post('dsRemarks/getEventCmDateRmks', 'DsRemarksController@getEventCmDateRmks');
        Route::post('dsRemarks/preview', 'DsRemarksController@preview');
        Route::post('dsRemarks/saveRmks', 'DsRemarksController@saveRmks');
        Route::delete('dsRemarks/{id}', 'DsRemarksController@destroy');

        //DS Obsn Marking
        Route::get('dsObsnMarking', 'DsObsnMarkingController@index');
        Route::post('dsObsnMarking/filter', 'DsObsnMarkingController@filter');
        Route::post('dsObsnMarking/showCmMarkingList', 'DsObsnMarkingController@showCmMarkingList');
        Route::post('dsObsnMarking/saveObsnMarking', 'DsObsnMarkingController@saveObsnMarking');
        Route::post('dsObsnMarking/getRequestForUnlockModal', 'DsObsnMarkingController@getRequestForUnlockModal');
        Route::post('dsObsnMarking/saveRequestForUnlock', 'DsObsnMarkingController@saveRequestForUnlock');
        Route::post('dsObsnMarking/requestCourseSatatusSummary', 'DsObsnMarkingController@requestCourseSatatusSummary');
        Route::post('dsObsnMarking/getDsMarkingSummary', 'DsObsnMarkingController@getDsMarkingSummary');
        Route::post('dsObsnMarking/clearMarking', 'DsObsnMarkingController@clearMarking');

        // generate course report
        Route::get('crGeneration', 'CrGenerationController@index');
        Route::post('crGeneration/filter', 'CrGenerationController@filter');
        Route::post('crGeneration/getCourse', 'CrGenerationController@getCourse');
        Route::post('crGeneration/getCm', 'CrGenerationController@getCm');
        Route::post('crGeneration/getSentence', 'CrGenerationController@getSentence');
        Route::post('crGeneration/setSentence', 'CrGenerationController@setSentence');
        Route::post('crGeneration/saveSentences', 'CrGenerationController@saveSentences');
        Route::post('crGeneration/generateDoc', 'CrGenerationController@generateDoc');
        Route::post('crGeneration/getUploadModifiedDoc', 'CrGenerationController@getUploadModifiedDoc');
        Route::post('crGeneration/setUploadModifiedDoc', 'CrGenerationController@setUploadModifiedDoc');
    });
    // End:: DS Access
    // Start:: CI Access
    Route::group(['middleware' => 'ci'], function() {
        //deligate CI account to DS
        Route::get('deligateCiAcctToDs', 'DeligateCiAcctToDsController@index');
        Route::post('deligateCiAcctToDs/getDsList', 'DeligateCiAcctToDsController@getDsList');
        Route::post('deligateCiAcctToDs/getDsInfo', 'DeligateCiAcctToDsController@getDsInfo');
        Route::post('deligateCiAcctToDs/setDeligation', 'DeligateCiAcctToDsController@setDeligation');
        Route::post('deligateCiAcctToDs/cancelDeligation', 'DeligateCiAcctToDsController@cancelDeligation');

        //Deligate reports to ds
        Route::get('deligateReportsToDs', 'DeligateReportsToDsController@index');
        Route::post('deligateReportsToDs/setDeligation', 'DeligateReportsToDsController@setDeligation');
        Route::post('deligateReportsToDs/cancelDeligation', 'DeligateReportsToDsController@cancelDeligation');
    });
    // End:: CI Access
    // Start:: deligated Ds,CI Access
    Route::group(['middleware' => 'deligatedDsCi'], function() {
        //CI Moderation Marking
        Route::get('ciModerationMarking', 'CiModerationMarkingController@index');
        Route::post('ciModerationMarking/getTermEvent', 'CiModerationMarkingController@getTermEvent');
        Route::post('ciModerationMarking/getSubEvent', 'CiModerationMarkingController@getSubEvent');
        Route::post('ciModerationMarking/getSubSubEvent', 'CiModerationMarkingController@getSubSubEvent');
        Route::post('ciModerationMarking/getSubSubSubEvent', 'CiModerationMarkingController@getSubSubSubEvent');
        Route::post('ciModerationMarking/showMarkingCmList', 'CiModerationMarkingController@showMarkingCmList');
        Route::post('ciModerationMarking/saveCiModerationMarking', 'CiModerationMarkingController@saveCiModerationMarking');
        Route::post('ciModerationMarking/saveRequestForUnlock', 'CiModerationMarkingController@saveRequestForUnlock');
        Route::post('ciModerationMarking/getRequestForUnlockModal', 'CiModerationMarkingController@getRequestForUnlockModal');
        Route::post('ciModerationMarking/getDsMarkingSummary', 'CiModerationMarkingController@getDsMarkingSummary');
        Route::post('ciModerationMarking/clearMarking', 'CiModerationMarkingController@clearMarking');



        // CI Obsn Marking
        Route::get('ciObsnMarking', 'CiObsnMarkingController@index');
        Route::post('ciObsnMarking/showCmMarkingList', 'CiObsnMarkingController@showCmMarkingList');
        Route::post('ciObsnMarking/saveObsnMarking', 'CiObsnMarkingController@saveObsnMarking');
        Route::post('ciObsnMarking/getRequestForUnlockModal', 'CiObsnMarkingController@getRequestForUnlockModal');
        Route::post('ciObsnMarking/saveRequestForUnlock', 'CiObsnMarkingController@saveRequestForUnlock');
        Route::post('ciObsnMarking/requestCourseSatatusSummary', 'CiObsnMarkingController@requestCourseSatatusSummary');
        Route::post('ciObsnMarking/getDsMarkingSummary', 'CiObsnMarkingController@getDsMarkingSummary');
        Route::post('ciObsnMarking/filter', 'CiObsnMarkingController@filter');
        Route::post('ciObsnMarking/clearMarking', 'CiObsnMarkingController@clearMarking');



        //Unlock Event Assessment
        Route::get('unlockEventAssessment', 'UnlockEventAssessmentController@index');
        Route::post('unlockEventAssessment/unlockRequest', 'UnlockEventAssessmentController@unlock');
        Route::post('unlockEventAssessment/denyRequest', 'UnlockEventAssessmentController@deny');
        Route::post('unlockEventAssessment/filter', 'UnlockEventAssessmentController@filter');
    });
    // End:: deligated Ds,CI Access
    // Start:: deligated Ds,CI,COMDT Access
    Route::group(['middleware' => 'deligatedDsCiComdt'], function() {
        //Comdt Moderation Marking
//        Route::get('comdtModerationMarking', 'ComdtModerationMarkingController@index');
//        Route::post('comdtModerationMarking/getTermEvent', 'ComdtModerationMarkingController@getTermEvent');
//        Route::post('comdtModerationMarking/getSubEvent', 'ComdtModerationMarkingController@getSubEvent');
//        Route::post('comdtModerationMarking/getSubSubEvent', 'ComdtModerationMarkingController@getSubSubEvent');
//        Route::post('comdtModerationMarking/getSubSubSubEvent', 'ComdtModerationMarkingController@getSubSubSubEvent');
//        Route::post('comdtModerationMarking/showMarkingCmList', 'ComdtModerationMarkingController@showMarkingCmList');
//        Route::post('comdtModerationMarking/saveComdtModerationMarking', 'ComdtModerationMarkingController@saveComdtModerationMarking');
//        Route::post('comdtModerationMarking/saveRequestForUnlock', 'ComdtModerationMarkingController@saveRequestForUnlock');
//        Route::post('comdtModerationMarking/getRequestForUnlockModal', 'ComdtModerationMarkingController@getRequestForUnlockModal');
//        Route::post('comdtModerationMarking/getDsMarkingSummary', 'ComdtModerationMarkingController@getDsMarkingSummary');
        // Comdt Obsn Marking
        Route::get('comdtObsnMarking', 'ComdtObsnMarkingController@index');
        Route::post('comdtObsnMarking/showCmMarkingList', 'ComdtObsnMarkingController@showCmMarkingList');
        Route::post('comdtObsnMarking/saveObsnMarking', 'ComdtObsnMarkingController@saveObsnMarking');
        Route::post('comdtObsnMarking/getRequestForUnlockModal', 'ComdtObsnMarkingController@getRequestForUnlockModal');
        Route::post('comdtObsnMarking/saveRequestForUnlock', 'ComdtObsnMarkingController@saveRequestForUnlock');
        Route::post('comdtObsnMarking/requestCourseSatatusSummary', 'ComdtObsnMarkingController@requestCourseSatatusSummary');
        Route::post('comdtObsnMarking/getDsMarkingSummary', 'ComdtObsnMarkingController@getDsMarkingSummary');
        Route::post('comdtObsnMarking/filter', 'ComdtObsnMarkingController@filter');
        Route::post('comdtObsnMarking/clearMarking', 'ComdtObsnMarkingController@clearMarking');

        //Unlock CI moderation marking
        Route::get('unlockCiModerationMarking', 'UnlockCiModerationMarkingController@index');
        Route::post('unlockCiModerationMarking/unlockRequest', 'UnlockCiModerationMarkingController@unlock');
        Route::post('unlockCiModerationMarking/denyRequest', 'UnlockCiModerationMarkingController@deny');
        Route::post('unlockCiModerationMarking/filter', 'UnlockCiModerationMarkingController@filter');

        //Unlock DS obsn marking
        Route::get('unlockDsObsnMarking', 'UnlockDsObsnMarkingController@index');
        Route::post('unlockDsObsnMarking/unlockRequest', 'UnlockDsObsnMarkingController@unlock');
        Route::post('unlockDsObsnMarking/denyRequest', 'UnlockDsObsnMarkingController@deny');
        Route::post('unlockDsObsnMarking/filter', 'UnlockDsObsnMarkingController@filter');

        //Unlock CI obsn marking
        Route::get('unlockCiObsnMarking', 'UnlockCiObsnMarkingController@index');
        Route::post('unlockCiObsnMarking/unlockRequest', 'UnlockCiObsnMarkingController@unlock');
        Route::post('unlockCiObsnMarking/denyRequest', 'UnlockCiObsnMarkingController@deny');
        Route::post('unlockCiObsnMarking/filter', 'UnlockCiObsnMarkingController@filter');

        //Unlock Comdt moderation marking
//        Route::get('unlockComdtModerationMarking', 'UnlockComdtModerationMarkingController@index');
//        Route::post('unlockComdtModerationMarking/unlockRequest', 'UnlockComdtModerationMarkingController@unlock');
//        Route::post('unlockComdtModerationMarking/denyRequest', 'UnlockComdtModerationMarkingController@deny');
//        Route::post('unlockComdtModerationMarking/filter', 'UnlockComdtModerationMarkingController@filter');
        //Unlock Comdt obsn marking
        Route::get('unlockComdtObsnMarking', 'UnlockComdtObsnMarkingController@index');
        Route::post('unlockComdtObsnMarking/unlockRequest', 'UnlockComdtObsnMarkingController@unlock');
        Route::post('unlockComdtObsnMarking/denyRequest', 'UnlockComdtObsnMarkingController@deny');
        Route::post('unlockComdtObsnMarking/filter', 'UnlockComdtObsnMarkingController@filter');
    });
    // End:: deligated Ds,CI,COMDT Access
    // Start:: Report archive Routes

    Route::group(['middleware' => 'deligatedDsCi'], function() {
        // Nominal Roll Report
//        Route::get('nominalRollReport', 'NominalRollReportController@index');
//        Route::post('nominalRollReport/getCourse', 'NominalRollReportController@getCourse');
//        Route::post('nominalRollReport/getTerm', 'NominalRollReportController@getTerm');
//        Route::post('nominalRollReport/filter', 'NominalRollReportController@filter');
//        Route::get('nominalRollReport/{id}/profile', 'NominalRollReportController@profile');
        // CM Course Report
        Route::get('cmCourseReport', 'CmCourseReportController@index');
        Route::post('cmCourseReport/getCourse', 'CmCourseReportController@getCourse');
        Route::post('cmCourseReport/filter', 'CmCourseReportController@filter');

        //Event List Report
        Route::get('eventListReport', 'EventListReportController@index');
        Route::post('eventListReport/getCourse', 'EventListReportController@getCourse');
        Route::post('eventListReport/getTerm', 'EventListReportController@getTerm');
        Route::post('eventListReport/filter', 'EventListReportController@eventFilter');

        // Cm Wise Event Trend Report
        Route::get('cmWiseEventTrendReport', 'CmWiseEventTrendReportController@index');
        Route::post('cmWiseEventTrendReport/getCourse', 'CmWiseEventTrendReportController@getCourse');
        Route::post('cmWiseEventTrendReport/getTerm', 'CmWiseEventTrendReportController@getTerm');
        Route::post('cmWiseEventTrendReport/getCourseWiseCmEvent', 'CmWiseEventTrendReportController@getCourseWiseCmEvent');
        Route::post('cmWiseEventTrendReport/filter', 'CmWiseEventTrendReportController@filter');

        // Arms & Svc Wise Event Trend Report
        Route::get('armsServiceWiseEventTrendReport', 'ArmsServiceWiseEventTrendReportController@index');
        Route::post('armsServiceWiseEventTrendReport/getCourse', 'ArmsServiceWiseEventTrendReportController@getCourse');
        Route::post('armsServiceWiseEventTrendReport/getTerm', 'ArmsServiceWiseEventTrendReportController@getTerm');
        Route::post('armsServiceWiseEventTrendReport/getCourseWiseArmsServiceEvent', 'ArmsServiceWiseEventTrendReportController@getCourseWiseArmsServiceEvent');
        Route::post('armsServiceWiseEventTrendReport/filter', 'ArmsServiceWiseEventTrendReportController@filter');


        // Wing Wise Event Trend Report
        Route::get('wingWiseEventTrendReport', 'WingWiseEventTrendReportController@index');
        Route::post('wingWiseEventTrendReport/getCourse', 'WingWiseEventTrendReportController@getCourse');
        Route::post('wingWiseEventTrendReport/getTerm', 'WingWiseEventTrendReportController@getTerm');
        Route::post('wingWiseEventTrendReport/getCourseWiseWingEvent', 'WingWiseEventTrendReportController@getCourseWiseWingEvent');
        Route::post('wingWiseEventTrendReport/filter', 'WingWiseEventTrendReportController@filter');

        // Commissioning Course Wise Event Trend Report
        Route::get('commissioningCourseWiseEventTrendReport', 'CommissioningCourseWiseEventTrendReportController@index');
        Route::post('commissioningCourseWiseEventTrendReport/getCourse', 'CommissioningCourseWiseEventTrendReportController@getCourse');
        Route::post('commissioningCourseWiseEventTrendReport/getTerm', 'CommissioningCourseWiseEventTrendReportController@getTerm');
        Route::post('commissioningCourseWiseEventTrendReport/getCourseWiseCommissioningCourseEvent', 'CommissioningCourseWiseEventTrendReportController@getCourseWiseCommissioningCourseEvent');
        Route::post('commissioningCourseWiseEventTrendReport/filter', 'CommissioningCourseWiseEventTrendReportController@filter');

        // CM group Wise Event Trend Report
        Route::get('cmGroupWiseEventTrendReport', 'CmGroupWiseEventTrendReportController@index');
        Route::post('cmGroupWiseEventTrendReport/getCourse', 'CmGroupWiseEventTrendReportController@getCourse');
        Route::post('cmGroupWiseEventTrendReport/getTerm', 'CmGroupWiseEventTrendReportController@getTerm');
        Route::post('cmGroupWiseEventTrendReport/getCourseWiseCmGroupEvent', 'CmGroupWiseEventTrendReportController@getCourseWiseCmGroupEvent');
        Route::post('cmGroupWiseEventTrendReport/filter', 'CmGroupWiseEventTrendReportController@filter');

        // Arms & Svc Wise Performance Trend Report
        Route::get('armsServiceWisePerformanceTrendReport', 'ArmsServiceWisePerformanceTrendReportController@index');
        Route::post('armsServiceWisePerformanceTrendReport/getCourse', 'ArmsServiceWisePerformanceTrendReportController@getCourse');
        Route::post('armsServiceWisePerformanceTrendReport/getCourseWiseArmsService', 'ArmsServiceWisePerformanceTrendReportController@getCourseWiseArmsService');
        Route::post('armsServiceWisePerformanceTrendReport/filter', 'ArmsServiceWisePerformanceTrendReportController@filter');

        // Wing Wise Performance Trend Report
        Route::get('wingWisePerformanceTrendReport', 'WingWisePerformanceTrendReportController@index');
        Route::post('wingWisePerformanceTrendReport/getCourse', 'WingWisePerformanceTrendReportController@getCourse');
        Route::post('wingWisePerformanceTrendReport/getCourseWiseWing', 'WingWisePerformanceTrendReportController@getCourseWiseWing');
        Route::post('wingWisePerformanceTrendReport/filter', 'WingWisePerformanceTrendReportController@filter');


        // Commissioning Course Wise Performance Trend Report
        Route::get('commissioningCourseWisePerformanceTrendReport', 'CommissioningCourseWisePerformanceTrendReportController@index');
        Route::post('commissioningCourseWisePerformanceTrendReport/getCourse', 'CommissioningCourseWisePerformanceTrendReportController@getCourse');
        Route::post('commissioningCourseWisePerformanceTrendReport/getCourseWiseCommissioningCourse', 'CommissioningCourseWisePerformanceTrendReportController@getCourseWiseCommissioningCourse');
        Route::post('commissioningCourseWisePerformanceTrendReport/filter', 'CommissioningCourseWisePerformanceTrendReportController@filter');


        // Overall Performance Trend Report
        Route::get('overallPerformanceTrendReport', 'OverallPerformanceTrendReportController@index');
        Route::post('overallPerformanceTrendReport/getCourse', 'OverallPerformanceTrendReportController@getCourse');
        Route::post('overallPerformanceTrendReport/filter', 'OverallPerformanceTrendReportController@filter');




        //Event Avg Trend Report
        Route::get('eventAvgTrendReport', 'EventAvgTrendReportController@index');
        Route::post('eventAvgTrendReport/getCourse', 'EventAvgTrendReportController@getCourse');
        Route::post('eventAvgTrendReport/getTerm', 'EventAvgTrendReportController@getTerm');
        Route::post('eventAvgTrendReport/getCourseWiseEvent', 'EventAvgTrendReportController@getCourseWiseEvent');
        Route::post('eventAvgTrendReport/filter', 'EventAvgTrendReportController@filter');

// DS Marking Trend Report
        Route::get('dsMarkingTrendReport', 'DsMarkingTrendReportController@index');
        Route::post('dsMarkingTrendReport/getCourse', 'DsMarkingTrendReportController@getCourse');
        Route::post('dsMarkingTrendReport/getTerm', 'DsMarkingTrendReportController@getTerm');
        Route::post('dsMarkingTrendReport/getCourseWiseDsEvent', 'DsMarkingTrendReportController@getCourseWiseDsEvent');
        Route::post('dsMarkingTrendReport/getCourseWiseDs', 'DsMarkingTrendReportController@getCourseWiseDs');
        Route::post('dsMarkingTrendReport/filter', 'DsMarkingTrendReportController@filter');
        Route::post('dsMarkingTrendReport/getCourseWiseDsSubEvent', 'DsMarkingTrendReportController@getCourseWiseDsSubEvent');
        Route::post('dsMarkingTrendReport/getCourseWiseDsSubSubEvent', 'DsMarkingTrendReportController@getCourseWiseDsSubSubEvent');
        Route::post('dsMarkingTrendReport/getCourseWiseDsSubSubSubEvent', 'DsMarkingTrendReportController@getCourseWiseDsSubSubSubEvent');


        // Mutual Assessment (Detailed) Report
        Route::get('mutualAssessmentDetailedReport', 'MutualAssessmentDetailedReportController@index');
        Route::post('mutualAssessmentDetailedReport/getCourse', 'MutualAssessmentDetailedReportController@getCourse');
        Route::post('mutualAssessmentDetailedReport/getTerm', 'MutualAssessmentDetailedReportController@getTerm');
        Route::post('mutualAssessmentDetailedReport/getMaEvent', 'MutualAssessmentDetailedReportController@getMaEvent');
        Route::post('mutualAssessmentDetailedReport/getsubSyn', 'MutualAssessmentDetailedReportController@getsubSyn');
        Route::post('mutualAssessmentDetailedReport/getSynOrGp', 'MutualAssessmentDetailedReportController@getSynOrGp');
        Route::post('mutualAssessmentDetailedReport/getSubEvent', 'MutualAssessmentDetailedReportController@getSubEvent');
        Route::post('mutualAssessmentDetailedReport/getSubSubEvent', 'MutualAssessmentDetailedReportController@getSubSubEvent');
        Route::post('mutualAssessmentDetailedReport/getSubSubSubEvent', 'MutualAssessmentDetailedReportController@getSubSubSubEvent');
        Route::post('mutualAssessmentDetailedReport/getEventGroup', 'MutualAssessmentDetailedReportController@getEventGroup');
        Route::post('mutualAssessmentDetailedReport/filter', 'MutualAssessmentDetailedReportController@filter');

        // Mutual Assessment (Summary) Report
        Route::get('mutualAssessmentSummaryReport', 'MutualAssessmentSummaryReportController@index');
        Route::post('mutualAssessmentSummaryReport/getCourse', 'MutualAssessmentSummaryReportController@getCourse');
        Route::post('mutualAssessmentSummaryReport/getTerm', 'MutualAssessmentSummaryReportController@getTerm');
        Route::post('mutualAssessmentSummaryReport/getMaEvent', 'MutualAssessmentSummaryReportController@getMaEvent');
        Route::post('mutualAssessmentSummaryReport/getsubSyn', 'MutualAssessmentSummaryReportController@getsubSyn');
        Route::post('mutualAssessmentSummaryReport/getSynOrGp', 'MutualAssessmentSummaryReportController@getSynOrGp');
        Route::post('mutualAssessmentSummaryReport/getSubEvent', 'MutualAssessmentSummaryReportController@getSubEvent');
        Route::post('mutualAssessmentSummaryReport/getSubSubEvent', 'MutualAssessmentSummaryReportController@getSubSubEvent');
        Route::post('mutualAssessmentSummaryReport/getSubSubSubEvent', 'MutualAssessmentSummaryReportController@getSubSubSubEvent');
        Route::post('mutualAssessmentSummaryReport/getEventGroup', 'MutualAssessmentSummaryReportController@getEventGroup');
        Route::post('mutualAssessmentSummaryReport/filter', 'MutualAssessmentSummaryReportController@filter');


        // Marking Group Summary Report
        Route::get('markingGroupSummaryReport', 'MarkingGroupSummaryReportController@index');
        Route::post('markingGroupSummaryReport/getCourse', 'MarkingGroupSummaryReportController@getCourse');
        Route::post('markingGroupSummaryReport/getTerm', 'MarkingGroupSummaryReportController@getTerm');
        Route::post('markingGroupSummaryReport/getEvent', 'MarkingGroupSummaryReportController@getEvent');
        Route::post('markingGroupSummaryReport/getSubEventReport', 'MarkingGroupSummaryReportController@getSubEventReport');
        Route::post('markingGroupSummaryReport/getSubSubEventReport', 'MarkingGroupSummaryReportController@getSubSubEventReport');
        Route::post('markingGroupSummaryReport/getSubSubSubEventReport', 'MarkingGroupSummaryReportController@getSubSubSubEventReport');
        Route::post('markingGroupSummaryReport/getsubSyn', 'MarkingGroupSummaryReportController@getsubSyn');
        Route::post('markingGroupSummaryReport/filter', 'MarkingGroupSummaryReportController@filter');

        // DS Renarks Report
        Route::get('dsRemarksReport', 'DsRemarksReportController@index');
        Route::post('dsRemarksReport/getCourse', 'DsRemarksReportController@getCourse');
        Route::post('dsRemarksReport/getTerm', 'DsRemarksReportController@getTerm');
        Route::post('dsRemarksReport/getEvent', 'DsRemarksReportController@getEvent');
        Route::post('dsRemarksReport/getsubSyn', 'DsRemarksReportController@getsubSyn');
        Route::post('dsRemarksReport/filter', 'DsRemarksReportController@filter');

        // event result Report
        Route::get('eventResultReport', 'EventResultReportController@index');
        Route::post('eventResultReport/getCourse', 'EventResultReportController@getCourse');
        Route::post('eventResultReport/getTerm', 'EventResultReportController@getTerm');
        Route::post('eventResultReport/getEvent', 'EventResultReportController@getEvent');
        Route::post('eventResultReport/getSubEventReport', 'EventResultReportController@getSubEventReport');
        Route::post('eventResultReport/getSubSubEventReport', 'EventResultReportController@getSubSubEventReport');
        Route::post('eventResultReport/getSubSubSubEventReport', 'EventResultReportController@getSubSubSubEventReport');
        Route::post('eventResultReport/getsubSyn', 'EventResultReportController@getsubSyn');
        Route::post('eventResultReport/filter', 'EventResultReportController@filter');

        // event result combined Report
        Route::get('eventResultCombinedReport', 'EventResultCombinedReportController@index');
        Route::post('eventResultCombinedReport/getCourse', 'EventResultCombinedReportController@getCourse');
        Route::post('eventResultCombinedReport/getEvent', 'EventResultCombinedReportController@getEvent');
        Route::post('eventResultCombinedReport/filter', 'EventResultCombinedReportController@filter');



        // Performace Analysis
        Route::get('performanceAnalysisReport', 'PerformanceAnalysisReportController@index');
        Route::post('performanceAnalysisReport/getCourse', 'PerformanceAnalysisReportController@getCourse');
        Route::post('performanceAnalysisReport/getTerm', 'PerformanceAnalysisReportController@getTerm');
        Route::post('performanceAnalysisReport/getCm', 'PerformanceAnalysisReportController@getCm');
        Route::post('performanceAnalysisReport/filter', 'PerformanceAnalysisReportController@filter');



        // term Report
        Route::get('termResultReport', 'TermResultReportController@index');
        Route::post('termResultReport/getCourse', 'TermResultReportController@getCourse');
        Route::post('termResultReport/getTerm', 'TermResultReportController@getTerm');
        Route::post('termResultReport/filter', 'TermResultReportController@filter');

        // DS obsn Report
        Route::get('dsObsnReport', 'DsObsnReportController@index');
        Route::post('dsObsnReport/getCourse', 'DsObsnReportController@getCourse');
        Route::post('dsObsnReport/getTerm', 'DsObsnReportController@getTerm');
        Route::post('dsObsnReport/filter', 'DsObsnReportController@filter');



        // course Progressive Result Report
        Route::get('courseProgressiveResultReport', 'CourseProgressiveResultReportController@index');
        Route::post('courseProgressiveResultReport/getCourse', 'CourseProgressiveResultReportController@getCourse');
        Route::post('courseProgressiveResultReport/getTerm', 'CourseProgressiveResultReportController@getTerm');
        Route::post('courseProgressiveResultReport/filter', 'CourseProgressiveResultReportController@filter');

        // course Result Report
        Route::get('courseResultReport', 'CourseResultReportController@index');
        Route::post('courseResultReport/getCourse', 'CourseResultReportController@getCourse');
        Route::post('courseResultReport/filter', 'CourseResultReportController@filter');

        // Individual Profile Report
        Route::get('individualProfileReport', 'IndividualProfileReportController@index');
        Route::post('individualProfileReport/getCourse', 'IndividualProfileReportController@getCourse');
        Route::post('individualProfileReport/getCm', 'IndividualProfileReportController@getCm');
        Route::post('individualProfileReport/filter', 'IndividualProfileReportController@filter');
        Route::get('individualProfileReport/{id}/profile', 'IndividualProfileReportController@profile');
    });
    // End:: Report archive Routes
    //
    //
    //
    //
    // Start:: current Report Routes
    // Nominal Roll Report
//    Route::get('nominalRollReportCrnt', 'NominalRollReportCrntController@index');
//    Route::post('nominalRollReportCrnt/getCourse', 'NominalRollReportCrntController@getCourse');
//    Route::post('nominalRollReportCrnt/getTerm', 'NominalRollReportCrntController@getTerm');
//    Route::post('nominalRollReportCrnt/filter', 'NominalRollReportCrntController@filter');
//    Route::get('nominalRollReportCrnt/{id}/profile', 'NominalRollReportCrntController@profile');
    // CM Course Report current
    Route::get('cmCourseReportCrnt', 'CmCourseReportCrntController@index');
    Route::post('cmCourseReportCrnt/getCourse', 'CmCourseReportCrntController@getCourse');
    Route::post('cmCourseReportCrnt/filter', 'CmCourseReportCrntController@filter');


    //Event List ReportCrnt
    Route::get('eventListReportCrnt', 'EventListReportCrntController@index');
    Route::post('eventListReportCrnt/getCourse', 'EventListReportCrntController@getCourse');
    Route::post('eventListReportCrnt/getTerm', 'EventListReportCrntController@getTerm');
    Route::post('eventListReportCrnt/filter', 'EventListReportCrntController@eventFilter');

    //CI/DS Profile
    Route::get('ciDsProfileReportCrnt', 'CiDsProfileReportCrntController@index');
    Route::get('ciDsProfileReportCrnt/{id}/profile', 'CiDsProfileReportCrntController@profile');

    Route::group(['middleware' => 'dsCi'], function() {

        // DS Renarks ReportCrnt
        Route::get('dsRemarksReportCrnt', 'DsRemarksReportCrntController@index');
        Route::post('dsRemarksReportCrnt/getCourse', 'DsRemarksReportCrntController@getCourse');
        Route::post('dsRemarksReportCrnt/getTerm', 'DsRemarksReportCrntController@getTerm');
        Route::post('dsRemarksReportCrnt/getEvent', 'DsRemarksReportCrntController@getEvent');
        Route::post('dsRemarksReportCrnt/getsubSyn', 'DsRemarksReportCrntController@getsubSyn');
        Route::post('dsRemarksReportCrnt/filter', 'DsRemarksReportCrntController@filter');

        // event result ReportCrnt
        Route::get('eventResultReportCrnt', 'EventResultReportCrntController@index');
        Route::post('eventResultReportCrnt/getCourse', 'EventResultReportCrntController@getCourse');
        Route::post('eventResultReportCrnt/getTerm', 'EventResultReportCrntController@getTerm');
        Route::post('eventResultReportCrnt/getEvent', 'EventResultReportCrntController@getEvent');
        Route::post('eventResultReportCrnt/getSubEventReportCrnt', 'EventResultReportCrntController@getSubEventReportCrnt');
        Route::post('eventResultReportCrnt/getSubSubEventReportCrnt', 'EventResultReportCrntController@getSubSubEventReportCrnt');
        Route::post('eventResultReportCrnt/getSubSubSubEventReportCrnt', 'EventResultReportCrntController@getSubSubSubEventReportCrnt');
        Route::post('eventResultReportCrnt/getsubSyn', 'EventResultReportCrntController@getsubSyn');
        Route::post('eventResultReportCrnt/filter', 'EventResultReportCrntController@filter');

        // Individual Profile ReportCrnt
        Route::get('individualProfileReportCrnt', 'IndividualProfileReportCrntController@index');
        Route::post('individualProfileReportCrnt/getCourse', 'IndividualProfileReportCrntController@getCourse');
        Route::post('individualProfileReportCrnt/getCm', 'IndividualProfileReportCrntController@getCm');
        Route::post('individualProfileReportCrnt/filter', 'IndividualProfileReportCrntController@filter');
        Route::get('individualProfileReportCrnt/{id}/profile', 'IndividualProfileReportCrntController@profile');
        // cmIndividual Profile ReportCrnt
        Route::get('cmProfileReportCrnt', 'CmProfileReportCrntController@index');
        Route::post('cmProfileReportCrnt/getCourse', 'CmProfileReportCrntController@getCourse');
        Route::post('cmProfileReportCrnt/getCm', 'CmProfileReportCrntController@getCm');
        Route::post('cmProfileReportCrnt/filter', 'CmProfileReportCrntController@filter');
        Route::get('cmProfileReportCrnt/{id}/profile', 'CmProfileReportCrntController@profile');
    });
    Route::group(['middleware' => 'deligatedDsCiSuperAdmin'], function() {
        //clear marking
        Route::get('clearMarking', 'ClearMarkingController@index');
        Route::post('clearMarking/clear', 'ClearMarkingController@doClear');
        Route::post('clearMarking/requestCourseSatatusSummary', 'ClearMarkingController@requestCourseSatatusSummary');
        Route::post('clearMarking/getDsMarkingSummary', 'ClearMarkingController@getDsMarkingSummary');
        Route::post('clearMarking/getDsEvent', 'ClearMarkingController@getDsEvent');
        Route::post('clearMarking/getEvent', 'ClearMarkingController@getDsEvent');
        Route::post('clearMarking/getSubEvent', 'ClearMarkingController@getSubEvent');
        Route::post('clearMarking/getSubSubEvent', 'ClearMarkingController@getSubSubEvent');
        Route::post('clearMarking/getSubSubSubEvent', 'ClearMarkingController@getSubSubSubEvent');
        Route::post('clearMarking/getDs', 'ClearMarkingController@getDsEvent');

        // event marking state ReportCrnt
        Route::get('eventMarkingStateReportCrnt', 'EventMarkingStateReportCrntController@index');
        Route::post('eventMarkingStateReportCrnt/getCourse', 'EventMarkingStateReportCrntController@getCourse');
        Route::post('eventMarkingStateReportCrnt/filter', 'EventMarkingStateReportCrntController@filter');

        //CI/DS Profile
        Route::get('ciDsProfileReport', 'CiDsProfileReportController@index');
        Route::get('ciDsProfileReport/{id}/profile', 'CiDsProfileReportController@profile');



        // Mutual Assessment (Detailed) ReportCrnt
        Route::get('mutualAssessmentDetailedReportCrnt', 'MutualAssessmentDetailedReportCrntController@index');
        Route::post('mutualAssessmentDetailedReportCrnt/getCourse', 'MutualAssessmentDetailedReportCrntController@getCourse');
        Route::post('mutualAssessmentDetailedReportCrnt/getTerm', 'MutualAssessmentDetailedReportCrntController@getTerm');
        Route::post('mutualAssessmentDetailedReportCrnt/getMaEvent', 'MutualAssessmentDetailedReportCrntController@getMaEvent');
        Route::post('mutualAssessmentDetailedReportCrnt/getsubSyn', 'MutualAssessmentDetailedReportCrntController@getsubSyn');
        Route::post('mutualAssessmentDetailedReportCrnt/getSynOrGp', 'MutualAssessmentDetailedReportCrntController@getSynOrGp');
        Route::post('mutualAssessmentDetailedReportCrnt/getSubEvent', 'MutualAssessmentDetailedReportCrntController@getSubEvent');
        Route::post('mutualAssessmentDetailedReportCrnt/getSubSubEvent', 'MutualAssessmentDetailedReportCrntController@getSubSubEvent');
        Route::post('mutualAssessmentDetailedReportCrnt/getSubSubSubEvent', 'MutualAssessmentDetailedReportCrntController@getSubSubSubEvent');
        Route::post('mutualAssessmentDetailedReportCrnt/getEventGroup', 'MutualAssessmentDetailedReportCrntController@getEventGroup');
        Route::post('mutualAssessmentDetailedReportCrnt/filter', 'MutualAssessmentDetailedReportCrntController@filter');


        //appt to Cm Report
        Route::get('apptToCmReportCrnt', 'ApptToCmReportCrntController@index');
        Route::post('apptToCmReportCrnt/getCourse', 'ApptToCmReportCrntController@getCourse');
        Route::post('apptToCmReportCrnt/getTerm', 'ApptToCmReportCrntController@getTerm');
        Route::post('apptToCmReportCrnt/getEvent', 'ApptToCmReportCrntController@getEvent');
        Route::post('apptToCmReportCrnt/getSubEventReportCrnt', 'ApptToCmReportCrntController@getSubEventReportCrnt');
        Route::post('apptToCmReportCrnt/getSubSubEventReportCrnt', 'ApptToCmReportCrntController@getSubSubEventReportCrnt');
        Route::post('apptToCmReportCrnt/getSubSubSubEventReportCrnt', 'ApptToCmReportCrntController@getSubSubSubEventReportCrnt');
        Route::post('apptToCmReportCrnt/getsubSyn', 'ApptToCmReportCrntController@getsubSyn');
        Route::post('apptToCmReportCrnt/filter', 'ApptToCmReportCrntController@filter');
    });
    Route::group(['middleware' => 'dsCiSuperAdmin'], function() {
        //content
        Route::post('content/filter/', 'ContentController@filter');
        Route::get('content', 'ContentController@index')->name('content.index');
        Route::get('content/create', 'ContentController@create')->name('content.create');
        Route::post('content/store', 'ContentController@store')->name('content.store');
        Route::get('content/{id}/edit', 'ContentController@edit')->name('content.edit');
        Route::POST('content', 'ContentController@update')->name('content.update');
        Route::delete('content/{id}', 'ContentController@destroy')->name('content.destroy');
        Route::post('content/addContentRow', 'ContentController@addContentRow')->name('content.addContentRow');
        Route::post('content/downloadFile', 'ContentController@downloadFile')->name('content.downloadFile');


        // Marking Group Summary ReportCrnt
        Route::get('markingGroupSummaryReportCrnt', 'MarkingGroupSummaryReportCrntController@index');
        Route::post('markingGroupSummaryReportCrnt/getCourse', 'MarkingGroupSummaryReportCrntController@getCourse');
        Route::post('markingGroupSummaryReportCrnt/getTerm', 'MarkingGroupSummaryReportCrntController@getTerm');
        Route::post('markingGroupSummaryReportCrnt/getEvent', 'MarkingGroupSummaryReportCrntController@getEvent');
        Route::post('markingGroupSummaryReportCrnt/getSubEventReportCrnt', 'MarkingGroupSummaryReportCrntController@getSubEventReportCrnt');
        Route::post('markingGroupSummaryReportCrnt/getSubSubEventReportCrnt', 'MarkingGroupSummaryReportCrntController@getSubSubEventReportCrnt');
        Route::post('markingGroupSummaryReportCrnt/getSubSubSubEventReportCrnt', 'MarkingGroupSummaryReportCrntController@getSubSubSubEventReportCrnt');
        Route::post('markingGroupSummaryReportCrnt/getsubSyn', 'MarkingGroupSummaryReportCrntController@getsubSyn');
        Route::post('markingGroupSummaryReportCrnt/filter', 'MarkingGroupSummaryReportCrntController@filter');

        Route::get('mksSubmissionState', 'MksSubmissionStateController@index');
        Route::post('mksSubmissionState/getDsMarkingSummary', 'MksSubmissionStateController@getDsMarkingSummary');

        // Mutual Assessment (Summary) ReportCrnt
        Route::get('mutualAssessmentSummaryReportCrnt', 'MutualAssessmentSummaryReportCrntController@index');
        Route::post('mutualAssessmentSummaryReportCrnt/getCourse', 'MutualAssessmentSummaryReportCrntController@getCourse');
        Route::post('mutualAssessmentSummaryReportCrnt/getTerm', 'MutualAssessmentSummaryReportCrntController@getTerm');
        Route::post('mutualAssessmentSummaryReportCrnt/getMaEvent', 'MutualAssessmentSummaryReportCrntController@getMaEvent');
        Route::post('mutualAssessmentSummaryReportCrnt/getsubSyn', 'MutualAssessmentSummaryReportCrntController@getsubSyn');
        Route::post('mutualAssessmentSummaryReportCrnt/getSynOrGp', 'MutualAssessmentSummaryReportCrntController@getSynOrGp');
        Route::post('mutualAssessmentSummaryReportCrnt/getSubEvent', 'MutualAssessmentSummaryReportCrntController@getSubEvent');
        Route::post('mutualAssessmentSummaryReportCrnt/getSubSubEvent', 'MutualAssessmentSummaryReportCrntController@getSubSubEvent');
        Route::post('mutualAssessmentSummaryReportCrnt/getSubSubSubEvent', 'MutualAssessmentSummaryReportCrntController@getSubSubSubEvent');
        Route::post('mutualAssessmentSummaryReportCrnt/getEventGroup', 'MutualAssessmentSummaryReportCrntController@getEventGroup');
        Route::post('mutualAssessmentSummaryReportCrnt/filter', 'MutualAssessmentSummaryReportCrntController@filter');
    });



    Route::group(['middleware' => 'deligatedDsCi'], function() {
        // DS obsn ReportCrnt
        Route::get('dsObsnReportCrnt', 'DsObsnReportCrntController@index');
        Route::post('dsObsnReportCrnt/getCourse', 'DsObsnReportCrntController@getCourse');
        Route::post('dsObsnReportCrnt/getTerm', 'DsObsnReportCrntController@getTerm');
        Route::post('dsObsnReportCrnt/filter', 'DsObsnReportCrntController@filter');

        // Cm Wise Event Trend ReportCrnt
        Route::get('cmWiseEventTrendReportCrnt', 'CmWiseEventTrendReportCrntController@index');
        Route::post('cmWiseEventTrendReportCrnt/getCourse', 'CmWiseEventTrendReportCrntController@getCourse');
        Route::post('cmWiseEventTrendReportCrnt/getTerm', 'CmWiseEventTrendReportCrntController@getTerm');
        Route::post('cmWiseEventTrendReportCrnt/getCm', 'CmWiseEventTrendReportCrntController@getCm');
        Route::post('cmWiseEventTrendReportCrnt/getCourseWiseCmEvent', 'CmWiseEventTrendReportCrntController@getCourseWiseCmEvent');
        Route::post('cmWiseEventTrendReportCrnt/filter', 'CmWiseEventTrendReportCrntController@filter');

        // Arms & Svc Wise Event Trend ReportCrnt
        Route::get('armsServiceWiseEventTrendReportCrnt', 'ArmsServiceWiseEventTrendReportCrntController@index');
        Route::post('armsServiceWiseEventTrendReportCrnt/getCourse', 'ArmsServiceWiseEventTrendReportCrntController@getCourse');
        Route::post('armsServiceWiseEventTrendReportCrnt/getTerm', 'ArmsServiceWiseEventTrendReportCrntController@getTerm');
        Route::post('armsServiceWiseEventTrendReportCrnt/getCourseWiseArmsServiceEvent', 'ArmsServiceWiseEventTrendReportCrntController@getCourseWiseArmsServiceEvent');
        Route::post('armsServiceWiseEventTrendReportCrnt/filter', 'ArmsServiceWiseEventTrendReportCrntController@filter');


        // Wing Wise Event Trend ReportCrnt
        Route::get('wingWiseEventTrendReportCrnt', 'WingWiseEventTrendReportCrntController@index');
        Route::post('wingWiseEventTrendReportCrnt/getCourse', 'WingWiseEventTrendReportCrntController@getCourse');
        Route::post('wingWiseEventTrendReportCrnt/getTerm', 'WingWiseEventTrendReportCrntController@getTerm');
        Route::post('wingWiseEventTrendReportCrnt/getCourseWiseWingEvent', 'WingWiseEventTrendReportCrntController@getCourseWiseWingEvent');
        Route::post('wingWiseEventTrendReportCrnt/filter', 'WingWiseEventTrendReportCrntController@filter');

        // Commissioning Course Wise Event Trend ReportCrnt
        Route::get('commissioningCourseWiseEventTrendReportCrnt', 'CommissioningCourseWiseEventTrendReportCrntController@index');
        Route::post('commissioningCourseWiseEventTrendReportCrnt/getCourse', 'CommissioningCourseWiseEventTrendReportCrntController@getCourse');
        Route::post('commissioningCourseWiseEventTrendReportCrnt/getTerm', 'CommissioningCourseWiseEventTrendReportCrntController@getTerm');
        Route::post('commissioningCourseWiseEventTrendReportCrnt/getCourseWiseCommissioningCourseEvent', 'CommissioningCourseWiseEventTrendReportCrntController@getCourseWiseCommissioningCourseEvent');
        Route::post('commissioningCourseWiseEventTrendReportCrnt/filter', 'CommissioningCourseWiseEventTrendReportCrntController@filter');

        // CM group Wise Event Trend ReportCrnt
        Route::get('cmGroupWiseEventTrendReportCrnt', 'CmGroupWiseEventTrendReportCrntController@index');
        Route::post('cmGroupWiseEventTrendReportCrnt/getCourse', 'CmGroupWiseEventTrendReportCrntController@getCourse');
        Route::post('cmGroupWiseEventTrendReportCrnt/getTerm', 'CmGroupWiseEventTrendReportCrntController@getTerm');
        Route::post('cmGroupWiseEventTrendReportCrnt/getCourseWiseCmGroupEvent', 'CmGroupWiseEventTrendReportCrntController@getCourseWiseCmGroupEvent');
        Route::post('cmGroupWiseEventTrendReportCrnt/filter', 'CmGroupWiseEventTrendReportCrntController@filter');


        // Arms & Svc Wise Performance Trend ReportCrnt
        Route::get('armsServiceWisePerformanceTrendReportCrnt', 'ArmsServiceWisePerformanceTrendReportCrntController@index');
        Route::post('armsServiceWisePerformanceTrendReportCrnt/getCourse', 'ArmsServiceWisePerformanceTrendReportCrntController@getCourse');
        Route::post('armsServiceWisePerformanceTrendReportCrnt/getCourseWiseArmsService', 'ArmsServiceWisePerformanceTrendReportCrntController@getCourseWiseArmsService');
        Route::post('armsServiceWisePerformanceTrendReportCrnt/filter', 'ArmsServiceWisePerformanceTrendReportCrntController@filter');

        // Wing Wise Performance Trend ReportCrnt
        Route::get('wingWisePerformanceTrendReportCrnt', 'WingWisePerformanceTrendReportCrntController@index');
        Route::post('wingWisePerformanceTrendReportCrnt/getCourse', 'WingWisePerformanceTrendReportCrntController@getCourse');
        Route::post('wingWisePerformanceTrendReportCrnt/getCourseWiseWing', 'WingWisePerformanceTrendReportCrntController@getCourseWiseWing');
        Route::post('wingWisePerformanceTrendReportCrnt/filter', 'WingWisePerformanceTrendReportCrntController@filter');


        // Commissioning Course Wise Performance Trend ReportCrnt
        Route::get('commissioningCourseWisePerformanceTrendReportCrnt', 'CommissioningCourseWisePerformanceTrendReportCrntController@index');
        Route::post('commissioningCourseWisePerformanceTrendReportCrnt/getCourse', 'CommissioningCourseWisePerformanceTrendReportCrntController@getCourse');
        Route::post('commissioningCourseWisePerformanceTrendReportCrnt/getCourseWiseCommissioningCourse', 'CommissioningCourseWisePerformanceTrendReportCrntController@getCourseWiseCommissioningCourse');
        Route::post('commissioningCourseWisePerformanceTrendReportCrnt/filter', 'CommissioningCourseWisePerformanceTrendReportCrntController@filter');


        // Overall Performance Trend ReportCrnt
        Route::get('overallPerformanceTrendReportCrnt', 'OverallPerformanceTrendReportCrntController@index');
        Route::post('overallPerformanceTrendReportCrnt/getCourse', 'OverallPerformanceTrendReportCrntController@getCourse');
        Route::post('overallPerformanceTrendReportCrnt/filter', 'OverallPerformanceTrendReportCrntController@filter');


        //Event Avg Trend ReportCrnt
        Route::get('eventAvgTrendReportCrnt', 'EventAvgTrendReportCrntController@index');
        Route::post('eventAvgTrendReportCrnt/getCourse', 'EventAvgTrendReportCrntController@getCourse');
        Route::post('eventAvgTrendReportCrnt/getTerm', 'EventAvgTrendReportCrntController@getTerm');
        Route::post('eventAvgTrendReportCrnt/getCourseWiseEvent', 'EventAvgTrendReportCrntController@getCourseWiseEvent');
        Route::post('eventAvgTrendReportCrnt/filter', 'EventAvgTrendReportCrntController@filter');

        // DS Marking Trend ReportCrnt
        Route::get('dsMarkingTrendReportCrnt', 'DsMarkingTrendReportCrntController@index');
        Route::post('dsMarkingTrendReportCrnt/getCourse', 'DsMarkingTrendReportCrntController@getCourse');
        Route::post('dsMarkingTrendReportCrnt/getTerm', 'DsMarkingTrendReportCrntController@getTerm');
        Route::post('dsMarkingTrendReportCrnt/getCourseWiseDsEvent', 'DsMarkingTrendReportCrntController@getCourseWiseDsEvent');
        Route::post('dsMarkingTrendReportCrnt/getCourseWiseDs', 'DsMarkingTrendReportCrntController@getCourseWiseDs');
        Route::post('dsMarkingTrendReportCrnt/filter', 'DsMarkingTrendReportCrntController@filter');
        Route::post('dsMarkingTrendReportCrnt/getCourseWiseDsSubEvent', 'DsMarkingTrendReportCrntController@getCourseWiseDsSubEvent');
        Route::post('dsMarkingTrendReportCrnt/getCourseWiseDsSubSubEvent', 'DsMarkingTrendReportCrntController@getCourseWiseDsSubSubEvent');
        Route::post('dsMarkingTrendReportCrnt/getCourseWiseDsSubSubSubEvent', 'DsMarkingTrendReportCrntController@getCourseWiseDsSubSubSubEvent');
    });
    Route::group(['middleware' => 'ds'], function() {

        // Cm Wise Event Trend ReportCrnt
        Route::get('dsEventTrendReportCrnt', 'DsEventTrendReportCrntController@index');
        Route::post('dsEventTrendReportCrnt/getCourse', 'DsEventTrendReportCrntController@getCourse');
        Route::post('dsEventTrendReportCrnt/getTerm', 'DsEventTrendReportCrntController@getTerm');
        Route::post('dsEventTrendReportCrnt/getCourseWiseCmEvent', 'DsEventTrendReportCrntController@getCourseWiseCmEvent');
        Route::post('dsEventTrendReportCrnt/getCourseWiseCmSubEvent', 'DsEventTrendReportCrntController@getCourseWiseCmSubEvent');
        Route::post('dsEventTrendReportCrnt/getCourseWiseCmSubSubEvent', 'DsEventTrendReportCrntController@getCourseWiseCmSubSubEvent');
        Route::post('dsEventTrendReportCrnt/getCourseWiseCmSubSubSubEvent', 'DsEventTrendReportCrntController@getCourseWiseCmSubSubSubEvent');
        Route::post('dsEventTrendReportCrnt/getCourseWiseCm', 'DsEventTrendReportCrntController@getCourseWiseCm');
        Route::post('dsEventTrendReportCrnt/filter', 'DsEventTrendReportCrntController@filter');
    });

    Route::group(['middleware' => 'deligatedTermResultReport'], function() {
        // term ReportCrnt
        Route::get('termResultReportCrnt', 'TermResultReportCrntController@index');
        Route::post('termResultReportCrnt/getCourse', 'TermResultReportCrntController@getCourse');
        Route::post('termResultReportCrnt/getTerm', 'TermResultReportCrntController@getTerm');
        Route::post('termResultReportCrnt/filter', 'TermResultReportCrntController@filter');
    });
    Route::group(['middleware' => 'deligatedPerformanceAnalysisReport'], function() {
        // Performace Analysis
        Route::get('performanceAnalysisReportCrnt', 'PerformanceAnalysisReportCrntController@index');
        Route::post('performanceAnalysisReportCrnt/getCourse', 'PerformanceAnalysisReportCrntController@getCourse');
        Route::post('performanceAnalysisReportCrnt/getTerm', 'PerformanceAnalysisReportCrntController@getTerm');
        Route::post('performanceAnalysisReportCrnt/getCm', 'PerformanceAnalysisReportCrntController@getCm');
        Route::post('performanceAnalysisReportCrnt/filter', 'PerformanceAnalysisReportCrntController@filter');
    });
    Route::group(['middleware' => 'deligatedDsCiComdt'], function() {
        // course Progressive Result ReportCrnt
        Route::get('courseProgressiveResultReportCrnt', 'CourseProgressiveResultReportCrntController@index');
        Route::post('courseProgressiveResultReportCrnt/getCourse', 'CourseProgressiveResultReportCrntController@getCourse');
        Route::post('courseProgressiveResultReportCrnt/getTerm', 'CourseProgressiveResultReportCrntController@getTerm');
        Route::post('courseProgressiveResultReportCrnt/filter', 'CourseProgressiveResultReportCrntController@filter');
    });
    Route::group(['middleware' => 'deligatedCourseResultReport'], function() {
        // course Result ReportCrnt
        Route::get('courseResultReportCrnt', 'CourseResultReportCrntController@index');
        Route::post('courseResultReportCrnt/getCourse', 'CourseResultReportCrntController@getCourse');
        Route::post('courseResultReportCrnt/filter', 'CourseResultReportCrntController@filter');
    });

    Route::group(['middleware' => 'deligatedEventCombResultReport'], function() {
        // event result combined ReportCrnt
        Route::get('eventResultCombinedReportCrnt', 'EventResultCombinedReportCrntController@index');
        Route::post('eventResultCombinedReportCrnt/getCourse', 'EventResultCombinedReportCrntController@getCourse');
        Route::post('eventResultCombinedReportCrnt/filter', 'EventResultCombinedReportCrntController@filter');
    });




    // End:: current Report Routes
    //Start :: Analytical Engine
    Route::group(['middleware' => 'deligatedDsCiSuperAdmin'], function() {
        //DS Analytics
        Route::get('passportInfoWiseDsAnalytics', 'PassportInfoWiseDsAnalyticsController@index');
        Route::post('passportInfoWiseDsAnalytics/filter', 'PassportInfoWiseDsAnalyticsController@filter');

        Route::get('basicInfoWiseDsAnalytics', 'BasicInfoWiseDsAnalyticsController@index');
        Route::post('basicInfoWiseDsAnalytics/filter', 'BasicInfoWiseDsAnalyticsController@filter');

        Route::get('addressWiseDsAnalytics', 'AddressWiseDsAnalyticsController@index');
        Route::post('addressWiseDsAnalytics/filter', 'AddressWiseDsAnalyticsController@filter');

        Route::get('otherInfoWiseDsAnalytics', 'OtherInfoWiseDsAnalyticsController@index');
        Route::post('otherInfoWiseDsAnalytics/filter', 'OtherInfoWiseDsAnalyticsController@filter');

        Route::get('maritalInfoWiseDsAnalytics', 'MaritalInfoWiseDsAnalyticsController@index');
        Route::post('maritalInfoWiseDsAnalytics/filter', 'MaritalInfoWiseDsAnalyticsController@filter');

        Route::get('bankInfoWiseDsAnalytics', 'BankInfoWiseDsAnalyticsController@index');
        Route::post('bankInfoWiseDsAnalytics/filter', 'BankInfoWiseDsAnalyticsController@filter');

        Route::get('comCourseWiseDsAnalytics', 'ComCourseWiseDsAnalyticsController@index');
        Route::post('comCourseWiseDsAnalytics/filter', 'ComCourseWiseDsAnalyticsController@filter');

        Route::get('recSvcWiseDsAnalytics', 'RecSvcWiseDsAnalyticsController@index');
        Route::post('recSvcWiseDsAnalytics/filter', 'RecSvcWiseDsAnalyticsController@filter');

        Route::get('milQualWiseDsAnalytics', 'MilQualWiseDsAnalyticsController@index');
        Route::post('milQualWiseDsAnalytics/filter', 'MilQualWiseDsAnalyticsController@filter');


        //celebration DS Analytics
        Route::get('celebrationDsAnalytics', 'CelebrationDsAnalyticsController@index');
        Route::post('celebrationDsAnalytics/filter', 'CelebrationDsAnalyticsController@filter');
        Route::post('celebrationDsAnalytics/getmonthDayList', 'CelebrationDsAnalyticsController@getmonthDayList');
    });
    Route::group(['middleware' => 'dsCiSuperAdmin'], function() {
        //CM Analytics
        Route::get('basicInfoWiseCmAnalytics', 'BasicInfoWiseCmAnalyticsController@index');
        Route::post('basicInfoWiseCmAnalytics/filter', 'BasicInfoWiseCmAnalyticsController@filter');

        Route::get('addressWiseCmAnalytics', 'AddressWiseCmAnalyticsController@index');
        Route::post('addressWiseCmAnalytics/filter', 'AddressWiseCmAnalyticsController@filter');

        Route::get('maritalInfoWiseCmAnalytics', 'MaritalInfoWiseCmAnalyticsController@index');
        Route::post('maritalInfoWiseCmAnalytics/filter', 'MaritalInfoWiseCmAnalyticsController@filter');

        Route::get('passportInfoWiseCmAnalytics', 'PassportInfoWiseCmAnalyticsController@index');
        Route::post('passportInfoWiseCmAnalytics/filter', 'PassportInfoWiseCmAnalyticsController@filter');

        Route::get('otherInfoWiseCmAnalytics', 'OtherInfoWiseCmAnalyticsController@index');
        Route::post('otherInfoWiseCmAnalytics/filter', 'OtherInfoWiseCmAnalyticsController@filter');

        Route::get('recSvcWiseCmAnalytics', 'RecSvcWiseCmAnalyticsController@index');
        Route::post('recSvcWiseCmAnalytics/filter', 'RecSvcWiseCmAnalyticsController@filter');

        Route::get('bankInfoWiseCmAnalytics', 'BankInfoWiseCmAnalyticsController@index');
        Route::post('bankInfoWiseCmAnalytics/filter', 'BankInfoWiseCmAnalyticsController@filter');

        Route::get('milQualWiseCmAnalytics', 'MilQualWiseCmAnalyticsController@index');
        Route::post('milQualWiseCmAnalytics/filter', 'MilQualWiseCmAnalyticsController@filter');

        Route::get('comCourseWiseCmAnalytics', 'ComCourseWiseCmAnalyticsController@index');
        Route::post('comCourseWiseCmAnalytics/filter', 'ComCourseWiseCmAnalyticsController@filter');

        //celebration CM Analytics
        Route::get('celebrationCmAnalytics', 'CelebrationCmAnalyticsController@index');
        Route::post('celebrationCmAnalytics/filter', 'CelebrationCmAnalyticsController@filter');
        Route::post('celebrationCmAnalytics/getmonthDayList', 'CelebrationCmAnalyticsController@getmonthDayList');
    });
    //End :: Analytical Engine
});
