##TESTING_SCRIPTS_DIR=vendor/symfony-cmf/testing/bin
##CONSOLE=${TESTING_SCRIPTS_DIR}/console
#VERSION=dev-master
#ifdef BRANCH
#	VERSION=dev-${BRANCH}
#endif

list:
	@echo 'test:             will run all tests'
	@echo 'unit_tests:       will run unit tests only'
	@echo 'functional_tests: will run functional tests only'

.PHONY: test
test: unit_tests functional_tests
unit_tests: cmf_unit_tests sonata_unit_tests
functional_tests: sonata_functional_tests cmf_functional_tests

cmf_unit_tests:
	@vendor/bin/simple-phpunit --configuration phpunit.xml.dist.cmf_unit --testsuite "unit tests"

sonata_unit_tests:
	@vendor/bin/simple-phpunit --configuration tests/Sonata/DoctrinePHPCRAdminBundle/phpunit.xml.dist --testsuite "unit tests"

sonata_functional_tests:
	@vendor/bin/simple-phpunit --configuration tests/Sonata/DoctrinePHPCRAdminBundle/phpunit.xml.dist --testsuite "functional tests"

cmf_functional_tests: functional_tests_block functional_tests_content functional_tests_media functional_tests_menu functional_tests_resource functional_tests_resource_rest functional_tests_seo functional_tests_sonata_phpcr_admin_integration

functional_tests_block:
	@vendor/bin/simple-phpunit --configuration phpunit.xml.dist.block --testsuite "functional tests" tests/Bundle/BlockBundle

functional_tests_content:
	@vendor/bin/simple-phpunit --configuration phpunit.xml.dist.content --testsuite "functional tests" tests/Bundle/ContentBundle

functional_tests_media:
	@vendor/bin/simple-phpunit --configuration phpunit.xml.dist.media --testsuite "functional tests" tests/Bundle/MediaBundle

functional_tests_menu:
	@vendor/bin/simple-phpunit --configuration phpunit.xml.dist.menu --testsuite "functional tests" tests/Bundle/MenuBundle

functional_tests_resource:
	@vendor/bin/simple-phpunit --configuration phpunit.xml.dist.resource --testsuite "functional tests" tests/Bundle/ResourceBundle

functional_tests_resource_rest:
	@vendor/bin/simple-phpunit --configuration phpunit.xml.dist.resource_rest --testsuite "functional tests" tests/Bundle/ResourceRestBundle

functional_tests_seo:
	@vendor/bin/simple-phpunit --configuration phpunit.xml.dist.seo_phpcr --testsuite "functional tests" tests/Bundle/SeoBundle
	@vendor/bin/simple-phpunit --configuration phpunit.xml.dist.seo_orm --testsuite "functional tests" tests/Bundle/SeoBundle

functional_tests_sonata_phpcr_admin_integration:
	@vendor/bin/simple-phpunit --configuration phpunit.xml.dist.sonata_phpcr_admin_integration --testsuite "functional tests" tests/Bundle/SonataPhpcrAdminIntegrationBundle
