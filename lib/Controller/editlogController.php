<?php

/**
 * Description of catController
 *
 * @author antonio
 */
class editlogController extends viewController {

	private $jid = "";
	private $project_status = "";
	private $thisUrl;

    private $job_archived = false;
    private $job_owner_email;
    private $jobData;
    private $job_stats;
    private $data;

	public function __construct() {
		parent::__construct();
		parent::makeTemplate("editlog.html");

        $filterArgs = array(
                'jid'      => array( 'filter' => FILTER_SANITIZE_NUMBER_INT ),
                'password' => array( 'filter' => FILTER_SANITIZE_STRING, 'flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ),
        );

        $__postInput = filter_input_array( INPUT_GET, $filterArgs );

        $this->jid      = $__postInput[ "jid" ];
        $this->password = $__postInput[ "password" ];
        $this->thisUrl  = $_SERVER[ 'REQUEST_URI' ];

    }

	public function doAction() {

        $this->generateAuthURL();

        //pay a little query to avoid to fetch 5000 rows
        $this->jobData = $jobData = getJobData( $this->jid, $this->password );

        $wStruct = new WordCount_Struct();
        $wStruct->setIdJob( $this->jid );
        $wStruct->setJobPassword( $this->password );
        $wStruct->setNewWords( $jobData[ 'new_words' ] );
        $wStruct->setDraftWords( $jobData[ 'draft_words' ] );
        $wStruct->setTranslatedWords( $jobData[ 'translated_words' ] );
        $wStruct->setApprovedWords( $jobData[ 'approved_words' ] );
        $wStruct->setRejectedWords( $jobData[ 'rejected_words' ] );

        if( $jobData['status'] == Constants_JobStatus::STATUS_ARCHIVED || $jobData['status'] == Constants_JobStatus::STATUS_CANCELLED ){
            //this job has been archived
            $this->job_archived = true;
            $this->job_owner_email = $jobData['job_owner'];
        }

        $tmp = CatUtils::getEditingLogData( $this->jid, $this->password );

        $this->data  = $tmp[ 0 ];
        $this->stats = $tmp[ 1 ];

        $this->job_stats = CatUtils::getFastStatsForJob($wStruct);

        $proj = getProject( $jobData['id_project'] );
        $this->project_status = $proj[0];

	}

	public function setTemplateVars() {

        $this->template->job_archived = ( $this->job_archived ) ? INIT::JOB_ARCHIVABILITY_THRESHOLD : '';
        $this->template->owner_email  = $this->job_owner_email;

        $this->template->jid          = $this->jid;
        $this->template->password     = $this->password;
        $this->template->data         = $this->data;
        $this->template->stats        = $this->stats;
        $this->template->pname        = $this->data[ 0 ][ 'pname' ];
        $this->template->source_code  = $this->data[ 0 ][ 'source_lang' ];
        $this->template->target_code  = $this->data[ 0 ][ 'target_lang' ];

        $this->job_stats['STATUS_BAR_NO_DISPLAY'] = ( $this->project_status['status_analysis'] == Constants_ProjectStatus::STATUS_DONE ? '' : 'display:none;' );
        $this->job_stats['ANALYSIS_COMPLETE']     = ( $this->project_status['status_analysis'] == Constants_ProjectStatus::STATUS_DONE ? true : false );
        $this->template->job_stats    = $this->job_stats;

        $this->template->showDQF = ( INIT::$DQF_ENABLED && !empty( $this->jobData['dqf_key'] ) );

        $this->template->build_number = INIT::$BUILD_NUMBER;
        $this->template->extended_user  = trim( $this->logged_user['first_name'] . " " . $this->logged_user['last_name'] );
        $this->template->logged_user  = $this->logged_user['short'];
        $this->template->incomingUrl  = '/login?incomingUrl=' . $this->thisUrl;
        $this->template->authURL      = $this->authURL;

	}
}


