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
package sample.addressbook.service;


import java.util.HashMap;

import sample.addressbook.entry.Entry;



public class AddressBookService {
    
    private HashMap entries = new HashMap();

    /**
     * Add an Entry to the Address Book
     * @param entry
     */
    public void addEntry(Entry entry) {
        this.entries.put(entry.getName(), entry);
    }
    
    /**
     * Search an address of a person
     * 
     * @param name the name of the person whose address needs to be found
     * @return return the address entry of the person. 
     */
    public Entry findEntry(String name) {
        return (Entry) this.entries.get(name);
    }
}
