Quice Framework
===============

Introduction
-------------

Quice is a lightweight dependency injection(property only) framework. It is
written with speed and flexibility in mind. It allows developers to build better
and easy to maintain websites with PHP.

Install
-------

Installation instructions are slightly different depending on whether you're
installing a distribution-specific package, downloading the latest release, or
fetching the latest development version.

Container
---------

Put simply, Quice Container alleviates the need for factories and the use of new
in your PHP code. Think of Quice's Container as the new new. You will still need
to write factories in some cases, but your code will not depend directly on
them. Your code will be easier to change, unit test and reuse in other contexts.

Module
------

A module contributes configuration information, typically interface bindings,
which will be used to create an Injector. A Quice-based application is
ultimately composed of little more than a set of Modules and some bootstrapping
code.

Action
------

An action is a PHP method that executes, for example, when a given route is
matched. Though an action may also refer to an entire PHP class that includes
several actions.

Service
-------

A Service is a generic term for any PHP object that performs a specific task. A
service is usually used "globally", such as a database access object or an
object that delivers email messages. In Quice, services are often configured and
retrieved from the module. An application that has many decoupled services is
said to follow a service-oriented architecture.

Event
-----

Event is a PHP library that provides a lightweight implementation of the
Observer design pattern. It's a good way to make your code more flexible. It's
also a great way to make your code easily extensible by others. Third-party code
listens to specific events by registering PHP callbacks and the dispatcher
called them whenever your code notifies these events.
