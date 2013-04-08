Sample: SOAP with Attachments
================================

Introduction
============
This sample demonstrates the capabilities and power of SOAP with Attachment support and the
Attachment API of AXIS2. More information about Axis2 attachment implementation can be found at 
http://ws.apache.org/axis2/1_1/mtom-guide.html. 

This sample includes a service and a client which can be used to upload a file to the server using 
SOAP message containing a SOAP with Attachment type binary attachment. The service is written and 
deployed using the RCPMessageReceiver and a POJO(Plain Old Java Object). The POJO service class uses 
the Attachment API of the MessageContext to retrieve the received attachment.The client program is 
written using the OperationClient API of Axis2 together with the Attachment API of MessageContext.


Objectives
==========

 * Writing a POJO (Plain Old Java Object) based service to access attachments.
 * Implement a Axis2 OperationClient based Web Service client to invoke the service with SOAP 
   with Attachment type attachments.
 * Invoke the deployed service.

Prerequisites
=============
Install Apache Ant 1.6.2 or later


Running the Sample:
===================
The files belonging to this sample are contained in the samples/soapwithattachments folder of the 
extracted Axis2 binary distribution, which will be called here after as SWA_SAMPLE_DIR. The 
location of the extracted binary distribution will be refered as AXIS2_DIST. There is a 
"build.xml" Ant script in the SWA_SAMPLE_DIR that contains build targets for building the service 
archive and running the client application - all described in steps below. 

1. Generate the service
Use "ant generate.service" command in the SWA_SAMPLE_DIR to build the service. Generated service 
will automatically gets copied in to the AXIS2_DIST/repository/services directory. Source file 
ralating to this service can be found at 
SWA_SAMPLE_DIR/src/sample/soapwithattachments/service/AttachmentService.java. The services.xml used
when building this service can be found at SWA_SAMPLE_DIR/resources directory. 

2. Deploy the service
Run the AXIS2_DIST/bin/axis2server.{sh.bat} script to start the standalone axis2 server. This server 
will deploy all the srvices available at AXIS2_DIST/repository/services directory. Alternatively you
can drop the sample-swa.aar service archive to the services directory of a running Axis2 servlet)

3. Running the client
Use "ant run.client -Dfile <file to be send> -Ddest <destination file name>" command in the 
SWA_SAMPLE_DIR to build and run the client. Source file ralating to the client can be found at 
SWA_SAMPLE_DIR/src/sample/soapwithattachments/client/SWAClient.java.

Help
====
Please contact axis-user list (axis-user@ws.apache.org) if you have any trouble running the sample.

