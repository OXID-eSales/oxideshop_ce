Axis2 JAX-WS sample that uses JAXB artifacts created from a simple schema.

   1. Given a simple schema, generate the JAXB artifacts using xjc.
   2. With the generated JAXB beans in hand, write the service implementation 
      and add annotations to make a Web Service.
   3. Write a Dispatch client to interact with the service
   4. Run the Dispatch client against the service implementation deployed in the Axis2 server

This address book sample is based on one of the jaxws-integration tests:
modules/jaxws-integration/test/org/apache/axis2/jaxws/sample/addressbook

Note that this is a very simple example, and does not persist any data.  The intent is
to illustrate the use of JAXB objects with JAX-WS, not to actually implement and address book.

The following source is included with this example:
src\AddressBookEntry.xsd:  Schema used to generate JAXB artifacts
src\org\apache\axis2\jaxws\addressbook\AddressBook.java:  JAXWS Service Endpoint Interface (SEI)
    Note that this SEI is NOT CURRENTLY USED in this example.
src\org\apache\axis2\jaxws\addressbook\AddressBookClient.java: JAXWS Dispatch Client
src\org\apache\axis2\jaxws\addressbook\AddressBookImpl.java: JAXWS service implementation


Step 0: How to build this sample
================================
To build this sample, execute the following maven command: 
	mvn clean install

This will do the following:
- Generate the JAXB artifacts (in target/schema)
- Compile the service implementation classes (in target/classes), including the JAXB artifacts, 
  and create a JAR containing those classes (target/jaxws-addressbook-SNAPSHOT.jar)
- Compile the Dispatch client classes (in target/classes) and create a JAR containing those classes
  (target/jaxws-addressbook-SNAPSHOT-Client.jar).  


Step 1: Generate JAXB artifacts from simple schema
==================================================
The file src/AddressBookEntry.xsd describes a simple AddressBookEntry object with the
following fields:
    String firstName;
    String lastName;
    String phone;
    String street;
    String city;
    String state;

The following JAXB artifacts are generated in the target/schema/src directory when the 
sample is built 
org\apache\axis2\jaxws\addressbook\AddressBookEntry.java
org\apache\axis2\jaxws\addressbook\ObjectFactory.java
org\apache\axis2\jaxws\addressbook\package-info.java

These files will be compiled into target/classes as part of the build.


Step 2: Write a JAX-WS service implementation using the JAXB artifacts
======================================================================
The simple service implementation will have two methods on it:
    public String addEntry(String firstName, String lastName, String phone, String street, String city, String state)
    public AddressBookEntry findByLastName(String lastName)
    
The service implementation does not explicitly specify a JAX-WS SEI.  The public methods on the
implementation will be an implicit SEI.  Simply by adding an @WebService annotation to the 
implementation class it becomes a JAX-WS web service.   

The implementation class is: src\org\apache\axis2\jaxws\addressbook\AddressBookImpl.java  

This file will be compiled into target/classes as part of the build.


Step 3: Write a JAX-WS Dispatch client to interact with the service
===================================================================
The extremely simple Dispatch client will be a Payload mode String Dispatch client, meaning that
it will provide the exact SOAP body to send in the request (Payload mode) as a String, and expect 
the response to be a SOAP body returned as a String.  It will invoke both methods on the  
service implenetation's implicit SEI.

The dispatch client class is: src\org\apache\axis2\jaxws\addressbook\AddressBookClient.java

This file will be compiled into target/classes as part of the build.


Step 4: Run the Dispatch client against the service implementation deployed in the Axis2 server
===============================================================================================
(a) Setup your environment to run the sample.  You will need two windows, one for the server
and one for the client.  Each needs the following environment variables set:
- Axis2 binary distribution.  For example: AXIS2_HOME=C:\temp\Axis2\axis2-SNAPSHOT
- Java5 JDK.  For example: JAVA_HOME=c:\java\java5 

(b) Copy the service implementation JAR file from the sample target directory to the appropriate 
Axis2 repository directory, %AXIS2_HOME%\repository\servicejars.  Note that JAR files in this 
directory will be deployed into the Axis2 simple server using only annotations on classes 
within the JARs; no deployment descriptor is required.

If the repository directory does not exist, create it first, then copy the service 
implementation JAR:
	mkdir %AXIS2_HOME%\repository\servicejars
	copy target\jaxws-addressbook-SNAPSHOT.jar %AXIS2_HOME%\repository\servicejars

(c) Start the axis2 server.  This will deploy the JAX-WS service implementation.
	cd %AXIS2_HOME%
	bin\axis2server.bat

You should see a message such as:
[INFO] Deploying artifact : jaxws-addressbook-SNAPSHOT.jar
[INFO] Deploying JAXWS annotated class org.apache.axis2.jaxws.addressbook.AddressBookImpl as a service - AddressBookImplService.AddressBookImplPort

(d) From another window with the environment setup, in the jaxws-addressbook samples directory run 
the Dispatch client:
	java -Djava.ext.dirs=%AXIS2_HOME%\lib;%JAVA_HOME%\jre\lib\ext -cp target/classes org.apache.axis2.jaxws.addressbook.AddressBookClient.class 


You should see something like the following in the client window:
>> Invoking sync Dispatch for AddEntry
Add Entry response: <dlwmin:addEntryResponse xmlns:dlwmin="http://addressbook.jaxws.axis2.apache.org/"><return xmlns:ns2
="http://addressbook.jaxws.axis2.apache.org">AddEntry Completed!</return></dlwmin:addEntryResponse>
>> Invoking Dispatch for findByLastName
Find response: <dlwmin:findByLastNameResponse xmlns:dlwmin="http://addressbook.jaxws.axis2.apache.org/"><return xmlns:ns
2="http://addressbook.jaxws.axis2.apache.org"><firstName>firstName</firstName><lastName>lastName</lastName><phone>phone<
/phone><street>street</street><city>city</city><state>state</state></return></dlwmin:findByLastNameResponse>
