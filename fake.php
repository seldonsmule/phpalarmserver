<?php

// allows you to put the system in a diag mode that operates without the need for the alarm system
// use fake_cmd.php to send dummy (fake) alarm states and see that everything is working
// better than pissing off the family with the alarm going on/off
//

require_once('config.php');
require_once('logmsg.php');

class FakeMode {

  var $b_enabled = false;
  var $b_was_enabled = false; // to help users understand for state management
  var $enablefile;
  var $b_was_off = true;

  var $b_tail_open;

  var $config;
  var $mylog;

  var $fp;

  var $fp_pipe;
  var $fp_socket;

  function __construct($mylog){

    $this->mylog = $mylog;

    $this->config = new EgcConfig();

    $this->enablefile = sprintf("%s/fakemode.on", $this->dirname());

    $this->b_tail_open = false;

    $this->is_on(); // call to set current state

  }

  function get_handle(){

    if($this->is_on()){
      return $this->fp_pipe;
    }else{
      return $this->fp_socket;
    }

  }

  function open($hostname, $port){

    if(!$this->open_socket($hostname, $port)){
      $this->mylog->log(__FILE__,__LINE__, "error opening socket");
      return false;
    }

    if(!$this->open_tail()){
      $this->$mylog->log(__FILE__,__LINE__, "error opening tail");
      $this->close_socket();
      return false;
    }

    return true;


  }

  function close(){
    $this->close_socket();
    $this->close_tail();
  }

  function open_socket($hostname, $port){
  
    $this->fp_socket = fsockopen($hostname, $port, $errno, $errstr, 30);

    if(!$this->fp_socket){
      $$this->mylog->log(__FILE__,__LINE__, "$errstr ($errno)<br />");

      return false;
    }

    return true;
  }

  function close_socket(){
    fclose($this->fp_socket);
  }

  function close_tail(){
    pclose($this->fp);
    $this->b_tail_open = false;
  }

  function open_tail(){

    $tailcmd = sprintf("tail -f %s 2>&1", $this->fullfilename());
    $this->fp_pipe = popen($tailcmd, 'r');

    $this->b_tail_open = true;
  
    if(!$this->fp_pipe){
      $$this->mylog->log(__FILE__,__LINE__, "$errstr ($errno)<br />");
      return false;
    }

    return true;
  }
 
  function dirname(){
    return($this->config->fake_dir);
  }

  function filename(){
    return($this->config->fake_inputfile);
  }

  function fullfilename(){
    $name = sprintf("%s/%s", $this->dirname(), $this->filename());
    return($name);
  }

  function is_enablefile(){
    return(file_exists($this->enablefile));
  }

  function is_on(){
    if($this->is_enablefile()){
      $this->b_enabled = true;
      $this->b_was_enabled = true;
    }else{
      $this->b_enabled = false;
    }

    return $this->b_enabled;
  }

  function was_on(){
    return $this->b_was_enabled;
  }

  function reset_was_on(){
    $this->b_was_enabled = false;
  }

  function is_was_off(){
    return $this->b_was_off;
  }

  function set_was_off($onoff){
    $this->b_was_off = $onoff;
  }

  function turn_on(){
    fopen($this->enablefile,"w");
    $this->b_enabled = true;
    $this->b_was_enabled = true;
  }

  function turn_off(){
    if($this->is_enablefile()){
      unlink($this->enablefile);
      $this->b_enabled = false;
 
      $this->send_disarm(); // forces anyone reading the file to see a new
                            // entry and discover things are turned off

   }
  }

  function send_disarm(){

    $this->mylog->log(__FILE__,__LINE__, "FAKE MODE - DISARM");

    $catcmd = sprintf("cat %s/input.disarm >> %s",
                      $this->dirname(),
                      $this->fullfilename());

    $this->mylog->log(__FILE__,__LINE__, exec($catcmd));

  }

  function send_armaway(){

    $this->mylog->log(__FILE__,__LINE__, "FAKE MODE - ARM AWAY");

    $catcmd = sprintf("cat %s/input.away >> %s",
                      $this->dirname(),
                      $this->fullfilename());

    $this->mylog->log(__FILE__,__LINE__, exec($catcmd));

  }

  function send_armstay(){

    $this->mylog->log(__FILE__,__LINE__, "FAKE MODE - ARM STAY");

    $catcmd = sprintf("cat %s/input.stay >> %s",
                      $this->dirname(),
                      $this->fullfilename());

    $this->mylog->log(__FILE__,__LINE__, exec($catcmd));

  }

  function notify(){
    $catcmd = sprintf("echo '!FAKE State Change' >> %s",
                      $this->fullfilename());

    $this->mylog->log(__FILE__,__LINE__, exec($catcmd));
  }

  function tail_handle(){

    $tailcmd = sprintf("tail -f %s 2>&1", $this->fullfilename());
    $this->fp = popen($tailcmd, 'r');

    $this->b_tail_open = true;

    return $this->fp;

  }

  function is_tail_open(){
    return $this->b_tail_open;
  }

  function close_tail_handle(){
    if($this->b_tail_open){
      pclose($this->fp);
      $this->b_tail_open = false;
    }
  } 

}
