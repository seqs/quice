<?php

!isset($config) and $config = array();
!isset($packages) and $packages = array();
!isset($components) and $components = array();

$_config = array(
    'env' => 'dev',
    'tpl' => array('dir' => __DIR__ . '/tpl')
);

$_packages = array(
    'Quice' => __DIR__ . '/src'
);

$_components = array(
    // Globals
    'Dispatcher' => array(
        'class' => 'Quice\Action\ActionDispatcher',
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
        'properties' => array(
            'dir' => '%tpl.dir%',
            'slots' => array(
                'url' => 'UrlHelper',
                'request' => 'Request',
                'trans' => 'Trans',
                'html' => 'HtmlHelper',
            ),
        ),
    ),

    'UrlHelper' => array(
        'class' => 'Quice\Helper\UrlHelper',
        'properties' => array('request' => 'Request'),
    ),

    'HtmlHelper' => array(
        'class' => 'Quice\Helper\HtmlHelper',
        'properties' => array('url' => 'UrlHelper'),
    ),

    'Trans' => array(
        'class' => 'Quice\Locale\Translate',
        'properties' => array('dir' => '%i18n.dir%'),
    ),

    // Modules
    'CoreModule' => array('class' => 'Quice\Developer\DeveloperModule'),
    'DevModule' => array('class' => 'Quice\Developer\DeveloperModule')
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
    $injector->getComponent('Dispatcher')->execute();
} catch(Exception $e) {
    echo "<h3>" . $e->getMessage() . "<h3>\n";
    echo "<pre>" . $e->getTraceAsString() . "</pre>\n";
}

?>