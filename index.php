<?php
if( !@include_once 'inc/config.inc.php')
	header("Location: configMissing");

INIT::obtain();
$db = Database::obtain ( INIT::$DB_SERVER, INIT::$DB_USER, INIT::$DB_PASS, INIT::$DB_DATABASE );
$db->connect ();

Log::$uniqID = ( isset( $_COOKIE['PHPSESSID'] ) ? substr( $_COOKIE['PHPSESSID'], 0 , 13 ) : uniqid() );

$controller = controller::getInstance ();
$controller->doAction ();
$controller->finalize ();
$db->close();