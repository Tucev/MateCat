<?php
$root = realpath(dirname(__FILE__) . '/../../../');
include_once "$root/inc/config.inc.php";
INIT::obtain();
require_once INIT::$MODEL_ROOT.'/queries.php';

$db=Database::obtain(INIT::$DB_SERVER, INIT::$DB_USER, INIT::$DB_PASS, INIT::$DB_DATABASE);
$db->debug=false;
$db->connect();


declare ( ticks = 1 );
if (! function_exists ( 'pcntl_signal' )) {
    $msg = "****** PCNTL EXTENSION NOT LOADED. KILLING THIS PROCESS COULD CAUSE UNPREDICTABLE ERRORS ******";
    _TimeStampMsg( $msg );
} else {

    pcntl_signal( SIGTERM, 'sigSwitch' );
    pcntl_signal( SIGINT, 'sigSwitch' );
    pcntl_signal( SIGHUP, 'sigSwitch' );

    $msg = str_pad( " " . getmypid() . " Signal Handler Installed ", 50, "-", STR_PAD_BOTH );
    _TimeStampMsg( $msg );

}



function _TimeStampMsg( $msg, $log = true ) {
    if( $log ) Log::doLog( $msg );
    echo "[" . date( DATE_RFC822 ) . "] " . $msg . "\n";
}
