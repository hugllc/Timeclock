#!/bin/sh

SED=`which sed`

for file in `find ./${dir} -iname "*.php"|grep -v contrib |grep -v Joomla |grep -v "tests/joomla" |grep -v 'bin' |grep -v build |grep -v composer`; do
    ${SED} -i'' "s/@version    [ ]*Release[ ~a-zA-Z0-9:.$]*/@version    Release: ${1}/g" ${file}
done
 
for file in `find ./${dir} -iname "*.js"|grep -v contrib |grep -v Joomla |grep -v "tests/joomla" |grep -v 'bin' |grep -v build |grep -v composer`; do
    ${SED} -i'' "s/@version    [ ]*Release[ ~a-zA-Z0-9:.$]*/@version    Release: ${1}/g" ${file}
done

#for file in `find ./${dir} -iname "*.php"|grep -v contrib |grep -v Joomla |grep -v "tests/joomla" |grep -v 'bin' |grep -v build |grep -v composer`; do
#    ${SED} -i'' "s/@version    [ ]*SVN[ ~a-zA-Z0-9:.$]*/@version    GIT: $Id$/g" ${file}
#done

#for file in `find ./${dir} -iname "*.js"|grep -v contrib |grep -v Joomla |grep -v "tests/joomla" |grep -v 'bin' |grep -v build |grep -v composer`; do
#    ${SED} -i'' "s/@version    [ ]*SVN[ ~a-zA-Z0-9:.$]*/@version    GIT: $Id$/g" ${file}
#done

for file in `find ./${dir} -iname "*.xml"|grep -v contrib |grep -v Joomla |grep -v "tests/joomla" |grep -v 'bin' |grep -v build |grep -v composer`; do
    ${SED} -i'' "s/^[[:space:]]*<version>[ ~a-zA-Z0-9:.$]*<\/version>/    <version>${1}<\/version>/g" ${file}
done

cat << EOF > Version.mk
# This is automatically created.  Do not EDIT.
PACKAGE_VERSION := ${1}
EOF
