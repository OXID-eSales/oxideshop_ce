Sample: JAX-WS top-down development
===================================

Introduction
============
This sample demonstrates top-down development (i.e. development starting with a WSDL document) of a service and client using the JAX-WS API.  The sample is based on an interop scenario for base data types which may be found at http://mssoapinterop.org/ilab/ .

Building the service from scratch
=================================
The service may be constructed from scratch by executing the wsimport tool (found in the Sun JAX-WS RI) on the BaseDataTypesDocLibB.wsdl file: 'wsimport -keep -verbose BaseDataTypesDocLibB.wsdl'  (The -keep option tells the tool to keep the generated files and the -verbose option causes the tool to list what is generated.)  After the files have been generated you will need to provide an implementation of the service (i.e. a class equivalent to TopDownSampleService.java).

Note: Due to an issue with the JAXWSDeployer, the annotations from the interface need to be replicated in the implementing class if the service is being deployed through a jar via the servicejars directory.

Deploying the service
=====================
The classes can be packaged into a jar and the jar can be dropped into the servicejars directory of a deployed axis2 server.

Building the client from scratch
================================
The dynamic proxy client may be constructed from scratch by executing the wsimport tool (found in the Sun JAX-WS RI) on the BaseDataTypesDocLibB.wsdl file: 'wsimport -keep -verbose BaseDataTypesDocLibB.wsdl'  (The -keep option tells the tool to keep the generated files and the -verbose option causes the tool to list what is generated.)  After the files have been generated you will need to provide an implementation of the client (i.e. a class equivalent to TopDownSampleClient.java).


Running the Client
==================
The client can be run via the axis2.sh or axis2.bat scripts (e.g. 'axis2.sh org.apache.axis2.jaxws.interop.InteropSampleClient'.)  The endpoint URL is extracted at runtime from the WSDL, so you can either modify the soap:address to refer to Microsoft's endpoint at http://131.107.72.15/SoapWsdl_BaseDataTypes_XmlFormatter_Service_Indigo/BaseDataTypesDocLitB.svc or to a local endpoint such as http://localhost:8080/axis2/services/TopDownSampleServiceService.IBaseDataTypesDocLitBPort  
