#!/bin/bash

GIT_SERVER=git://git.hugllc.com/

BZIP2="bzip2"
GZIP="gzip"
MD5SUM="md5sum"
SHA1SUM="sha1sum"
ROOT_DIR=`pwd`

COM_NAME=$1
COM_VERSION=$2

if [ x$COM_NAME == "x" ] || [ x$COM_VERSION == "x" ]; then
    echo "Usage:  $0 <component> <release version>"
    exit;
fi

GIT_TAR="git archive --format=tar --prefix=${COM_NAME}-${COM_VERSION}/ v${COM_VERSION} "
GIT_ZIP="git archive --format=zip --prefix=${COM_NAME}-${COM_VERSION}/ v${COM_VERSION} "


mkdir -p ${ROOT_DIR}/rel
cd ${COM_NAME}

rm -Rf ${ROOT_DIR}/rel/${COM_NAME}-${COM_VERSION}*

echo Exporting ${COM_NAME} version ${COM_VERSION}
#svn export ${SVN_SERVER}/HUGnet/tags/${COM_NAME}/${COM_VERSION} ${COM_NAME}-$COM_VERSION
#git archive --format=tar --prefix=${COM_NAME}-${COM_VERSION}/ v${COM_VERSION} | (cd ${ROOT_DIR}/rel/ && tar xf -) 

cd ${ROOT_DIR}


${GIT_ZIP} > ${ROOT_DIR}/rel/${COM_NAME}-${COM_VERSION}.zip
cd ${ROOT_DIR}/rel

FILES=`ls ${COM_NAME}-${COM_VERSION}.*`
${MD5SUM} ${FILES}  > ${COM_NAME}-${COM_VERSION}.md5
${SHA1SUM} ${FILES}  > ${COM_NAME}-${COM_VERSION}.sha1

for FILE in ${FILES}
do
    echo "Signing ${FILE}"
    if [ ${FILE} != ${COM_NAME}-${COM_VERSION}.md5 ] && [ ${FILE} != ${COM_NAME}-${COM_VERSION}.sha1 ]; then
       gpg2 -b ${FILE}
    fi
done
