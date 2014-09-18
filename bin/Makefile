BASEDIR=`pwd`/

all: composer build-setup
	cd .. && ${BASEDIR}bin/composer update
	cd .. && ${BASEDIR}bin/composer install
	
	
build-setup: phantomjs jsdoc jslint jasmine-node jshint 


phpunit:
	wget https://phar.phpunit.de/phpunit.phar
	chmod +x phpunit.phar
	mv phpunit.phar $(BASE_DIR)phpunit

phploc:
	wget https://phar.phpunit.de/phploc.phar
	chmod +x phploc.phar
	mv phploc.phar $(BASE_DIR)phploc

phpcs:
	wget https://github.com/squizlabs/PHP_CodeSniffer/releases/download/2.0.0a2/phpcs.phar
	chmod +x phpcs.phar
	mv phpcs.phar $(BASE_DIR)phpcs
	
hhvm-wrapper:
	wget https://phar.phpunit.de/hhvm-wrapper.phar
	chmod +x hhvm-wrapper.phar
	mv hhvm-wrapper.phar $(BASE_DIR)hhvm-wrapper

phpcpd:
	wget https://phar.phpunit.de/phpcpd.phar
	chmod +x phpcpd.phar
	mv phpcpd.phar $(BASE_DIR)phpcpd

phpdox:
	wget http://phpdox.de/releases/phpdox.phar
	chmod +x phpdox.phar
	mv phpdox.phar $(BASE_DIR)phpdox

behat:
	wget https://github.com/downloads/Behat/Behat/behat.phar
	chmod +x behat.phar
	mv behat.phar $(BASE_DIR)behat
	
phpdcd:
	wget https://phar.phpunit.de/phpdcd.phar
	chmod +x phpdcd.phar
	mv phpdcd.phar $(BASE_DIR)phpdcd
	
composer:
	curl -sS https://getcomposer.org/installer | php -- --install-dir=./
	mv $(BASE_DIR)composer.phar $(BASE_DIR)composer
	
jasmine-node:
	rm -f jasmine-node
	npm install jasmine-node
	ln -s ../node_modules/jasmine-node/jasmine-node ./jasmine-node
	
jshint:
	rm -f jshint
	npm install jshint
	ln -s ../node_modules/jshint/jshint ./jshint

jsdoc:
	rm -f jsdoc
	npm install jsdoc
	ln -s ../node_modules/jsdoc/jsdoc.js ./jsdoc

jslint:
	rm -f jslint
	npm install jslint
	ln -s ../node_modules/jslint/jslint.js ./jslint

phantomjs:
	rm -f phantomjs
	npm install phantomjs
	ln -s ../node_modules/phantomjs/phantomjs ./phantomjs
