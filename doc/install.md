Install
=======

Downloading Quice
-----------------

First, check that you have installed and configured a Web server (such as
Apache) with PHP 5.3.2 or higher.

Ready? Start by downloading the "Quice Standard Edition", a Quice distribution
that is preconfigured for the most common use cases and also contains some code
that demonstrates how to use Quice.

After unpacking the archive under your web server root directory, you should
have a quice/ directory that looks like this:

    www/ <- your web root directory
        quice/ <- the unpacked archive


Create Project
--------------

Create your project directory projectname/ under your web server root directory
and create index.php under your project directory.

    // www/projectname/index.php
    <?php
    require __DIR__ . '/../quice/boot.php';
    ?>

Use the following URL to see your first "real" Quice webpage:

    http://localhost/projectname/index.php

Quice should welcome and congratulate you for your hard work so far!



