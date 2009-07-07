#!/bin/bash

#  $Id$

ROOT_DIR=`pwd`

SVN_SERVER=https://svn.hugllc.com/

COM_NAME=$1
COM_VERSION=$2

if [ x$COM_NAME == "x" ] || [ x$COM_VERSION == "x" ]; then
    echo "Usage:  $0 <component> <release version>"
    exit;
fi


for SERVER in downloads01.hugllc.com downloads02.hugllc.com downloads04.hugllc.com downloads05.hugllc.com
do
    rsync -av rel/${COM_NAME}-${COM_VERSION}.* ${SERVER}:/hug/www/hugllc/downloads/Joomla/Timeclock/
done
