#!/bin/sh

#  $Id: com_hugnet.sh 279 2006-10-13 21:14:00Z prices $

ROOT_DIR=`pwd`

SVN_SERVER=https://svn.hugllc.com/

COM_NAME=$1
COM_VERSION=$2

if [ x$COM_NAME == "x" ] || [ x$COM_VERSION == "x" ]; then
    echo "Usage:  $0 <component> <release version>"
    exit;
fi

mkdir -p rel
cd rel

rm -Rf ${COM_NAME}-${COM_VERSION}*

echo Exporting ${COM_NAME} version ${COM_VERSION}
svn export ${SVN_SERVER}/0007/tags/${COM_NAME}/${COM_VERSION} ${COM_NAME}-$COM_VERSION


cd $ROOT_DIR/rel
zip -r ${COM_NAME}-${COM_VERSION}.zip ${COM_NAME}-$COM_VERSION

rm -Rf ${COM_NAME}-${COM_VERSION}
