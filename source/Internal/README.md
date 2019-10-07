Internal namespace
===================

`Internal` namespace is created to have clearly defined public API.
One of the means to achieve this is given by the nature of the Symfony DI container that is
used to manage services in the `Internal` namespace.

You can use the DI container in the traditional code by fetching the container via
the `ContainerFactory` and then call the `get()`method on the container to obtain a service and
this is only possible for public services, all other services are somehow protected from
direct usage in the traditional code.

We will follow our deprecation procedure only for
interfaces that are marked `@stable`. All other interfaces might change, even in minor versions (we will
keep them stable for patch versions, because nobody should be afraid to install security fixes).

`Internal namespace consists of four main packages:

##### Container

Consists of all classes that handling dependency injection container

##### Domain

Consists of all packages that directly works with business logic

##### Framework

Consists of all technical stuff mostly classes that related to the core

##### Transition

Consists of classes that handling with traditional code

For more information check the developer documentation.
