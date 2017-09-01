# /bin/sh

ROOTDIR=/Applications/MAMP
BINDIR=$ROOTDIR/bin
HTDOCSDIR=$ROOTDIR/htdocs
ALDDIR=$HTDOCSDIR/phpalarmserver
SCRIPTSDIR=$ALDDIR/scripts

rm $BINDIR/egc*

rm $HTDOCSDIR/index.php

rm $HTDOCSDIR/.htaccess

rm $ROOTDIR/logs/phpalarmserver


rm $ROOTDIR/conf/apache/server.crt
rm $ROOTDIR/conf/apache/server.key

./clean.sh
