#######################################################
# DO NOT EDIT THIS FILE!                              #
#                                                     #
# It's auto-generated by symfony-cmf/dev-kit package. #
#######################################################

############################################################################
# This file is part of the Symfony CMF package.                            #
#                                                                          #
# (c) 2011-2017 Symfony CMF                                                #
#                                                                          #
# For the full copyright and license information, please view the LICENSE  #
# file that was distributed with this source code.                         #
############################################################################

TESTING_SCRIPTS_DIR=vendor/symfony-cmf/testing/bin
CONSOLE=${TESTING_SCRIPTS_DIR}/console
VERSION=dev-master
ifdef BRANCH
	VERSION=dev-${BRANCH}
endif
PACKAGE=symfony-cmf/block-bundle
export KERNEL_CLASS=Symfony\Cmf\Bundle\BlockBundle\Tests\Fixtures\App\Kernel
list:
	@echo 'test:                    will run all tests'
	@echo 'unit_tests:               will run unit tests only'
	@echo 'functional_tests_phpcr:  will run functional tests with PHPCR'

include ${TESTING_SCRIPTS_DIR}/make/test_installation.mk
#include ${TESTING_SCRIPTS_DIR}/make/unit_tests.mk
#include ${TESTING_SCRIPTS_DIR}/make/functional_tests_phpcr.mk

.PHONY: test
test: unit_tests functional_tests_phpcr

unit_tests:
	@echo
	@echo '+++ run unit tests +++'
ifeq ($(HAS_XDEBUG), 0)
	@vendor/bin/simple-phpunit --coverage-clover build/logs/clover.xml --testsuite "unit tests"
else
	@vendor/bin/simple-phpunit --testsuite "unit tests"
endif


#functional_tests_phpcr:
#	@if [ "${CONSOLE}" = "" ]; then echo "Console executable missing"; exit 1; fi
#	@echo
#	@echo '+++ create PHPCR +++'
#	@${CONSOLE} doctrine:phpcr:init:dbal --drop --force
#	@${CONSOLE} doctrine:phpcr:repository:init
#	@echo '+++ run PHPCR functional tests +++'
#ifeq ($(HAS_XDEBUG), 0)
#	@vendor/bin/simple-phpunit --coverage-clover build/logs/clover.xml --testsuite "functional tests with phpcr"
#else
#	@vendor/bin/simple-phpunit --testsuite "functional tests with phpcr"
#endif
#	@${CONSOLE} doctrine:database:drop --force
