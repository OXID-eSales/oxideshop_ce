Axis2 Quick Start Guide- Sample 2 (ADB)
======================================

This sample contains source code for the xdocs/1_1/quickstartguide.html document found in 
the extracted Axis2 Documents Distribution. For a more detailed description on the 
source code kindly see this 'Axis2 Quick Start Guide' document.

Introduction
============

In this sample, we are deploying an ADB generated service. The service
is tested using generated client stubs.

Pre-Requisites
==============

Apache Ant 1.6.2 or later

Building the Service
====================

Type "ant generate.service" from Axis2_HOME/samples/quickstartadb
directory and then deploy the 
Axis2_HOME/samples/quickstartadb/build/service/build/lib/StockQuoteService.aar

Running the Client
==================

type ant run.client in the Axis2_HOME/samples/quickstartadb directory

Help
====
Please contact axis-user list (axis-user@ws.apache.org) if you
have any trouble running the sample.
