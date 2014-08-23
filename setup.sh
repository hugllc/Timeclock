#!/bin/bash

GIT_SERVER=git://git.hugllc.com/

BASEDIR=$1
ROOTDIR=`pwd`
if [ x$BASEDIR == "x" ]; then
    echo "Usage:  $0 <web base dir>"
    exit;
fi

mv ${BASEDIR}/components/com_timeclock ${BASEDIR}/components/com_timeclock.old
ln -s ${ROOTDIR}/com_timeclock/site ${BASEDIR}/components/com_timeclock

mv ${BASEDIR}/administrator/components/com_timeclock  ${BASEDIR}/administrator/components/com_timeclock.old
ln -s ${PWD}/com_timeclock/admin  ${BASEDIR}/administrator/components/com_timeclock

mv ${BASEDIR}/modules/mod_timeclockinfo ${BASEDIR}/modules/mod_timeclockinfo.old
ln -s ${PWD}/mod_timeclockinfo ${BASEDIR}/modules/mod_timeclockinfo

mv ${BASEDIR}/plugins/user/timeclock ${BASEDIR}/plugins/user/timeclock.old
ln -s ${PWD}/plg_user_timeclock ${BASEDIR}/plugins/user/timeclock

for file in en-GB.plg_user_timeclock.ini en-GB.plg_user_timeclock.sys.ini
do
    mv ${BASEDIR}/administrator/language/en-GB/${file} ${BASEDIR}/administrator/language/en-GB/${file}.old
    ln -s ${PWD}/plg_user_timeclock/language/en-GB/${file} ${BASEDIR}/administrator/language/en-GB/${file}
done

for file in en-GB.com_timeclock.sys.ini en-GB.com_timeclock.ini
do
    mv ${BASEDIR}/administrator/language/en-GB/${file} ${BASEDIR}/administrator/language/en-GB/${file}.old
    ln -s ${PWD}/com_timeclock/administrator/language/en-GB/${file} ${BASEDIR}/administrator/language/en-GB/${file}
done

for file in en-GB.mod_timeclockinfo.sys.ini en-GB.mod_timeclockinfo.ini
do
    mv ${BASEDIR}/language/en-GB/${file} ${BASEDIR}/language/en-GB/${file}.old
    ln -s ${PWD}/mod_timeclockinfo/language/en-GB/${file} ${BASEDIR}/language/en-GB/${file}
done

