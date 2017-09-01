<?php

  require_once('phpalarmserver/config.php');
  require_once('phpalarmserver/alarmstate.php');
  require_once('phpalarmserver/db.php');
  require_once('phpalarmserver/server.php');
  require_once('phpalarmserver/logmsg.php');

  $mylog = new LogMsg("phpalarmserver/logs/alarm.log");

  $mylog->log(__FILE__,__LINE__, "hello world");

  if($g_admin_mode){
    $mylog->log(__FILE__,__LINE__, "WARNING: Admin Mode On");

    $protocol = "HTTP/1.0";
    if ( "HTTP/1.1" == $_SERVER["SERVER_PROTOCOL"] ){
      $protocol = "HTTP/1.1";
    }

    header("$protocol 503 Service Unavailable", true, 503 );
    header("Retry-After: 3600");


    return;
  }



  $db = new MyDB();
  if(!$db){
    echo $db->lastErrorMsg();
  }

  //$state = new AlarmState($db);

  $server = new Server($mylog, $db);

  $server->serve();
 

/*
  if( ($last = $db->get_last_saved_state()) != null){
    print_r($last);
  }
*/



?>
