<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
 */

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::post('login', 'Api\AuthenticateController@authenticate');
Route::post('dashboard', 'Api\DashboardController@index');

Route::post('cm/updatePassword', 'Api\CmController@updatePassword');
//Profile
Route::post('cm/profile', 'Api\CmController@profile');

Route::post('cm/getRank', 'Api\CmController@getRank');
Route::post('cm/updatePersonalInfo', 'Api\CmController@updatePersonalInfo');
Route::post('cm/updatePhoto', 'Api\CmController@updatePhoto');
Route::post('cm/updateFamilyInfo', 'Api\CmController@updateFamilyInfo');
Route::post('cm/updateMaritalStatus', 'Api\CmController@updateMaritalStatus');
Route::post('cm/updateBrotherSisterInfo', 'Api\CmController@updateBrotherSisterInfo');
Route::post('cm/rowAddForBrotherSister', 'Api\CmController@rowAddForBrotherSister');
Route::post('cm/getDistrict', 'Api\CmController@getDistrict');
Route::post('cm/getThana', 'Api\CmController@getThana');
Route::post('cm/updateAddress', 'Api\CmController@updateAddress');
Route::post('cm/rowAddForCivilEducation', 'Api\CmController@rowAddForCivilEducation');
Route::post('cm/updateCivilEducationInfo', 'Api\CmController@updateCivilEducationInfo');
Route::post('cm/rowAddForServiceRecord', 'Api\CmController@rowAddForServiceRecord');
Route::post('cm/updateServiceRecordInfo', 'Api\CmController@updateServiceRecordInfo');
Route::post('cm/rowAddForAwardRecord', 'Api\CmController@rowAddForAwardRecord');
Route::post('cm/updateAwardRecordInfo', 'Api\CmController@updateAwardRecordInfo');
Route::post('cm/rowAddForUnMsn', 'Api\CmController@rowAddForUnMsn');
Route::post('cm/updateUnMsn', 'Api\CmController@updateUnMsn');
Route::post('cm/rowAddForMilQual', 'Api\CmController@rowAddForMilQual');
Route::post('cm/updateMilQualInfo', 'Api\CmController@updateMilQualInfo');
Route::post('cm/updateNextKin', 'Api\CmController@updateNextKin');
Route::post('cm/updatePassportDetails', 'Api\CmController@updatePassportDetails');
Route::post('cm/updateWinterTraining', 'Api\CmController@updateWinterTraining');
Route::post('cm/updateCmOthersInfo', 'Api\CmController@updateCmOthersInfo');

Route::post('cm/rowAddForCountry', 'Api\CmController@rowAddForCountry');
Route::post('cm/rowAddForChild', 'Api\CmController@rowAddForChild');
Route::post('cm/rowAddForBank', 'Api\CmController@rowAddForBank');
Route::post('cm/updateCountryVisit', 'Api\CmController@updateCountryVisit');
Route::post('cm/updateBank', 'Api\CmController@updateBank');

//MUA
Route::post('mutualAssessment', 'Api\MutualAssessmentController@index');
Route::post('mutualAssessment/getSubEvent', 'Api\MutualAssessmentController@getSubEvent');
Route::post('mutualAssessment/getSubSubEvent', 'Api\MutualAssessmentController@getSubSubEvent');
Route::post('mutualAssessment/getSubSubSubEvent', 'Api\MutualAssessmentController@getSubSubSubEvent');
Route::post('mutualAssessment/saveMark', 'Api\MutualAssessmentController@saveMark');
Route::post('mutualAssessment/getUpdatedCmList', 'Api\MutualAssessmentController@getUpdatedCmList');
Route::post('mutualAssessment/requestForUnlock', 'Api\MutualAssessmentController@requestForUnlock');
//Content
Route::post('content/filter', 'Api\ContentController@filter');
Route::post('content', 'Api\ContentController@index')->name('content.index');
Route::post('content/create', 'Api\ContentController@create')->name('content.create');
Route::post('content/edit', 'Api\ContentController@edit')->name('content.edit');
Route::post('content/addContentRow', 'Api\ContentController@addContentRow')->name('content.addContentRow');
Route::post('content/store', 'Api\ContentController@store')->name('content.store');
Route::post('content/update', 'Api\ContentController@update')->name('content.update');
Route::post('content/delete', 'Api\ContentController@destroy')->name('content.delete');

Route::post('/documentSearch', 'Api\DocumentSearchReportController@index')->name('documentSearch.index');
Route::post('/documentSearch/filter', 'Api\DocumentSearchReportController@filter');

Route::post('/dailyDocReport', 'Api\DailyDocReportController@index')->name('dailyDocReport.index');
Route::post('/dailyDocReport/filter', 'Api\DailyDocReportController@filter');

Route::post('/monthlyDocReport', 'Api\MonthlyDocReportController@index')->name('monthlyDocReport.index');
Route::post('/monthlyDocReport/filter', 'Api\MonthlyDocReportController@filter');

Route::post('/catWiseDocReport', 'Api\CatWiseDocReportController@index')->name('catWiseDocReport.index');
Route::post('/catWiseDocReport/filter', 'Api\CatWiseDocReportController@filter');

Route::post('/originatorWiseDocReport', 'Api\OriginatorWiseDocReportController@index')->name('originatorWiseDocReport.index');
Route::post('/originatorWiseDocReport/filter', 'Api\OriginatorWiseDocReportController@filter');

Route::post('/classificationWiseDocReport', 'Api\ClassificationWiseDocReportController@index')->name('classificationWiseDocReport.index');
Route::post('/classificationWiseDocReport/filter', 'Api\ClassificationWiseDocReportController@filter');

Route::post('/cmEvalOfGs', 'Api\CmEvalOfGsController@index');
Route::post('/cmEvalOfGs/getSubject', 'Api\CmEvalOfGsController@getSubject');
Route::post('/cmEvalOfGs/getLesson', 'Api\CmEvalOfGsController@getLesson');
Route::post('/cmEvalOfGs/storeGrading', 'Api\CmEvalOfGsController@storeGrading');
Route::post('/cmEvalOfGs/saveRequestForUnlock', 'Api\CmEvalOfGsController@saveRequestForUnlock');
