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
package org.apache.axis2.jaxws.calculator.client;

import javax.xml.ws.WebFault;


@WebFault(name = "AddNumbersException", targetNamespace = "http://calculator.jaxws.axis2.apache.org")
public class AddNumbersException_Exception
        extends Exception {

    /**
     * Java type that goes as soapenv:Fault detail element.
     */
    private AddNumbersException faultInfo;

    /**
     * @param faultInfo
     * @param message
     */
    public AddNumbersException_Exception(String message, AddNumbersException faultInfo) {
        super(message);
        this.faultInfo = faultInfo;
    }

    /**
     * @param faultInfo
     * @param message
     * @param cause
     */
    public AddNumbersException_Exception(String message, AddNumbersException faultInfo, Throwable cause) {
        super(message, cause);
        this.faultInfo = faultInfo;
    }

    /**
     * @return returns fault bean: org.apache.axis2.jaxws.calculator.client.AddNumbersException
     */
    public AddNumbersException getFaultInfo() {
        return faultInfo;
    }

}
