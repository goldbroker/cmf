TESTING_SCRIPTS_DIR=vendor/symfony-cmf/testing/bin
CONSOLE=${TESTING_SCRIPTS_DIR}/console
VERSION=dev-master
ifdef BRANCH
	VERSION=dev-${BRANCH}
endif

list:
	@echo 'test:             will run all tests'
	@echo 'unit_tests:       will run unit tests only'
	@echo 'functional_tests: will run functional tests only'

.PHONY: test
test: unit_tests functional_tests
unit_tests: cmf_unit_tests sonata_unit_tests
functional_tests: sonata_functional_tests

cmf_unit_tests:
	@echo
	@echo '+++ run CMF unit tests +++'
	@vendor/bin/simple-phpunit --testsuite "unit tests"

sonata_unit_tests:
	@echo
	@echo '+++ run sonata unit tests +++'
	@vendor/bin/simple-phpunit --configuration tests/Sonata/DoctrinePHPCRAdminBundle/phpunit.xml.dist --testsuite "unit tests"

sonata_functional_tests:
	@vendor/bin/simple-phpunit --configuration tests/Sonata/DoctrinePHPCRAdminBundle/phpunit.xml.dist --testsuite "functional tests"

cmf_functional_tests: functional_tests_block functional_tests_content functional_tests_core functional_tests_media functional_tests_menu functional_tests_resource functional_tests_resource_rest functional_tests_sonata_phpcr_admin_integration

functional_tests_block:
	@vendor/bin/simple-phpunit --testsuite "functional tests" tests/Bundle/BlockBundle

functional_tests_content:
	@vendor/bin/simple-phpunit --testsuite "functional tests" tests/Bundle/ContentBundle

functional_tests_core:
	@vendor/bin/simple-phpunit --testsuite "functional tests" tests/Bundle/CoreBundle

functional_tests_media:
	@vendor/bin/simple-phpunit --testsuite "functional tests" tests/Bundle/MediaBundle

functional_tests_menu:
	@vendor/bin/simple-phpunit --testsuite "functional tests" tests/Bundle/MenuBundle

functional_tests_resource:
	@vendor/bin/simple-phpunit --testsuite "functional tests" tests/Bundle/ResourceBundle

functional_tests_resource_rest:
	@vendor/bin/simple-phpunit --testsuite "functional tests" tests/Bundle/ResourceRestBundle

functional_tests_sonata_phpcr_admin_integration:
	@vendor/bin/simple-phpunit --testsuite "functional tests" tests/Bundle/SonataPhpcrAdminIntegrationBundle

functional_tests_setup:
	@if [ "${CONSOLE}" = "" ]; then echo "Console executable missing"; exit 1; fi
	@echo
	@echo '+++ create PHPCR +++'
	@${CONSOLE} doctrine:phpcr:init:dbal --drop --force
	@${CONSOLE} doctrine:phpcr:repository:init

functional_tests_teardown:
	@${CONSOLE} doctrine:database:drop --force

