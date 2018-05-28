# phpalarmserver
PHP Server that works with Nutechsoftware's alarmdecoder socket server.  Provides a RestAPI interface

## BackGround
* PHP Based REST API server to work with AlarmDecoder devices and software
* Built to run native on MacOS
* To have the simpliest MacOS installation utilize a MAMP installation (see below)
* Reason for building over using the Nutechsoftware webapp is that code was designed to run on a Raspberry PI.  When running on a Mac either native or in a container - it requires SSDP services which conflict with MacOS's Bonjoure.  
* Finally decided to replicate the RESP APIs.


## Required software
* Software is PHP assuming a LAMP stack.  This code assumes it is MAMP: https://www.mamp.info/en/
* On local network Nutechsoftware's ser2sock server is running and connected to an alarmdecoder device (Connected to an alarm panel).  https://github.com/nutechsoftware/ser2sock

## Future work
* Update to work on Ubuntu unix as well
* SSL between Smartthings code and MAMP apache server
* Config scripts auto adjust httpd and httpd-ssl conf files
