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
	${PHPUNIT} --report Documentation/test/codecoverage/ \
                --log-xml Documentation/test/log.xml \
                --testdox-html Documentation/test/testdox.html \
                --log-pmd Documentation/test/pmd.xml \
                --log-metrics Documentation/test/metrics.xml \
                TimeclockTests |tee Documentation/test/testoutput.txt	

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
	
