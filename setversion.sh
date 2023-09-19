#!/bin/bash

SED=`which sed`
MAKE=`which make`
SHA512=`which sha512sum`
SHA256=`which sha256sum`
CUT=`which cut`

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

for file in `find ./${dir} -iname "*.xml"|grep -v contrib |grep -v Joomla |grep -v "tests/joomla" |grep -v 'bin' |grep -v build |grep -v composer |grep -v update.xml`; do
    ${SED} -i'' "s/^[[:space:]]*<version>[ ~a-zA-Z0-9:.$]*<\/version>/    <version>${1}<\/version>/g" ${file}
done

cat << EOF > Version.mk
# This is automatically created.  Do not EDIT.
PACKAGE_VERSION := ${1}
EOF

${MAKE} clean archive > /dev/null
mkdir -p github
cp rel/pkg_timeclock-${1}.zip github/
SHA512SUM=`${SHA512} github/pkg_timeclock-${1}.zip | ${CUT} -d " " -f 1`
SHA256SUM=`${SHA256} github/pkg_timeclock-${1}.zip | ${CUT} -d " " -f 1`
TEMPLATE=`cat update.xml.template`
XML1=${TEMPLATE//__VERSION__/${1}}
XML2=${XML1/__SHA512__/"<sha512>${SHA512SUM}</sha512>"}
XML3=${XML2/__SHA256__/"<sha256>${SHA256SUM}</sha256>"}
echo "${XML3}" > update.xml

echo "Now upload github/pkg_timeclock-${1}.zip to github as a release"
