ROOTPATH="/Applications/MAMP/htdocs/phpalarmserver"
SCRIPTPATH="$ROOTPATH/scripts"

. $SCRIPTPATH/env_profile.sh

ps -ef|grep listen.php|grep -v grep

if [ "$?" = "0" ]
then 
  echo "listen server is running, no need to start"
  exit 0
fi

echo "Starting listen.php"
cd $ROOTPATH
php ./listen.php 192.168.2.45 10000 > logs/listen.out 2>&1 &
