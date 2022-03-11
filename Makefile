.PHONY: all
all: vendor

.PHONY: test
test: vendor
	vendor/bin/phpunit

.PHONY: check-style
check-style: check-style-phpcs check-style-phpstan

.PHONY: check-style-phpcs
check-style-phpcs: vendor
	vendor/bin/phpcs

.PHONY: check-style-phpstan
check-style-phpstan: vendor
	vendor/bin/phpstan analyze --memory-limit=1G

.PHONY: fix-style
fix-style:
	vendor/bin/phpcbf

.PHONY: clean
clean:
	rm -rf vendor composer.phar

vendor: composer.lock composer.phar
	./composer.phar install
	touch $@

composer.phar:
	curl -sS https://getcomposer.org/installer | php

.PHONY: FORCE
