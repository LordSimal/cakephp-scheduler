<?php
declare(strict_types=1);

$findRoot = function ($root) {
    do {
        $lastRoot = $root;
        $root = dirname($root);
        if (is_dir($root . '/vendor/cakephp/cakephp')) {
            return $root;
        }
    } while ($root !== $lastRoot);
    throw new Exception('Cannot find the root of the application, unable to run tests');
};
$root = $findRoot(__FILE__);
unset($findRoot);
chdir($root);
require $root . '/vendor/cakephp/cakephp/tests/bootstrap.php';

// See setUp() method inside tests
define('PLUGIN_TESTS', $root . DS . 'tests' . DS);
