PHPUNIT=`which phpunit`
PHPDOC=`which phpdoc`
DOXYGEN=`which doxygen`
PHPCS=`which phpcs`
SVN=`which svn`
SVN_SERVER=https://svn.hugllc.com

all: test-php

UItest: UItest-CoreUI

UItest-CoreUI:
	cd CoreUI/test; ${PHPUNIT} --report ../Documentation/test/CoreUI/ homeTest.php

test: test-php

test-php:
	mkdir -p Documentation/test
	${PHPUNIT} --coverage-html Documentation/test/codecoverage/ \
                --log-junit Documentation/test/log.xml \
                --testdox-html Documentation/test/testdox.html \
		ComTimeclock/test/ |tee Documentation/test/testoutput.txt


test-unit: tests/joomla
	mkdir -p Documentation/test
	${PHPUNIT} --coverage-html Documentation/test/codecoverage/ \
                --log-junit Documentation/test/log.xml \
                --testdox-html Documentation/test/testdox.html \
                tests/unit/suite |tee Documentation/test/testoutput.txt

doc: doc-php

doc-php: doc-ComTimeclock

doc-ComTimeclock:
	rm -Rf Documentation/ComTimeclock
	mkdir -p Documentation/ComTimeclock
	echo Building ComTimeclock Docs
	${PHPDOC} -d ComTimeclock -t Documentation/ComTimeclock |tee Documentation/ComTimeclock.build.txt


style: style-ComTimeclock

style-ComTimeclock:
	${PHPCS} -n ComTimeclock

update:
	${SVN} update
	${SVN} update ../JoomlaMock


tests/joomla:
	mkdir -p tests
	svn checkout http://joomlacode.org/svn/joomla/development/trunk/ tests/joomla --username anonymous --password ''

