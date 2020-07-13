<?php

require_once('config.php');
require_once('apikey.php');
require_once('alarmmessage.php');
require_once('alarmstate.php');
require_once('db.php');
require_once('json_resp.php');
require_once('logmsg.php');
require_once('fake.php');


class restMsg {

  var $uri;
  var $method;
  var $path;
  var $query;
  var $data;
  var $mylog;

  function __construct($mylog){

    $this->mylog = $mylog;
    $this->uri = $_SERVER['REQUEST_URI'];
    $this->method = $_SERVER['REQUEST_METHOD'];

//$this->mylog->log(__FILE__,__LINE__, print_r($mylog));

    $url_details = parse_url($this->uri);
    $this->path = $url_details['path'];
    $this->query = $url_details['query'];

    $this->data = json_decode(file_get_contents('php://input'));

  }

  function dump(){
    $this->mylog->log(__FILE__,__LINE__,print_r($this, true));
  }

}

// did the extends so i could leave the restMsg generic for
// maybe future stuff
class AlarmDecoderMsg extends restMsg{

  var $key;
  var $validkey;
  var $db;

  function __construct($mylog, $db){
    parent::__construct($mylog);

    $this->db = $db;

    $this->key = new ApiKey($this->db);

    $this->validkey = false;

    $tmparray = explode("=", $this->query);   

    if(strcmp($tmparray[0],"apikey") != 0){
      // key invalid or not there
      return; 
    }

    // see if key is valid
    if($this->key->find($tmparray[1])){
      $this->validkey = true;
      return;
    }

    // if here already false - not valid!
    return;

  }

  function dump(){
    $this->mylog->log(__FILE__,__LINE__,print_r($this, true));
  }

}

class Server {

    var $myMsg;
    var $mylog;
    var $db;
    var $fake;

    function __construct($mylog, $db){

      $this->mylog = $mylog;
      $this->db = $db;

      $this->fake = new FakeMode($mylog);
    }

    public function process_get($msg){

      if(strcmp($msg->path, "/api/v1/alarmdecoder") == 0){
        $state = new AlarmState($this->db,true);
        $this->mylog->log(__FILE__,__LINE__, sprintf("GET %s\n", print_r($state,true)));
        $respMsg = new getAlarmDecoderResp($state, $this->mylog);
        $respJSON = json_encode($respMsg, JSON_PRETTY_PRINT);
        header('Content-type: application/json');
        echo $respJSON;

        return;
      }


    
      header('HTTP/1.1 404 Not Found');
      return;

    }

    public function process_post($msg){


      if($this->fake->is_on()){
        $this->mylog->log(__FILE__,__LINE__, sprintf("FAKE MODE POST send [%s] - not sending to panel", $msg->data->keys));

        $usercode = substr($msg->data->keys,0,4);
        $alarmcmd = substr($msg->data->keys,4,1);

        $this->mylog->log(__FILE__,__LINE__, sprintf("FAKE MODE usercode [%s] alalarmcmd[%s]", $usercode, $alarmcmd));

        switch($alarmcmd){

          case "1":
            $this->fake->send_disarm();
          break;

          case "2":
            $this->fake->send_armaway();
          break;

          case "3":
            $this->fake->send_armstay();
          break;

          default:
            $this->mylog->log(__FILE__,__LINE__, sprintf("FAKE MODE unknown command [%s]", $alarmcmd));

            header("HTTP/1.1 204 OK");

          return;

        }
        
        header("HTTP/1.1 204 OK");

        return;
      }


      if(strcmp($msg->path, "/api/v1/alarmdecoder/send") == 0){
        //$fp = fsockopen("macdaddy", 10000, $errno, $errstr, 30);
        $fp = fsockopen("192.168.2.45", 10000, $errno, $errstr, 30);

        if (!$fp) {
           header("HTTP/1.1 422 Unprocessable Entity");
           echo "$errstr ($errno)<br />\n";
           return;
        }

        $this->mylog->log(__FILE__,__LINE__, sprintf("POST send %s\n", $msg->data->keys));

        fwrite($fp, $msg->data->keys);
        fclose($fp);

        header("HTTP/1.1 204 OK");

        return;
      }

    
      header('HTTP/1.1 404 Not Found');
      return;

    }

    public function serve() {


        $myMsg = new AlarmDecoderMsg($this->mylog,$this->db); // convert message into something usable
        $myMsg->dump();


        if(!$myMsg->validkey){
            $this->mylog->log(__FILE__,__LINE__,"sending not authorized\n");
            header('HTTP/1.1 403 Forbidden');
            return;
        }

        $this->mylog->log(__FILE__,__LINE__,sprintf("here \n"));

        switch($myMsg->method){

          case "GET":
            $this->process_get($myMsg);
          break;

          case "POST":
            $this->process_post($myMsg);
          break;

          default:
            header('HTTP/1.1 404 Not Found');
          break;
        }
return;
      
    }
        
  }

?>
