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
package sample.addressbook.rpcclient;

import javax.xml.namespace.QName;

import org.apache.axis2.AxisFault;
import org.apache.axis2.addressing.EndpointReference;
import org.apache.axis2.client.Options;
import org.apache.axis2.rpc.client.RPCServiceClient;

import sample.addressbook.entry.Entry;



public class AddressBookRPCClient {

    public static void main(String[] args1) throws AxisFault {

        RPCServiceClient serviceClient = new RPCServiceClient();

        Options options = serviceClient.getOptions();

        EndpointReference targetEPR = new EndpointReference(
                "http://127.0.0.1:8080/axis2/services/AddressBookService");
        options.setTo(targetEPR);

        // /////////////////////////////////////////////////////////////////////

        /*
         * Creates an Entry and stores it in the AddressBook.
         */

        // QName of the target method 
        QName opAddEntry = new QName("http://service.addressbook.sample", "addEntry");

        /*
         * Constructing a new Entry
         */
        Entry entry = new Entry();

        entry.setName("Abby Cadabby");
        entry.setStreet("Sesame Street");
        entry.setCity("Sesame City");
        entry.setState("Sesame State");
        entry.setPostalCode("11111");

        // Constructing the arguments array for the method invocation
        Object[] opAddEntryArgs = new Object[] { entry };

        // Invoking the method
        serviceClient.invokeRobust(opAddEntry, opAddEntryArgs);

        ////////////////////////////////////////////////////////////////////////
        
        
        ///////////////////////////////////////////////////////////////////////
        
        /*
         * Fetching an Entry from the Address book
         */
        
        // QName of the method to invoke 
        QName opFindEntry = new QName("http://service.addressbook.sample", "findEntry");

        //
        String name = "Abby Cadabby";

        Object[] opFindEntryArgs = new Object[] { name };
        Class[] returnTypes = new Class[] { Entry.class };

        
        Object[] response = serviceClient.invokeBlocking(opFindEntry,
                opFindEntryArgs, returnTypes);
        
        Entry result = (Entry) response[0];
        
        if (result == null) {
            System.out.println("No entry found for " + name);
            return;
        } 
        
        System.out.println("Name   :" + result.getName());
        System.out.println("Street :" + result.getStreet());
        System.out.println("City   :" + result.getCity());
        System.out.println("State  :" + result.getState());
        System.out.println("Postal Code :" + result.getPostalCode());
        

        ///////////////////////////////////////////////////////////////////////
    }
}
