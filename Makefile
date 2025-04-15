SHELL = /bin/bash
PHPUNIT=`which phpunit`
PHPDOC=`which phpdoc`
DOXYGEN=`which doxygen`
PHPCS=`which phpcs`
SVN=`which svn`
SVN_SERVER=https://svn.hugllc.com
SRC=`pwd`
DEST="${SRC}/Joomla/"

include Version.mk

PKG_WITH_VERSION := pkg_timeclock-${PACKAGE_VERSION}.zip

all: clean archive


archive package: clean rel/pkg_timeclock.zip rel/${PKG_WITH_VERSION}

	
rel/pkg_timeclock.zip: build/pkg_timeclock/pkg_timeclock.xml build/pkg_timeclock/packages/com_timeclock.zip build/pkg_timeclock/packages/plg_user_timeclock.zip build/pkg_timeclock/packages/mod_timeclockinfo.zip 
	mkdir -p rel
	cd build; zip -r ../rel/pkg_timeclock.zip pkg_timeclock

rel/${PKG_WITH_VERSION}: rel/pkg_timeclock.zip
	cp $< $@

build/pkg_timeclock/packages/com_timeclock.zip:
	mkdir -p build/pkg_timeclock/packages
	zip -r build/pkg_timeclock/packages/com_timeclock.zip com_timeclock

build/pkg_timeclock/packages/plg_user_timeclock.zip:
	mkdir -p build/pkg_timeclock/packages
	zip -r build/pkg_timeclock/packages/plg_user_timeclock.zip plg_user_timeclock

build/pkg_timeclock/packages/mod_timeclockinfo.zip:
	mkdir -p build/pkg_timeclock/packages
	zip -r build/pkg_timeclock/packages/mod_timeclockinfo.zip mod_timeclockinfo

build/pkg_timeclock/pkg_timeclock.xml:
	mkdir -p build/pkg_timeclock
	cp pkg_timeclock.xml build/pkg_timeclock/

clean:
	rm -Rf build/* rel

dist-clean: clean
	rm -Rf rel/*.zip 
		
style:
	${PHPCS} -n com_timeclock plg_user_timeclock mod_timeclockinfo

bin:
	$(MAKE) -C bin

up:
	docker compose up

down:
	docker compose down

tests/unit:
	svn co https://github.com/joomla/joomla-cms/trunk/tests/unit

phpgraph: rel/phpgraph.zip
	
	
test: check all
	@phpunit

.PHONY: all check package style clean copy_from_dev bin
