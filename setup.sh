#!/bin/bash

GIT_SERVER=git://git.hugllc.com/

BASEDIR=$1
ROOTDIR=`pwd`
if [ x$BASEDIR == "x" ]; then
    echo "Usage:  $0 <basedir>"
    exit;
fi

mv ${BASEDIR}/components/com_timeclock ${BASEDIR}/components/com_timeclock.old
ln -s ${ROOTDIR}/ComTimeclock/site ${BASEDIR}/components/com_timeclock

mv ${BASEDIR}/administrator/components/com_timeclock  ${BASEDIR}/administrator/components/com_timeclock.old
ln -s ${PWD}/ComTimeclock/admin  ${BASEDIR}/administrator/components/com_timeclock

mv ${BASEDIR}/modules/mod_timeclockinfo ${BASEDIR}/modules/mod_timeclockinfo.old
ln -s ${PWD}/ComTimeclock/admin/modules/mod_timeclockinfo ${BASEDIR}/modules/mod_timeclockinfo

mv ${BASEDIR}/plugins/user/timeclock ${BASEDIR}/plugins/user/timeclock.old
ln -s ${PWD}/ComTimeclock/admin/plugins/timeclock ${BASEDIR}/plugins/user/timeclock

