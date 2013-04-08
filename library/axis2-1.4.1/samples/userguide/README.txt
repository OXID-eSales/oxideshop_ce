Axis2 User's Guide Sample
=========================

This sample contains the source code relevant to xdocs/1_1/adv-userguide.html, 
more specifically to xdocs/1_1/dii.html and xmlbased-server.html which are sections 
of the Axis2 Advanced User's Guide found in the Documents Distribution.

The sample explains how to write a Web service and Web service client with 
Apache Axis2 using XML based client APIs (Axis2's Primary APIs).

Introduction
============

The following sample clients are located at 
AXIS2_HOME/samples/userguide/samples/userguide/src/userguide/clients directory. 

EchoBlockingClient.java -- Demonstrates the request-response, blocking client which 
is written using Axis2's primary APIs.

EchoBlockingDualClient.java -- Demonstrates the request-response, blocking client which 
uses two transport connections and written using Axis2's primary APIs.

EchoNonBlockingClient.java -- Demonstrates the request-response, non-blocking invocation 
which is written using Axis2's primary APIs.

EchoNonBlockingDualClient.java -- Demonstrates the request-response, non-blocking invocation 
using two transport connections and is written using Axis2's primary APIs.

MailClient.java -- Represents a client for invoking 

PingClient.java -- Represents a simple one-way client which is written using Axis2's primary APIs.

RESTClient.java -- Represents a client for invoking a REST Web service

TCPClient.java -- Demonstrates a client for invoking a Web service using TCP

example1, example2 and example3 directories inside Axis2_home/samples/userguide/src/userguide 
contain the Web services which are invoked by the above clients.


Pre-Requisites
==============

Apache Ant 1.6.2 or later

Building the Service
====================
* Type "ant generate.module" from Axis2_HOME/samples/userguide to generate the logging module.
* Add the logging phase to the ../../conf/axis2.xml. For more details please refer to the 
  http://ws.apache.org/axis2/1_2/modules.html
* Type "ant generate.service" or just "ant" from Axis2_HOME/samples/userguide
* Then go to AXIS2_HOME/bin directory and run either axis2server.bat or axis2server.sh depending on your platform.

Alternatively you can copy the the generated service archives in to a servlet container.

If you go to http://localhost:8080/axis2/, you should see MyService, MyServiceWithModule are deployed. 


Running the Clients
===================

Type the following ant commands from Axis2_HOME/samples/userguide to run the clients one by one.

 * "ant run.client.blocking"
   This invokes MyService through a request-response, blocking client.   
 * "ant run.client.blockingdual"
   This invokes MyService through a request-response, blocking client via dual transport channels.   
 * "ant run.client.nonblocking"
   This invokes MyService through a request-response, non-blocking client.  
 * "ant run.client.nonblockingdual"
   This invokes MyService through a request-response, non-blocking client via dual transport channels.   
 * "ant run.client.ping"
   This invokes MyService through a one-way client

 
You can find more information on the above clients in Axis2 users guide, RESTFul Web services support, 
TCP Transport documents found in the Documents Distribution's xdocs directory. Also, you may find it 
useful to try out the above services and clients while going through these documents.

Note
==============
Sometimes, if you're having trouble running the client successfully, 
It may be necessary to clean the services repository before you generate the service, deploy it
and run the client. (i.e. delete services created from previous samples.)

Help
====
Please contact axis-user list (axis-user@ws.apache.org) if you have any trouble running the sample.
