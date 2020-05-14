# Business logic needs to be implemented in services

* Status: Proposed
* Date: 2020-05-14

While implementing password hashing it became obvious
that putting business logic into data objects is tricky.
This lead to the proposal to use an [anemic domain
model](https://en.wikipedia.org/wiki/Anemic_domain_model)
despite its drawbacks.

## Context and Problem Statement

According to the guidelines for the internal namespace
in the OXID eShop, business logic may be implemented not
only in services, but also in data objects. The only
rule up to now was that the logic in data objects needs
to be self contained.

But there is another consideration: Since the OXID eShop
is a platform where it is necessary to replace functionality
in projects. Currently we have no means to replace data objects.

## Considered Options

* Extract all but trivial logic from data objects to services
* Keep logic within data objects to improve consistency

## Decision Outcome

We put all but trivial business logic into services to
make it exchangeable through the DI container (anemic
domain model)

### Positive Consequences: 

* It is really easy to change business logic in projects

### Negative consequences:

* Business logic is kept apart from data objects
* Data objects may be used incorrectly by inexperiences
  developers leading to inconsistent state
* Code is harder to read because it is distributed 
  between several classes

