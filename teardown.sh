#!/bin/bash

GIT_SERVER=git://git.hugllc.com/

BASEDIR=$1
PWD=`pwd`

if [ x$BASEDIR == "x" ]; then
    echo "Usage:  $0 <basedir>"
    exit;
fi

cd ${BASEDIR}/components
rm com_timeclock
mv com_timeclock.old com_timeclock

cd ${BASEDIR}/administrator/components
rm com_timeclock
mv com_timeclock.old com_timeclock

cd ${BASEDIR}/modules
rm mod_timeclockinfo
mv mod_timeclockinfo.old mod_timeclockinfo

cd ${BASEDIR}/plugins/user
rm timeclock
mv timeclock.old timeclock

