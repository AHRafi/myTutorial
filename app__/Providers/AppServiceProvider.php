<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;
use Auth;
use Route;
use Common;
use App\DeligateCiAcctToDs;
use App\TermToCourse;
use App\CiModerationMarkingLock;
use App\EventAssessmentMarkingLock;
use App\ComdtModerationMarkingLock;
use App\CiObsnMarkingLock;
use App\ComdtObsnMarkingLock;
use App\DsObsnMarkingLock;
use App\DsRemarksViewer;
use Session;

class AppServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        view()->composer('*', function ($view) {
            if (Auth::check()) {
                $currentControllerFunction = Route::currentRouteAction();
                $controllerName = $currentCont = '';
                if (!empty($currentControllerFunction[1])) {
                    $currentCont = preg_match('/([a-z]*)@/i', request()->route()->getActionName(), $currentControllerFunction);
                    $controllerName = str_replace('controller', '', strtolower($currentControllerFunction[1]));
                }

                //get ds deligation list
                $dsDeligationList = Common::getDsDeligationList();
                $reportDeligationList = Common::getReportDelegationList();
                $reportDeligationDsList = Common::getReportDelegationDsList();

                //START:: Get Count of Unlock Assessment
                $activeTermInfo = TermToCourse::where('term_to_course.status', '1')
                                ->where('term_to_course.active', '1')->select('course_id', 'term_id')->first();

                $unlockCountArr = [];
                if (!empty($activeTermInfo)) {
                    if (in_array(Auth::user()->group_id, [2, 3]) || in_array(Auth::user()->id, $dsDeligationList)) {
                        $unlockCountArr['total'] = 0;
                        if (in_array(Auth::user()->group_id, [3]) || in_array(Auth::user()->id, $dsDeligationList)) {
                            //Get Event Assessment
                            $ciModMarkList = CiModerationMarkingLock::join('event_assessment_marking_lock', function($join) {
                                        $join->on('event_assessment_marking_lock.course_id', '=', 'ci_moderation_marking_lock.course_id');
                                        $join->on('event_assessment_marking_lock.term_id', '=', 'ci_moderation_marking_lock.term_id');
                                        $join->on('event_assessment_marking_lock.event_id', '=', 'ci_moderation_marking_lock.event_id');
                                        $join->on('event_assessment_marking_lock.sub_event_id', '=', 'ci_moderation_marking_lock.sub_event_id');
                                        $join->on('event_assessment_marking_lock.sub_sub_event_id', '=', 'ci_moderation_marking_lock.sub_sub_event_id');
                                        $join->on('event_assessment_marking_lock.sub_sub_sub_event_id', '=', 'ci_moderation_marking_lock.sub_sub_sub_event_id');
                                    })
                                    ->where('ci_moderation_marking_lock.course_id', $activeTermInfo->course_id)
                                    ->where('ci_moderation_marking_lock.term_id', $activeTermInfo->term_id)
                                    ->pluck('event_assessment_marking_lock.id', 'event_assessment_marking_lock.id')
                                    ->toArray();

                            $unlockCountArr['event_assessment'] = EventAssessmentMarkingLock::where('event_assessment_marking_lock.course_id', $activeTermInfo->course_id)
                                    ->where('event_assessment_marking_lock.term_id', $activeTermInfo->term_id)
                                    ->where('event_assessment_marking_lock.status', '2')
                                    ->whereNotIn('event_assessment_marking_lock.id', $ciModMarkList)
                                    ->count();

                            //Get DS observation assessment
                            $unlockCountArr['ds_observation'] = DsObsnMarkingLock::where('ds_obsn_marking_lock.course_id', $activeTermInfo->course_id)
                                    ->where('ds_obsn_marking_lock.term_id', $activeTermInfo->term_id)
                                    ->where('ds_obsn_marking_lock.status', '2')
                                    ->count();
                            $unlockCountArr['total'] += (!empty($unlockCountArr['event_assessment']) ? $unlockCountArr['event_assessment'] : 0 ) + (!empty($unlockCountArr['ds_observation']) ? $unlockCountArr['ds_observation'] : 0);
                        }

                        //Get CI moderation assessment
                        $comdtModMarkList = ComdtModerationMarkingLock::join('course', 'course.id', 'comdt_moderation_marking_lock.course_id')
                                ->join('ci_moderation_marking_lock', function($join) {
                                    $join->on('ci_moderation_marking_lock.course_id', '=', 'comdt_moderation_marking_lock.course_id');
                                    $join->on('ci_moderation_marking_lock.term_id', '=', 'comdt_moderation_marking_lock.term_id');
                                    $join->on('ci_moderation_marking_lock.event_id', '=', 'comdt_moderation_marking_lock.event_id');
                                    $join->on('ci_moderation_marking_lock.sub_event_id', '=', 'comdt_moderation_marking_lock.sub_event_id');
                                    $join->on('ci_moderation_marking_lock.sub_sub_event_id', '=', 'comdt_moderation_marking_lock.sub_sub_event_id');
                                    $join->on('ci_moderation_marking_lock.sub_sub_sub_event_id', '=', 'comdt_moderation_marking_lock.sub_sub_sub_event_id');
                                })
                                ->where('comdt_moderation_marking_lock.course_id', $activeTermInfo->course_id)
                                ->where('comdt_moderation_marking_lock.term_id', $activeTermInfo->term_id)
                                ->pluck('ci_moderation_marking_lock.id', 'ci_moderation_marking_lock.id')
                                ->toArray();

                        $unlockCountArr['ci_moderation'] = CiModerationMarkingLock::where('ci_moderation_marking_lock.course_id', $activeTermInfo->course_id)
                                ->where('ci_moderation_marking_lock.term_id', $activeTermInfo->term_id)
                                ->where('ci_moderation_marking_lock.status', '2')
                                ->whereNotIn('ci_moderation_marking_lock.id', $comdtModMarkList)
                                ->count();

                        //Get ci observation assessment
                        $unlockCountArr['ci_observation'] = CiObsnMarkingLock::where('ci_obsn_marking_lock.course_id', $activeTermInfo->course_id)
                                ->where('ci_obsn_marking_lock.status', '2')
                                ->count();



                        //Get Comdt observation assessment
                        $unlockCountArr['comdt_observation'] = ComdtObsnMarkingLock::where('comdt_obsn_marking_lock.course_id', $activeTermInfo->course_id)
                                ->where('comdt_obsn_marking_lock.status', '2')
                                ->count();
                        $unlockCountArr['total'] += (!empty($unlockCountArr['ci_moderation']) ? $unlockCountArr['ci_moderation'] : 0 ) + (!empty($unlockCountArr['ci_observation']) ? $unlockCountArr['ci_observation'] : 0) + (!empty($unlockCountArr['comdt_observation']) ? $unlockCountArr['comdt_observation'] : 0);
                    }
                }

//                echo '<pre>';
//                print_r($unlockCountArr);
//                exit;
                //END:: Get Count of Unlock Assessment
                //START:: Get Count of DS Remarks
                $dsRemarksCount = DsRemarksViewer::where('user_id', Auth::user()->id)
                                ->where('status', '0')->count();
                //END:: Get Count of DS Remarks







                $view->with([
                    'controllerName' => $controllerName,
                    'dsDeligationList' => $dsDeligationList,
                    'reportDeligationList' => $reportDeligationList,
                    'reportDeligationDsList' => $reportDeligationDsList,
                    'unlockCountArr' => $unlockCountArr,
                    'dsRemarksCount' => $dsRemarksCount
                ]);
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

}
