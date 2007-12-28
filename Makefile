PHPUNIT=`which phpunit`
PHPDOC=`which phpdoc`
DOXYGEN=`which doxygen`
PHPCS=`which phpcs`

test: test-php

test-php:
	mkdir -p Documentation/test
	${PHPUNIT} --report Documentation/test/codecoverage/ \
                --log-xml Documentation/test/log.xml \
                --testdox-html Documentation/test/testdox.html \
                --log-pmd Documentation/test/pmd.xml \
                --log-metrics Documentation/test/metrics.xml \
                AllTests |tee Documentation/test/testoutput.txt	


doc:
	rm -Rf Documentation/Timeclock
	mkdir -p Documentation/Timeclock
	echo Building Docs
	${PHPDOC} -d . -t Documentation/Timeclock -c |tee Documentation/Timeclock.build.txt

style:
	${PHPCS} --standard=PHPCS --report=checkstyle  . > Documentation/Timeclock/checkstyle.xml
