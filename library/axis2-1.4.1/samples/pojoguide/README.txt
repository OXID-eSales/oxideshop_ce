POJO Web Services using Apache Axis2- Sample 1
=============================================
This sample contains source code for the xdocs/1_1/pojoguide.html document found in 
the extracted Axis2 Documents Distribution. For a more detailed description on the 
source code kindly see this 'POJO Web Services using Apache Axis2' document.

The above mentioned document shows you how to take a simple POJO (Plain Old Java 
Object), and deploy it on Apache Tomcat as a Web service in the exploded directory 
format. This is a quick way to get a Web service up and running in no time. 

Introduction
============

This sample shows how to expose a Java class as a web service.  
The WeatherService Java class provides methods to get and set a Weather 
type Java objects. The client uses RPCServiceClient to invoke those two 
methods just as Java object method invocation.

Prerequisites
==============

Apache Ant 1.6.2 or later

Building the Service
====================

Type $ant from Axis2_HOME/samples/pojoguide


Running the Client
==================

Type $ant rpc.client


Help
====
Please contact axis-user list (axis-user@ws.apache.org) if you have any trouble running the sample.