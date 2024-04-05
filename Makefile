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
	@echo
	@echo '+++ run sonata functional tests +++'
	@vendor/bin/simple-phpunit --configuration tests/Sonata/DoctrinePHPCRAdminBundle/phpunit.xml.dist --testsuite "functional tests"

cmf_functional_tests: functional_tests_block functional_tests_teardown

functional_tests_block:
	export KERNEL_CLASS=Tests\Symfony\Cmf\Bundle\BlockBundle\Fixtures\App\Kernel
	@vendor/bin/simple-phpunit --testsuite "functional tests" test\Bundle\BlockBundle

#
#functional_tests_phpcr_content:
#	PACKAGE=symfony-cmf/content-bundle
#    export KERNEL_CLASS=Tests\Symfony\Cmf\Bundle\ContentBundle\Fixtures\App\Kernel
#functional_tests_phpcr_content: functional_tests_phpcr
#
#functional_tests_phpcr_media:
#	PACKAGE=symfony-cmf/media-bundle
#    export KERNEL_CLASS=Tests\Symfony\Cmf\Bundle\MediaBundle\Fixtures\App\Kernel
#functional_tests_phpcr_media: functional_tests_phpcr
#

functional_tests_setup:
	@if [ "${CONSOLE}" = "" ]; then echo "Console executable missing"; exit 1; fi
	@echo
	@echo '+++ create PHPCR +++'
	@${CONSOLE} doctrine:phpcr:init:dbal --drop --force
	@${CONSOLE} doctrine:phpcr:repository:init

functional_tests_teardown:
	@${CONSOLE} doctrine:database:drop --force

