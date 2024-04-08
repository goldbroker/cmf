<?php

declare(strict_types=1);

use Doctrine\Deprecations\Deprecation;

use Tests\Symfony\Cmf\Bundle\CoreBundle\Fixtures\App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/*
 * fix encoding issue while running text on different host with different locale configuration
 */
setlocale(\LC_ALL, 'en_US.UTF-8');

require_once __DIR__.'/../../../vendor/autoload.php';

if (class_exists(Deprecation::class)) {
    Deprecation::enableWithTriggerError();
}

$_ENV['KERNEL_CLASS'] = Kernel::class;
putenv(sprintf('KERNEL_CLASS=%s', $_ENV['KERNEL_CLASS']));

require_once __DIR__.'/../../../vendor/symfony-cmf/testing/bootstrap/bootstrap.php';

$application = new Application(new Kernel());
$application->setAutoExit(false);

// Load fixtures of the AppTestBundle
$input = new ArrayInput([
    'command' => 'doctrine:phpcr:init:dbal',
    '--drop' => true,
    '--force' => true,
]);
$application->run($input, new ConsoleOutput());

$input = new ArrayInput([
    'command' => 'doctrine:phpcr:repository:init',
]);
$application->run($input, new ConsoleOutput());
