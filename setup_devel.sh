#!/bin/sh

WWWDIR=$1

if [ ! -e ${WWWDIR}/configuration.php ]; then
	echo "You must supply the web directory. (The directory with Joomla's configuration.php in it)"
	exit 1
fi

DIR=`pwd`

for PART in com_dfprefs com_dfproject com_dfprojecttimeclock com_dfprojectbilling com_dfprojectwcomp
do
	if [ -d ${WWWDIR}/components/${PART} ]; then
		if [ ! -h ${WWWDIR}/components/${PART} ]; then
			echo "Doing components/${PART}"
			rm -Rf ${WWWDIR}/components/${PART}.old
			mv ${WWWDIR}/components/${PART} ${WWWDIR}/components/${PART}.old
			ln -s ${DIR}/$PART ${WWWDIR}/components/${PART}
		fi
	fi
	if [ -d ${WWWDIR}/administrator/components/${PART} ]; then
		if [ ! -h ${WWWDIR}/administrator/components/${PART} ]; then
			echo "Doing administrator/components/${PART}"
			rm -Rf ${WWWDIR}/administrator/components/${PART}.old
			mv ${WWWDIR}/administrator/components/${PART} ${WWWDIR}/administrator/components/${PART}.old
			ln -s ${DIR}/$PART ${WWWDIR}/administrator/components/${PART}
		fi
	fi
done

for PART in mod_dfprojectmenu mod_timeclockytd
do
	if [ -f ${WWWDIR}/modules/${PART}.php ]; then
		if [ ! -h ${WWWDIR}/modules/${PART}.php ]; then
			echo "Doing ${PART}.php"
			rm -Rf ${WWWDIR}/modules/${PART}.php.old
			mv ${WWWDIR}/modules/${PART}.php ${WWWDIR}/modules/${PART}.php.old
			ln -s ${DIR}/$PART/$PART.php ${WWWDIR}/modules/${PART}.php
		fi
	fi
	if [ -f ${WWWDIR}/modules/${PART}.xml ]; then
		if [ ! -h ${WWWDIR}/modules/${PART}.xml ]; then
			echo "Doing ${PART}.xml"
			rm -Rf ${WWWDIR}/modules/${PART}.xml.old
			mv ${WWWDIR}/modules/${PART}.xml ${WWWDIR}/modules/${PART}.xml.old
			ln -s ${DIR}/$PART/$PART.xml ${WWWDIR}/modules/${PART}.xml
		fi
	fi
done
