# /bin/sh

. ./config.sh

mkcmd (){

  NAME=$1
  BASENAME=`echo $item|awk -F. '{print $1}'`

  echo "#!$LAST/bin/php" > $BASENAME
  echo >> $BASENAME

  cat $NAME >> $BASENAME

  chmod +x $BASENAME

}


if [ "$UNIX" = "Darwin" ]
then
  # this is really bad scripting, but I am tired, trying to get the
  # latest version of php, which will be the last directory in the list
  PHPVERS=`ls -d $PHPBASE/php*`

  # for safety purposes if we don't find anything
  LAST=/usr

  for VER in $PHPVERS
  do
    LAST=$VER
  done

  echo $LAST
else
  LAST=/usr
fi

cd $ALDDIR

LIST="apikey_cntl.php send_cmd.php testboottime.php"

for item in $LIST
do


  mkcmd $item

done
