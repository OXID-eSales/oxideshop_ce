Sample: ServiceLifeCycle
=========================

Introduction:
============

This sample demonstrate usage of service lifecycle and bit of session managment. 
The main idea is to show where and how to use service lifecycle interface and 
session related methods. 


Prerequisites
=============
Apache Ant 1.6.2 or later

If you want to access the service in REST manner you have to deploy the service in 
application server such as Apache Tomcat. Note that it will not work with axis2server.



Deploying the Sevrice
===================== 

Deploy into Sample repository:
    
 * Type ant generate.service or simply ant from Axis2_HOME/samples/servicelifecycle

Deploy into Tomcat :
     
 * To build and copy the service archive file into Tomcat, type ant copy.to.tomcat from 
Axis2_HOME/samples/servicelifecycle which will copy the aar file into
tomcat/web-app/axis2/WEB-INF/services directory.

Running the Client
==================
Type ant run.client from Axis2_HOME/samples/servicelifecycle.

And then follow the instructions as mentioned in the console.

Advanced Guide
==============
For more details kindly see doc/servicelifecycleguide.html

Help
====
Please contact axis-user list (axis-user@ws.apache.org) if you have any trouble running the sample.

