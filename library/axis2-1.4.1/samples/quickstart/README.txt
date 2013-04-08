Axis2 Quick Start Guide- Sample 1
=================================

This sample contains source code for the xdocs/1_1/quickstartguide.html document found in 
the extracted Axis2 Documents Distribution. For a more detailed description on the 
source code kindly see this 'Axis2 Quick Start Guide' document.

Introduction
============
In this sample, we are deploying a POJO after writing a services.xml and
creating an aar. We also test the gePrice and update methods using a browser.

Pre-Requisites
==============

Apache Ant 1.6.2 or later

Building the Service
====================

Type "ant generate.service" or just "ant" from Axis2_HOME/samples/quickstart directory 
and then deploy the Axis2_HOME/samples/quickstart/build/StockQuoteService.aar

Generate WSDL
==============

Type "ant generate.wsdl" from Axis2_HOME/samples/quickstart directory which generates a 
WSDL file for the above Web service and it will be placed in Axis2_HOME/samples/quickstart/build 
directory.

Running the Client
==================
- From your browser, If you point to the following URL:
http://localhost:8080/axis2/services/StockQuoteService/getPrice?symbol=IBM

You will get the following response:
<ns:getPriceResponse><ns:return>42.0</ns:return></ns:getPriceResponse>

- If you invoke the update method like so:
http://localhost:8080/axis2/services/StockQuoteService/update?symbol=IBM&price=100

And then execute the first getPrice url. You can see that the price got updated.

Help
====
Please contact axis-user list (axis-user@ws.apache.org) if you have any trouble running the sample.