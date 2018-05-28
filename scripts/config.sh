# /bin/sh

# used to setup common variable depending on the OS
# switching between Macos and Ubuntu
# 
# You may have to change the ROOTDIR for your specific location
#

UNIX=`uname -s`

if [ "$UNIX" = "Darwin" ]
then
  ROOTDIR=/Applications/MAMP
  BINDIR=$ROOTDIR/bin
  HTDOCSDIR=$ROOTDIR/htdocs
  ALDDIR=$HTDOCSDIR/phpalarmserver
  SCRIPTSDIR=$ALDDIR/scripts
  PHPBASE=$BINDIR/php
else
  ROOTDIR="/var/www/html"
  HTDOCSDIR=$ROOTDIR
  ALDDIR=$HTDOCSDIR/phpalarmserver
  BINDIR=$ALDDIR/bin
  SCRIPTSDIR=$ALDDIR/scripts
  PHPBASE=/usr/bin/php
fi


export UNIX
export ROOTDIR
export HTDOCSDIR
export ALDDIR
export BINDIR
export SCRIPTSDIR
export PHPBASE

