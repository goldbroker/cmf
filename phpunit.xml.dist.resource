<?xml version="1.0" encoding="UTF-8"?>

<phpunit backupGlobals="false"
         backupStaticAttributes="false"
         colors="true"
         failOnWarning="true"
         failOnRisky="true"
         stopOnFailure="false"
         bootstrap="./tests/Bundle/ResourceBundle/bootstrap.php"
>
    <testsuites>
        <testsuite name="functional tests">
            <directory>./Functional</directory>
            <directory>./WebTest</directory>
        </testsuite>
    </testsuites>
</phpunit>
