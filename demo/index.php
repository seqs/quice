<?php

$config = array(
    'env' => 'dev',
    'tpl' => array('dir' => __DIR__ . '/tpl')
);

$packages = array(
    'Demo' => __DIR__ . '/src'
);

$components = array(
    // Modules
    'CoreModule' => array('class' => 'Demo\Example\ExampleModule')
);

require __DIR__ . '/../boot.php';

?>