Sample: Exception Handling using WSDL Faults
==============================================

Introduction
============

This sample demonstrates how exceptions can be handled using WSDL faults, in other words, how to specify a WSDL fault in order to allow your service to communicate exception pathways to your clients. 

Running of this sample assumes that you are running this within the extracted release folder.

Pre-Requisites
==============
Apache Ant 1.6.2 or later


Building The Service
=====================

* Type ant generate.service or just ant from Axis2_HOME/samples/faulthandling.
* Then go to Axis2_HOME/bin directory and run either axis2server.bat or axis2server.sh depending on your platform.

If you go to  http://localhost:8080/axis2/ you should see BankService is deployed. 

Running The Client
==================

Invoke the client/src/example/BankClient.java class. You may use the command scripts to do so. You need to supply 3 parameters to the command- url, account and amount.

 * ant run.client -Durl=http://localhost:8080/axis2/services/BankService -Daccount=13 -Damt=400
   Throws AccountNotExistFaultMessageException. You will see "Account#13 does not exist"  
 * ant run.client -Durl=http://localhost:8080/axis2/services/BankService -Daccount=88 -Damt=1200
   Throws InsufficientFundsFaultMessageException. You will see "Account#88 has balance of 1000. It cannot support withdrawal   of 1200"  
 * ant run.client -Durl=http://localhost:8080/axis2/services/BankService -Daccount=88 -Damt=400
   Succeeds with a balance of 600. You will see "Balance = 600"  

When you call ant run.client with parameters, before running client/src/example/BankClient.java class, it does the following as well:
 * Generate the stubs (for the client) from the WSDL
 * Compile the client classes
 * Create a Jar of the client classes and copy it to build/client/BankService-test-client.jar

Advanced Guide
==============
For more details kindly see doc/FaultHandlingSampleGuide.html

Help
====
Please contact axis-user list (axis-user@ws.apache.org) if you have any trouble running the sample.
