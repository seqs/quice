<?php

!isset($config) or $config = array();
!isset($packages) or $packages = array();
!isset($components) or $components = array();

$_config = array(
    'env' => 'dev',
    'tpl' => array('dir' => __DIR__ . '/tpl')
);

$_packages = array(
    'Quice' => __DIR__ . '/src'
);

$_components = array(
    // Globals
    'FrontAction' => array(
        'class' => 'Quice\Action\FrontAction',
        'properties' => array(
            'request' => 'Request',
            'response' => 'Response',
            'container' => 'ThisContainer'
        ),
    ),
    'Request' => array('class' => 'Quice\Http\Request'),
    'Response' => array(
        'class' => 'Quice\Http\Response',
        'properties' => array('renderer' => 'Template'),
    ),
    'Template' => array(
        'class' => 'Quice\Template\TemplateEngine',
        'properties' => array('dir' => '%tpl.dir%'),
    ),
);

$config = array_merge($_config, $config);
$packages = array_merge($_packages, $packages);
$components = array_merge($_components, $components);

error_reporting($config['env'] == 'dev' ? E_ALL|E_STRICT : 0);

require_once __DIR__ . '/src/Quice/Package/Autoloader.php';

use Quice\Package\Autoloader;
use Quice\Container\Injector;

try {
    $loader = new Autoloader();
    $loader->register($packages);

    $injector = new Injector($components, $config);
    $injector->getComponent('FrontAction')->execute();
} catch(Exception $e) {
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

?>