======================================================
Apache Axis2 1.4.1 build  (13-08-2008)

http://ws.apache.org/axis2
------------------------------------------------------

___________________
Documentation
===================
 
Documentation can be found in the 'docs' distribution of this release 
and in the main site.

___________________
Deploying
===================

To deploy a new Web service in Axis2 the following three steps must 
be performed:
  1) Create the Web service implementation class, supporting classes 
     and the services.xml file, 
  2) Archive the class files into a jar with the services.xml file in 
     the META-INF directory
  3) Drop the jar file to the $AXIS2_HOME/WEB-INF/services directory
     where $AXIS2_HOME represents the install directory of your Axis2 
     runtime. (In the case of a servelet container this would be the
     "axis2" directory inside "webapps".)

To verify the deployment please go to http://<yourip>:<port>/axis2/ and
follow the "Services" Link.

For more information please refer to the User's Guide.

___________________
Support
===================
 
Any problem with this release can be reported to Axis mailing list
or in the JIRA issue tracker. If you are sending an email to the mailing
list make sure to add the [Axis2] prefix to the subject.

Mailing list subscription:
    axis-dev-subscribe@ws.apache.org

Jira:
    http://issues.apache.org/jira/secure/BrowseProject.jspa?id=10611


Thank you for using Axis2!

The Axis2 Team. 
