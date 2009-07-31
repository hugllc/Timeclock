#!/bin/bash

#  $Id: release.sh 1021 2008-03-02 03:05:12Z prices $

ROOT_DIR=`pwd`

COM_NAME=$1
COM_VERSION=$2

if [ x$COM_NAME == "x" ] || [ x$COM_VERSION == "x" ]; then
    echo "Usage:  $0 <component> <release version>"
    exit;
fi

cd ${ROOT_DIR}/${COM_NAME}

git commit -a
echo Tagging the version
git tag -m "Release ${COM_VERSION}" -s v${COM_VERSION} HEAD
git push --tags

