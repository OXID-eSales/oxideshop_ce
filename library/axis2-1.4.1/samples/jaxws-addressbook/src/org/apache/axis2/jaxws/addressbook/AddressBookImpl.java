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

import javax.jws.WebService;

/**
 * JAX-WS service implementation that uses and implicit SEI rather than an explicit SEI.  An
 * implicit SEI means that there is no @WebService.endpointInterface element specified.  This means
 * that all public methods on the service implementation comprise an implicit SEI.
 */

// Simply adding the @WebService annotation makes this a JAX-WS service implementation.
@WebService
public class AddressBookImpl  {

    public String addEntry(String firstName, String lastName, String phone, String street, String city, String state) {
        System.out.println("AddressBookImpl.addEntry");
        AddressBookEntry entry = new AddressBookEntry();
        entry.setFirstName(firstName);
        entry.setLastName(lastName);
        entry.setPhone(phone);
        entry.setStreet(street);
        entry.setCity(city);
        entry.setState(state);
        return "AddEntry Completed!";
    }

    public AddressBookEntry findByLastName(String lastName) {
        System.out.println("AddressBookImpl.findByLastName");
        AddressBookEntry entry = new AddressBookEntry(); 
        entry.setFirstName("firstName");
        entry.setLastName("lastName");
        entry.setPhone("phone");
        entry.setStreet("street");
        entry.setCity("city");
        entry.setState("state");
        System.out.println("AddressBookImpl.findByLastName returning " + entry);
        return entry;
    }

}
