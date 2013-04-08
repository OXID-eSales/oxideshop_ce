/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements. See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership. The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */

package org.apache.axis2.jaxws.addressbook;

import javax.xml.namespace.QName;
import javax.xml.ws.BindingProvider;
import javax.xml.ws.Dispatch;
import javax.xml.ws.Service;

import java.util.Map;

/**
 * Simple JAX-WS Dispatch client for the address book service implementation.
 */
public class AddressBookClient {
    private static String NAMESPACE = "http://addressbook.jaxws.axis2.apache.org";
    private static QName QNAME_SERVICE = new QName(NAMESPACE, "service");
    private static QName QNAME_PORT = new QName(NAMESPACE, "port");
    private static String ENDPOINT_URL = "http://localhost:8080/axis2/services/AddressBookImplService.AddressBookImplPort";

    private static String ADD_ENTRY_BODY_CONTENTS = 
        "<ns1:addEntry xmlns:ns1=\"http://addressbook.jaxws.axis2.apache.org\">" + 
          "<ns1:firstName xmlns=\"http://addressbook.jaxws.axis2.apache.org\">myFirstName</ns1:firstName>" + 
          "<ns1:lastName xmlns=\"http://addressbook.jaxws.axis2.apache.org\">myLastName</ns1:lastName>" + 
          "<ns1:phone xmlns=\"http://addressbook.jaxws.axis2.apache.org\">myPhone</ns1:phone>" + 
          "<ns1:street xmlns=\"http://addressbook.jaxws.axis2.apache.org\">myStreet</ns1:street>" + 
          "<ns1:city xmlns=\"http://addressbook.jaxws.axis2.apache.org\">myCity</ns1:city>" + 
          "<ns1:state xmlns=\"http://addressbook.jaxws.axis2.apache.org\">myState</ns1:state>" + 
        "</ns1:addEntry>";
    
    private static String FIND_BODY_CONTENTS = 
        "<ns1:findByLastName xmlns:ns1=\"http://addressbook.jaxws.axis2.apache.org\">" +
          "<ns1:lastName xmlns=\"http://addressbook.jaxws.axis2.apache.org\">myLastName</ns1:lastName>" +        
        "</ns1:findByLastName>";
    
    public static void main(String[] args) {
        try {
            System.out.println("AddressBookClient ...");
            
            Service svc = Service.create(QNAME_SERVICE);
            svc.addPort(QNAME_PORT, null, ENDPOINT_URL);

            // A Dispatch<String> client sends the request and receives the response as 
            // Strings.  Since it is PAYLOAD mode, the client will provide the SOAP body to be 
            // sent; the SOAP envelope and any required SOAP headers will be added by JAX-WS.
            Dispatch<String> dispatch = svc.createDispatch(QNAME_PORT, 
                    String.class, Service.Mode.PAYLOAD);
                
            // Invoke the Dispatch
            System.out.println(">> Invoking sync Dispatch for AddEntry");
            String response = dispatch.invoke(ADD_ENTRY_BODY_CONTENTS);
            System.out.println("Add Entry response: " + response);
            
            System.out.println(">> Invoking Dispatch for findByLastName");
            String response2 = dispatch.invoke(FIND_BODY_CONTENTS);
            System.out.println("Find response: " + response2);
        } catch (Exception e) {
            System.out.println("Caught exception: " + e);
            e.printStackTrace();
        }
    }
}
