3Sample: Data Binding
====================

Introduction
============

This sample demonstrates how to use WSDL2Java generated code with Castor. 

Running of this sample assumes that you are running this within the extracted release folder (Axis2_HOME/samples/databinding).


Pre-Requisites
==============
 * Install Apache Ant 1.6.2 or later.
 * Please create a directory named lib under the directory that contains this file.
 * Download latest stax-utils jar from
   https://stax-utils.dev.java.net/servlets/ProjectDocumentList?folderID=1106 and drop that into the
   new lib directory.
 * Download version 1.0.4 of Castor jar from http://dist.codehaus.org/castor/1.0.4/castor-1.0.4.jar and drop that into the new
   lib directory.(The latest releases of castor are available at http://www.castor.org/download.html, but this example may not run 	
   with versions later than 1.0.4)

You can achieve all three of the above steps by running "ant download.jars", but it will take some 
time to download those two jars, using ant.


Deploying the Service
=====================

You need to create the stock service Web service and deploy it. Typing ant generate.service or 
simply ant in the command prompt, will build the service against StockQuoteService.wsdl listed 
inside Axis2_HOME/samples/databinding and put it under Axis2_HOME/repository/services.

You need to then startup the server to deploy the service. Go to Axis2_HOME/bin folder and execute either
axis2server.bat (in Windows) or axis2server.sh(in Linux), depending on your platform.


Running the Client
==================

Typing the command "ant run.client" inside Axis2_HOME/samples/databinding runs the Axis2_HOME/samples/databinding/client/src/samples/databinding/StockClient.java class. You may use
the command scripts (as specified above) to do so. You need to supply 2 parameters to the command- url and symbol.

 * ant run.client -Durl=http://localhost:8080/axis2/services/StockQuoteService -Dsymbol=IBM
   Succeeds with a Price of 99.0. You will see "Price = 99.0"

When you call ant run.client with parameters, before running
client/src/samples/databinding/StockClient.java class, it does the following as well:

  * Generate the stubs (for the client) from the WSDL
  * Compile the client classes
  * Create a Jar of the client classes and copy it to build/client/StockService-test-client.jar

How It Works
==============

- Generate code giving -d none to get all the Axis2 APIs with OMElements.
- Create Castor objects for the schema given in the StockQuoteService.wsdl.
- Client API and the service uses those castor objects to get/set data.
- Get StAX events from the castor objects and construct OMElements from them. Those StAX events
  are fed into StAXOMBuilder which can create OM tree from it.
- Feed those OMElement in to generated code.

Note
==============
Sometimes, if you're having trouble running the client successfully, 
It may be necessary to clean the services repository before you generate the service, deploy it
and run the client. (i.e. delete services created from previous samples.)

Help
====
Please contact axis-user list (axis-user@ws.apache.org) if you have any trouble running the sample.

