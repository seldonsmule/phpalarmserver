# /bin/sh

ROOTDIR=/Applications/MAMP
$ROOTDIR/bin/stop.sh

$ROOTDIR/bin/egcstoplisten.sh
$ROOTDIR/bin/egcstopreadcamlogs.sh
