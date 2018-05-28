. ./config.sh

BOOTTIME=`$SCRIPTSDIR/uptime.sh`

TIMENOW=`date +"%s"`

TIMEDIFF=`expr $TIMENOW - $BOOTTIME`

BOOTWAIT=300  # we want to wait 5 minutes before allowing

if [ "$TIMEDIFF" -gt "$BOOTWAIT" ] 
then
  echo "$TIMEDIFF secs have passed since boot, we only needed $BOOTWAIT"
else
  echo "Still waiting.  $TIMEDIFF has passed need $BOOTWAIT"
fi
