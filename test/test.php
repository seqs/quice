<?php
/*
 * This file is part of the Quice framework.
 *
 * (c) sunseesiu@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

error_reporting(E_ALL|E_STRICT);
require_once 'src/Quice/Container/Injector.php';

class A
{
    public $b;

    public function execute()
    {
        $this->b->execute();
    }
}

class B
{
    public function execute()
    {
        echo 'b';
    }
}

class C
{
    public $a;
    public function execute()
    {
        echo 'c';
    }
}

$components = array(
    'A' => array(
        'class' => 'A',
        'properties' => array('b' => 'C'),
    ),
    'B' => array(
        'class' => 'B',
        //'properties' => array('b' => 'B'),
    ),
    'C' => array(
        'class' => 'C',
        'properties' => array('a' => 'B'),
    ),
);

use Quice\Container\Injector;
$injector = new Injector($components);
$injector->getComponent('C')->execute();


