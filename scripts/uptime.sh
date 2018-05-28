#
# get the uptime and return it
. ./config.sh

if [ "$UNIX" = "Linux" ]
then
  UP=`uptime -s`
  BOOTTIME=`date -d "$UP" +%s`

else

  #macos is lame on how we get the uptime.  overly complex

  BOOTTIME=`sysctl -n kern.boottime |awk '{print $4}'`
  BOOTTIME=`echo ${BOOTTIME%?}`  # internet is wonderfull.  
                               # this strips off the last char
                               # which was a ','
fi

echo $BOOTTIME
