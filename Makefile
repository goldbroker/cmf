list:
	@echo 'test:             will run all tests'
	@echo 'unit_tests:       will run unit tests only'
	@echo 'functional_tests: will run functional tests only'

.PHONY: test
test: unit_tests functional_tests
unit_tests: cmf_unit_tests sonata_unit_tests
functional_tests: sonata_functional_tests cmf_functional_tests

cmf_unit_tests:
	docker compose exec php-fpm bash -c 'vendor/bin/simple-phpunit --configuration phpunit.xml.dist.cmf_unit --testsuite "unit tests"'

sonata_unit_tests:
	docker compose exec php-fpm bash -c 'vendor/bin/simple-phpunit --configuration tests/Sonata/DoctrinePHPCRAdminBundle/phpunit.xml.dist --testsuite "unit tests"'

sonata_functional_tests:
	docker compose exec php-fpm bash -c 'vendor/bin/simple-phpunit --configuration tests/Sonata/DoctrinePHPCRAdminBundle/phpunit.xml.dist --testsuite "functional tests"'

cmf_functional_tests: functional_tests_block functional_tests_content functional_tests_media functional_tests_menu functional_tests_resource functional_tests_resource_rest functional_tests_seo functional_tests_sonata_phpcr_admin_integration

functional_tests_block:
	docker compose exec php-fpm bash -c 'vendor/bin/simple-phpunit --configuration phpunit.xml.dist.block --testsuite "functional tests" tests/Bundle/BlockBundle'

functional_tests_content:
	docker compose exec php-fpm bash -c 'vendor/bin/simple-phpunit --configuration phpunit.xml.dist.content --testsuite "functional tests" tests/Bundle/ContentBundle'

functional_tests_core:
	docker compose exec php-fpm bash -c 'vendor/bin/simple-phpunit --configuration phpunit.xml.dist.core --testsuite "functional tests" tests/Bundle/CoreBundle'

functional_tests_media:
	docker compose exec php-fpm bash -c 'vendor/bin/simple-phpunit --configuration phpunit.xml.dist.media --testsuite "functional tests" tests/Bundle/MediaBundle'

functional_tests_menu:
	docker compose exec php-fpm bash -c 'vendor/bin/simple-phpunit --configuration phpunit.xml.dist.menu --testsuite "functional tests" tests/Bundle/MenuBundle'

functional_tests_resource:
	docker compose exec php-fpm bash -c 'vendor/bin/simple-phpunit --configuration phpunit.xml.dist.resource --testsuite "functional tests" tests/Bundle/ResourceBundle'

functional_tests_resource_rest:
	docker compose exec php-fpm bash -c 'vendor/bin/simple-phpunit --configuration phpunit.xml.dist.resource_rest --testsuite "functional tests" tests/Bundle/ResourceRestBundle'

functional_tests_seo:
	docker compose exec php-fpm bash -c 'vendor/bin/simple-phpunit --configuration phpunit.xml.dist.seo_phpcr --testsuite "functional tests" tests/Bundle/SeoBundle'
	docker compose exec php-fpm bash -c 'vendor/bin/simple-phpunit --configuration phpunit.xml.dist.seo_orm --testsuite "functional tests" tests/Bundle/SeoBundle'

functional_tests_sonata_phpcr_admin_integration:
	docker compose exec php-fpm bash -c 'vendor/bin/simple-phpunit --configuration phpunit.xml.dist.sonata_phpcr_admin_integration --testsuite "functional tests" tests/Bundle/SonataPhpcrAdminIntegrationBundle'

dev-up:
	docker compose up -d --build --remove-orphans

dev-down:
	docker compose down --remove-orphans

shell-php:
	docker compose exec php-fpm bash
