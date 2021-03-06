.PHONY: all
all: vendor

.PHONY: test
test: vendor
	vendor/bin/phpunit

.PHONY: check-style
check-style: vendor
	vendor/bin/phpcs

.PHONY: fix-style
fix-style:
	vendor/bin/phpcbf

.PHONY: clean
clean:
	rm -rf vendor composer.phar

composer.lock: composer.json composer.phar
	./composer.phar update -vvv
	touch $@

vendor: composer.lock composer.phar
	./composer.phar install -vvv
	touch $@

composer.phar:
	curl -sS https://getcomposer.org/installer | php

.PHONY: FORCE
