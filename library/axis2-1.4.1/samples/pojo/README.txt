Sample: POJO (Plain Old Java Object)
====================================

Introduction
============

This is an example POJO Web service. It shows how to expose the methods of a Java class as a Web
service using Aixs2.


Prerequisites  
=============

To build the sample service you must have ant-1.6.x installed in your system. 

To set AXIS2_HOME in Unix/Linux type:
$export AXIS2_HOME=<path to axis2 distribution>

Building the Service
====================

To build the sample service, type: $ant generate.service or just ant

This will build the AddressBookService.aar in the build directory and copy it to the
<AXIS2_HOME>/repository/services directory.

You can start the Axis2 server by running either axis2server.bat (on Windows) or axis2server.sh
(on Linux)that are located in <AXIS2_HOME>/bin directory.

The WSDL for this service should be viewable at:

http://<yourhost>:<yourport>/axis2/services/AddressBookService?wsdl 
(e.g. http://localhost:8080/axis2/services/AddressBookService?wsdl)

src/sample/addressbook/rpcclient/AddressBookRPCClient.java is a Client that uses RPCServiceClient
to invoke the methods of this web services just like the method invocations of a Java object.


Running the Client
==================

To compile and run, type
$ant rpc.client

src/sample/addressbook/adbclient/AddressBookADBClient is Client that uses a generated stub with ADB
to invoke the methods of this web service.

To generate the stub, compile and run, type
$ant adb.client -Dwsdl=http://<yourhost>:<yourport>/axis2/services/AddressBookService?wsdl

Help
====
Please contact axis-user list (axis-user@ws.apache.org) if you have any trouble running the sample.




