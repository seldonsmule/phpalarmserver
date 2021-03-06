<?php

  $g_unix=php_uname($mode="s");

// you may want to create a different working directory and change
// the location below

  if($g_unix == "Linux"){
    $g_working_dir = "/var/www/html/phpalarmserver";
    // optional state file for a status web server (no server side scripting)
    // to at least have the current alarm state for display
    $g_html_state_file = "/var/www/html/alarm_state.html";
  }
  else{
    //macos MAMP directory
    $g_working_dir = "/Applications/MAMP/htdocs/phpalarmserver";
    // optional state file for a status web server (no server side scripting)
    // to at least have the current alarm state for display
    //$g_html_state_file = "/Users/rxe789/SecuritySpy/Web/alarm_state.html";
    $g_html_state_file = "logs/alarm_state.html";
  }

  $g_admin_mode = false; // if true - stop accepting web request

  $g_fake_mode = false; // if true, we do not connect to the alarm system
                       // and will operate off of some fake out system instead

  $g_fake_inputfile = "nerd.in";
  $g_fake_dir = "fake_mode";


  class EgcConfig{

    var $working_dir;
    var $base_database_name = "egcalarm.db";
    var $database_name;
    var $database_dir;
    var $log_dir;

    var $admin_mode;  // if true - don't accept web requet
    var $fake_mode;  // if true - use fake file
    var $fake_inputfile;  // file to use

    function __construct(){
      $this->working_dir = $GLOBALS['g_working_dir'];
      $this->database_dir = sprintf("%s/db", $this->working_dir);
      $this->log_dir = sprintf("%s/logs", $this->working_dir);

      $this->database_name = sprintf("%s/%s", $this->database_dir, 
                                              $this->base_database_name);

      $this->admin_mode = false;

      $this->fake_mode = $GLOBALS['g_fake_mode'];
      $this->fake_inputfile = $GLOBALS['g_fake_inputfile'];
      $this->fake_dir = $GLOBALS['g_fake_dir'];

      if(!file_exists($this->database_dir)){
        mkdir($this->database_dir);
      }

      if(!file_exists($this->log_dir)){
        mkdir($this->log_dir);
      }
    }

  }


?>
