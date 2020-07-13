<?php

require_once('logmsg.php');
require_once('fake.php')

nerd

 $mylog = new LogMsg("fake.log");

 $fake = new FakeMode($mylog);

 if(count($argv) != 2){
   printf("Usage fake_cmd.php enable|disable|status|send_disarm|send_armaway|send_armstay\n");
   exit(1);
 }

 $cmd = $argv[1];

 switch($cmd){

   case "enable":
     $fake->turn_on();
   break;

   case "disable":
     $fake->turn_off();
   break;

   case "send_disarm":
     $fake->send_disarm();
   break;

   case "send_armaway":
     $fake->send_armaway();
   break;

   case "send_armstay":
     $fake->send_armstay();
   break;

   case "status":

     if($fake->is_on()){
       printf("Fake Model is Enabled\n");
     }else{
       printf("Fake Model is Disabled\n");
     }

     printf("Fake dir[%s]\n", $fake->dirname());

   break;

   default:
     printf("unknown command[%s]\n", $cmd);
   return;


 }

 //print_r($fake);


?>
