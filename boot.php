<?php
/*
 * This file is part of the Quice framework.
 *
 * (c) sunseesiu@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

!isset($config) and $config = array();
!isset($packages) and $packages = array();
!isset($components) and $components = array();

$_config = array(
    'env' => 'dev',
    'tpl' => array('dirs' => array('quice' => __DIR__ . '/dev/tpl'))
);

$_packages = array(
    'Quice' => __DIR__ . '/src',
    'Quice\Developer' => __DIR__ . '/dev/src'
);

$_components = array(
    // Globals
    'ActionDispatcher' => array(
        'class' => 'Quice\Action\ActionDispatcher',
        'properties' => array(
            'request' => 'Request',
            'response' => 'Response',
            'container' => 'ThisContainer'
        ),
    ),
    'EventDispatcher' => array(
        'class' => 'Quice\Event\EventDispatcher',
        'properties' => array('container' => 'ThisContainer'),
    ),
    'Request' => array('class' => 'Quice\Http\Request'),
    'Response' => array(
        'class' => 'Quice\Http\Response',
        'properties' => array('renderer' => 'Template'),
    ),
    'Template' => array(
        'class' => 'Quice\Template\TemplateEngine',
        'properties' => array(
            'dirs' => '%tpl.dirs%',
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
    'DevModule' => array('class' => 'Quice\Developer\DeveloperModule'),
    'ErrorModule' => array('class' => 'Quice\Developer\DeveloperModule')
);

$config = array_merge($_config, $config);
$packages = array_merge($_packages, $packages);
$components = array_merge($_components, $components);
$config['tpl']['dirs'] = isset($config['tpl']['dirs'])
    ? array_merge((array)$config['tpl']['dirs'], $_config['tpl']['dirs'])
    : $_config['tpl']['dirs'];

error_reporting($config['env'] == 'dev' ? E_ALL|E_STRICT : 0);

if (version_compare(phpversion(), '5.3.2', '<') === true) {
    die('Quice only supports PHP 5.3.2 or higher version.');
}

require_once __DIR__ . '/src/Quice/Package/Autoloader.php';

use Quice\Package\Autoloader;
use Quice\Container\Injector;

try {
    $loader = new Autoloader();
    $loader->register($packages);

    $injector = new Injector($components, $config);
    $injector->getComponent('ActionDispatcher')->execute();
} catch(Exception $e) {
    echo "<h3>" . $e->getMessage() . "</h3>\n";
    echo "<pre>" . $e->getTraceAsString() . "</pre>\n";
}
