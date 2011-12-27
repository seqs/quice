<?php

/*
 * This file is part of the Quice framework.
 *
 * (c) sunseesiu@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
phpunit ExceptionTest
PHPUnit 3.4.2 by Sebastian Bergmann.

F

Time: 0 seconds

There was 1 failure:

1) testException(ExceptionTest)
Expected exception InvalidArgumentException

FAILURES!
Tests: 1, Assertions: 1, Failures: 1.

phpunit ExpectedErrorTest
PHPUnit 3.4.2 by Sebastian Bergmann.

.

Time: 0 seconds

OK (1 test, 1 assertion)

*/

namespace Quice\Test;

class TestSuit
{
    private $className = null;

    public function __construct($className)
    {
        error_reporting(E_ALL | E_STRICT);
        $this->className = $className;
    }

    public function startUp()
    {
        if(empty($this->className)) {
            return $this->output('Empty class name');
        }

        $testClassName = $this->className . 'Test';
        if(!class_exists($testClassName)) {
           return $this->output('Test class not found: ' . $testClassName);
        }

        try {
            $testClass = new $testClassName();
            $this->runTest($testClass);
        } catch(Exception $e) {
            $this->output($e->getMessage());
        }
    }

    public function runTest($testClass)
    {
        // find all the test*() methods in the Test_* class
        $testMethods = get_class_methods($testClass);
        $passedTest = 0; $failTest = 0; $testNo = 0;
        $testMessage = 'Maxim_System test: ' . $this->className;
        $testHeader = str_repeat('=', strlen($testMessage));
        $testHeader = "\n" . $testHeader . "\n" . $testMessage
            . "\n" . $testHeader . "\n";
        $this->output($testHeader);
        foreach ($testMethods as $testMethod) {

            // skip non test*() methods
            if (substr($testMethod, 0, 4) != 'test') {
                continue;
            }

            $testNo = $testNo + 1;
            try {
                $testClass->setUp();
                $testClass->$testMethod();
                $passedTest = $passedTest + 1;
                $this->output("[$testNo] $testMethod Done!");
            } catch (Exception $e) {
                $error = $testClass->diag($e);
                $testClass->tearDown();
                $failTest = $failTest + 1;
                $this->output("[$testNo] $testMethod Fail!\n" . $error);
            }
            $testClass->tearDown();
        }

        // Test result
        $this->output("\n");
        $totalTest = $passedTest + $failTest;
        if($failTest) {
            $this->output("FAILURES!");
        } else {
            $this->output("OK!");
        }
        $this->output("Tests: {$totalTest}, Assertions: {$passedTest}, Failures: {$failTest}.\n");
    }

    public function output($content)
    {
        echo $content . "\n";
    }

    public function testException() {
        try {
            // ... Code that is expected to raise an exception ...
        } catch (InvalidArgumentException $expected) {
            return;
        }
        // $expectedException = $this->getExpectedException();
        // $this->setExpectedException('InvalidArgumentException');
        $this->fail('An expected exception has not been raised.');
    }
}
