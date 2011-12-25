<?php

$config = array(
    'env' => 'dev',
    'tpl' => array('dirs' => array('default' => __DIR__ . '/tpl'))
);

$packages = array(
    'Quice\Demo' => __DIR__ . '/src'
);

$components = array(
    // Modules
    'CoreModule' => array('class' => 'Quice\Demo\DemoModule'),
    'DemoModule' => array('class' => 'Quice\Demo\DemoModule')
);

require __DIR__ . '/../boot.php';

?>