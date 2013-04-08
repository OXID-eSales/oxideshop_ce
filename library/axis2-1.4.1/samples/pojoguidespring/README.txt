POJO Web Services using Apache Axis2- Sample 2
==============================================
This sample contains source code for the xdocs/1_1/pojoguide.html document found in 
the extracted Axis2 Documents Distribution. For a more detailed description on the 
source code kindly see this 'POJO Web Services using Apache Axis2' document.

In this specific sample you'll be shown how to take a POJO  (Plain Old Java Object) 
based on the Spring Framework, and deploy that as an AAR packaged Web service on Tomcat. 
This is a quick way to get a Web service up and running in no time. 

Introduction
============

This sample shows how to expose the getters and setters of WeatherSpringService that 
takes Weather type Java Object as the argument and the return type. It uses the Spring 
framework to initialize the weather property of the WeatherSpringService.


Pre-Requisites
==============

Apache Ant 1.6.2 or later

Spring-1.2.6.jar or later 
You need to have this jar in your build and runtime class path. The easiest way to do this 
is to copy it to Axis2_HOME/lib directory.

Building the Service
====================

Type $ant from Axis2_HOME/samples/pojoguidespring


Running the Client
==================
Type $ant rpc.client from from Axis2_HOME/samples/pojoguidespring

Help
====
Please contact axis-user list (axis-user@ws.apache.org) if you have any trouble running the sample.