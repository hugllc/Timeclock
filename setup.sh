#!/bin/bash

GIT_SERVER=git://git.hugllc.com/

BASEDIR=$1
ROOTDIR=`pwd`
if [ x$BASEDIR == "x" ]; then
    echo "Usage:  $0 <web base dir>"
    exit;
fi

if [[ -L "${BASEDIR}/components/com_timeclock" ]]; then
    rm ${BASEDIR}/components/com_timeclock
else
    mv ${BASEDIR}/components/com_timeclock ${BASEDIR}/components/com_timeclock.old
fi
ln -s ${ROOTDIR}/com_timeclock/site ${BASEDIR}/components/com_timeclock

if [[ -L "${BASEDIR}/administrator/components/com_timeclock" ]]; then
    rm ${BASEDIR}/administrator/components/com_timeclock
else
    mv ${BASEDIR}/administrator/components/com_timeclock  ${BASEDIR}/administrator/components/com_timeclock.old
fi
ln -s ${PWD}/com_timeclock/admin  ${BASEDIR}/administrator/components/com_timeclock

if [[ -L "${BASEDIR}/modules/mod_timeclockinfo" ]]; then
    rm ${BASEDIR}/modules/mod_timeclockinfo
else
    mv ${BASEDIR}/modules/mod_timeclockinfo ${BASEDIR}/modules/mod_timeclockinfo.old
fi
ln -s ${PWD}/mod_timeclockinfo ${BASEDIR}/modules/mod_timeclockinfo

if [[ -L "${BASEDIR}/plugins/user/timeclock" ]]; then
    rm ${BASEDIR}/plugins/user/timeclock
else
    mv ${BASEDIR}/plugins/user/timeclock ${BASEDIR}/plugins/user/timeclock.old
fi
ln -s ${PWD}/plg_user_timeclock ${BASEDIR}/plugins/user/timeclock

for file in en-GB.plg_user_timeclock.ini en-GB.plg_user_timeclock.sys.ini
do
    if [[ -L "${BASEDIR}/administrator/language/en-GB/${file}" ]]; then
        rm ${BASEDIR}/administrator/language/en-GB/${file}
    else
        mv ${BASEDIR}/administrator/language/en-GB/${file} ${BASEDIR}/administrator/language/en-GB/${file}.old
    fi
    ln -s ${PWD}/plg_user_timeclock/language/en-GB/${file} ${BASEDIR}/administrator/language/en-GB/${file}
done

for file in en-GB.com_timeclock.sys.ini en-GB.com_timeclock.ini
do
    if [[ -L "${BASEDIR}/administrator/language/en-GB/${file}" ]]; then
        rm ${BASEDIR}/administrator/language/en-GB/${file}
    else
        mv ${BASEDIR}/administrator/language/en-GB/${file} ${BASEDIR}/administrator/language/en-GB/${file}.old
    fi
    ln -s ${PWD}/com_timeclock/administrator/language/en-GB/${file} ${BASEDIR}/administrator/language/en-GB/${file}
done

for file in en-GB.mod_timeclockinfo.sys.ini en-GB.mod_timeclockinfo.ini
do
    if [[ -L "${BASEDIR}/language/en-GB/${file}" ]]; then
        rm ${BASEDIR}/language/en-GB/${file}
    else
        mv ${BASEDIR}/language/en-GB/${file} ${BASEDIR}/language/en-GB/${file}.old
    fi
    ln -s ${PWD}/mod_timeclockinfo/language/en-GB/${file} ${BASEDIR}/language/en-GB/${file}
done

