Sample: JAXWS-Calculator
====================================

Introduction
============

This is an example JAXWS Web service. It shows how to expose the methods of a class as a JAXWS Web
service using Axis2.


Building the Service
====================

To build the sample service, type: mvn clean install

This will build the jaxws-calculator.jar in the target directory and copy it to the
<AXIS2_HOME>/repository/servicejars directory (create that directory if it's not yet created)

You can start the Axis2 server by running either axis2server.bat (on Windows) or axis2server.sh
(on Linux)that are located in <AXIS2_HOME>/bin directory.

The WSDL for this service should be viewable at:

http://<yourhost>:<yourport>/axis2/services/CalculatorService.CalculatorServicePort?wsdl
(http://localhost:8080/axis2/services/CalculatorService.CalculatorServicePort?wsdl)


Running the Client
==================

1. Set up a CLASSPATH with the following 
- all the jars in <AXIS2_HOME/lib>
- the jaxws-calculator-SNAPSHOT-client.jar under the target directory
- addressing mar in <AXIS2_HOME/repository/modules>

2. Run the client using say "java -classpath %CLASSPATH% org.apache.axis2.jaxws.calculator.client.AddSEIClient 3 33"

Help
====
Please contact axis-user list (axis-user@ws.apache.org) if you have any trouble running the sample.

Improvements
============
Make this sample run possible with ant 


**** WARNING ****
Please Remove xalan jar from <AXIS2_HOME>/ before you start axis2 server. If you are using this
sample inside a WAR, please remove xalan jar from WEB-INF/lib