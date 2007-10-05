#!/bin/bash

#  $Id: com_hugnet.sh 279 2006-10-13 21:14:00Z prices $

ROOT_DIR=`pwd`

SVN_SERVER=https://svn.hugllc.com/

COM_NAME=$1
COM_VERSION=$2

if [ x$COM_NAME == "x" ] || [ x$COM_VERSION == "x" ]; then
    echo "Usage:  $0 <component> <release version>"
    exit;
fi

svn commit ./$COM_NAME
echo Tagging the version
svn -m "Release $COM_VERSOIN" copy ./$COM_NAME ${SVN_SERVER}/Timeclock/tags/${COM_NAME}/${COM_VERSION}


./build.sh $1 $2
