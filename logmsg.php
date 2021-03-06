<?php

 class LogMsg{

   var $fp;
   var $filename;
   var $roll_log;
   var $timeopened;
   //var $timetoroll; // 7/6/2020 - removed.  changed logic to use stat

   function __construct($filename=null){

     if($filename == null){
       $this->fp = STDOUT;
     }else{
       $this->fp = fopen($filename, 'a');
     }     

     $this->filename = $filename;
     $this->roll_log = 0;
     $this->timeopened = time();

     
   }


   function log($fn,$ln,$msg){

     date_default_timezone_set('America/New_York');
     $datestring = date("H:i:s-m/d/Y");

     $base = basename($fn);

     fprintf($this->fp,"%s|%s:%d|%s|%s\n",$datestring,$base, $ln, gethostname(), $msg);


     if($this->test_time_to_roll()){
       $this->roll_logs();
     }

   }

   function set_roll_log($time_in_hours){
     $this->roll_log = $time_in_hours;
//     $this->timetoroll = $this->timeopened + ($this->roll_log * 3600);
   }

   function test_time_to_roll(){

     if($this->roll_log == 0){
       return false;
     }

     // 7/6/2020 - updating logic to work off of date the file was created
     //            instead of keeping track of time in the object
     //

     $stat = stat($this->filename);

     $a_timetoroll = $stat['atime'] + ($this->roll_log * 3600);
     //$a_timetoroll = $stat['atime'] + ($this->roll_log * 60); //test a min

//printf("[%s] a_timetoroll[%d] timenow[%d] distance[%d] \n", $this->filename, $a_timetoroll, time(), $a_timetoroll - time());

/* -- old code
     if(time() > $this->timetoroll){
*/
     if(time() > $a_timetoroll){
       return true;
     }

     return false;
   }

   function roll_logs(){
//print_r($this);

     if($this->filename == null){
       return;
     }

     $new_filename = sprintf("%s.old", $this->filename);
     fclose($this->fp);
     rename($this->filename, $new_filename);

     $this->fp = fopen($this->filename, 'a');
     $this->timeopened = time();

     if($this->roll_log > 0){
       $this->set_roll_log($this->roll_log);
     }

   }

 }


?>
