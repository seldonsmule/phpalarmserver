<?php

class AlarmState {

  var $state_text;
  var $last_raw_msg;
  var $b_disarmed;
  var $b_armed_away;
  var $b_armed_stay;
  var $b_armed_instant;
  var $timestamp; 
  var $datestring; 
  var $panel;

  var $b_state_set = false;

  var $db;

  function __construct($db, $loadit = false){

    $this->db = $db;

    date_default_timezone_set('America/New_York');

    if($loadit){
      $this->get_last_saved_state();
    }
    

    return;
  }

  
  function get_last_saved_state(){
    if($this->db->get_last_saved_state($this) == null){
      return false;
    }
    
    return true;
   
  }


  // check_state returns true if state changes 

  function check_state($alarmmsg){

     // if 1st time here, just set to current values
     if(!$this->b_state_set){
       $this->save_change($alarmmsg);
       return true;
     }

     // decided to save a single instance of the last message received.  IF this proves
     // to much over head - we will remove
     $this->db->update_last_buffer($alarmmsg->buffer);
//printf("last msg: [%s]\n", $this->db->get_last_buffer());

     $b_something_changed = false;

     if($this->b_armed_away != $alarmmsg->b_armed_away){
       $b_something_changed = true;
     }else if($this->b_armed_stay != $alarmmsg->b_armed_stay){
       $b_something_changed = true;
     }else if($this->b_armed_instant != $alarmmsg->b_armed_instant){
       $b_something_changed = true;
     }

     if($b_something_changed){
       $this->save_change($alarmmsg); 
       return true;
     }


     return false;
  }

  function load($state_text, $b_disarmed, $b_armed_away, $b_armed_stay, $b_armed_instant, $timestamp, $datestring, $panel, $last_raw_msg){
     $this->b_state_set = true;
     $this->state_text = $state_text;

     $this->b_disarmed = $this->b_armed_away = $this->b_armed_stay = $this->b_armed_instant = false;

     if($b_disarmed == 1) $this->b_disarmed = true;
     if($b_armed_away == 1) $this->b_armed_away = true;
     if($b_armed_stay == 1) $this->b_armed_stay = true;
     if($b_armed_instant == 1) $this->b_armed_instant = true;
     
     $this->timestamp = $timestamp;
     $this->datestring = $datestring;
     $this->panel = $panel;
     //$this->last_raw_msg = $last_raw_msg;
     $this->last_raw_msg = str_replace("\r\n","",$last_raw_msg);
  }

  function save_change($alarmmsg){

     $this->b_armed_away = $alarmmsg->b_armed_away;
     $this->b_armed_stay = $alarmmsg->b_armed_stay;
     $this->b_armed_instant = $alarmmsg->b_armed_instant;

     $this->b_state_set = true;
     $this->timestamp = $alarmmsg->timestamp;
     $this->datestring = date("H:i:s-m/d/Y", $this->timestamp);

     if($this->b_armed_away || $this->b_armed_stay || $this->b_armed_instant){
       $this->b_disarmed = false;
     }else{
       $this->b_disarmed = true;
     }

     $this->state_text = "DISARMED";
     $this->last_raw_msg = $alarmmsg->buffer;
     $this->panel = $alarmmsg->panel_name;

     if($this->b_armed_away){
       $this->state_text = "ARMED-AWAY";
     }

     if($this->b_armed_stay){
       $this->state_text = "ARMED-STAY";
     }

     if($this->b_armed_instant){
       $this->state_text = "ARMED-INSTANT";
     }


     // now store in db as well
     $this->db->save_state_change($this);


  }

  function armed(){
    if($this->b_disarmed){
      return false;
    }
    
    return true;
  }

  function setup(){
     return $this->b_state_set;
  }

  // write an small html file of current state
  function write_html($filename){

    $state_color = "pink";
    $state_text = "Unknown";
    $description_text = "Unknown";

    if($this->b_disarmed){
      $state_color = "green";
      $state_text = "Disarmed";
      $description_text = "Alarm not set";
    }else{
      $state_color = "red";
      $state_text = "Armed";
      if($this->b_armed_away){
        $description_text = "Alarm set to Away mode";
      }else if($this->b_armed_stay){
        $description_text = "Alarm set to Stay mode";
        if($this->b_armed_instant){
          $description_text = "Alarm set to Instant mode";
        }
      }
    }

    $time_text = date("H:i:s", $this->timestamp);
    $date_text = date("m/d/Y", $this->timestamp);

    $fp = fopen($filename, "w");

    fprintf($fp, "<html>\n");

    fprintf($fp, "<head>\n");
    fprintf($fp, "  <style>\n");
    fprintf($fp, "    table, th, td {\n");
    fprintf($fp, "      border: 1px solid black;\n");
    fprintf($fp, "      border-collapse: collapse;\n");
    fprintf($fp, "      text-align: left;\n");
    fprintf($fp, "    }\n");
    fprintf($fp, "  </style>\n");
    fprintf($fp, "</head>\n");

    fprintf($fp, "<body>\n");

    fprintf($fp, "  <table>\n");
    fprintf($fp, "    <tr>\n");
    fprintf($fp, "      <th>State</th>\n");
    fprintf($fp, "      <th>Description</th>\n");
    fprintf($fp, "      <th>Time</th>\n");
    fprintf($fp, "      <th>Date</th>\n");
    fprintf($fp, "      <th>Raw Message</th>\n");
    fprintf($fp, "    </tr>\n");

    fprintf($fp, "    <tr>\n");
    fprintf($fp, "      <td><font color=\"%s\">%s</font></td>\n", $state_color, $state_text);
    fprintf($fp, "      <td>%s</td>\n", $description_text);
    fprintf($fp, "      <td>%s</td>\n", $time_text);
    fprintf($fp, "      <td>%s</td>\n", $date_text);
    fprintf($fp, "      <td>%s</td>\n", $this->last_raw_msg);
    fprintf($fp, "    </tr>\n");

    fprintf($fp, "  </table>\n");

    fprintf($fp, "</body>\n");

    fprintf($fp, "</html>\n");

    fclose($fp);
  }

} // end AlarmState


?>
