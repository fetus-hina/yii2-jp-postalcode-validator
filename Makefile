.PHONY: all
all: vendor

.PHONY: test
test: vendor FORCE
	vendor/bin/phpunit

.PHONY: clover.xml
clover.xml: vendor FORCE
	vendor/bin/phpunit --coverage-clover=clover.xml

.PHONY: check-style
check-style: vendor FORCE
	vendor/bin/phpcs --standard=PSR12 src test

.PHONY: fix-style
fix-style: FORCE
	vendor/bin/phpcbf --standard=PSR12 src test

.PHONY: clean
clean:
	rm -rf vendor composer.phar clover.xml

composer.lock: composer.json composer.phar
	./composer.phar update -vvv
	touch $@

vendor: composer.lock composer.phar
	./composer.phar install -vvv
	touch $@

composer.phar:
	curl -sS https://getcomposer.org/installer | php

.PHONY: FORCE
