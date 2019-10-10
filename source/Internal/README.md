Internal namespace
===================

The purpose of the `Internal` namespace is to have a clearly defined public API. 
One of the means to achieve this is the usage of the Symfony DI container to manage 
services in the `Internal` namespace. The implementations themselves are shielded 
by interfaces, so the interfaces in the Internal namespace might be considered as 
part of the public API of the OXID eShop.

You may use the DI container in the traditional code by fetching the it via the 
`ContainerFactory` and then call the `get()`method on the container to obtain a 
service. But be aware: this is only possible for public services, all other 
services are protected from direct usage in the traditional code.

But when you write modules you can write your own services and inject every service 
from the internal namespace. In this case you need to be aware that the 
implementation of these services might change between versions. And we also reserve 
the right to change the interfaces, although this should not happen too often and 
will be documented in the upgrade instructions.

We will follow our deprecation procedure only for interfaces that are explicitly 
marked `@stable`. All other interfaces might change, even in minor versions 
(we will keep them stable for patch versions, because nobody should be afraid to 
install security fixes).

The `Internal`  namespace consists of four main directories:

#### Container

All classes that enable traditional code to gain access `Internal` namespace via the `ContainerFactory`.        

#### Domain

All packages that directly works with business logic.

#### Framework

All Application infrastructure classes are not include business logic.

#### Transition

All classes that enable `Internal` namespace to gain access traditional code.

For more information check the developer documentation.
