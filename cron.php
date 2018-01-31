<?php
require_once '../../../wp-load.php';
set_time_limit( 6000 );
$date = new DateTime();
$date = $date->modify( '-30 days' )->format( 'Y-m-d H:i:s' );

global $IR_Facebook_Comments_Counter;
$IR_Facebook_Comments_Counter->parsing( $date );
