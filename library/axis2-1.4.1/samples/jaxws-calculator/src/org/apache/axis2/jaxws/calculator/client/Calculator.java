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

import javax.jws.WebMethod;
import javax.jws.WebParam;
import javax.jws.WebResult;
import javax.jws.WebService;
import javax.xml.bind.annotation.XmlSeeAlso;
import javax.xml.ws.RequestWrapper;
import javax.xml.ws.ResponseWrapper;
import javax.xml.ws.wsaddressing.W3CEndpointReference;


@WebService(name = "Calculator", targetNamespace = "http://calculator.jaxws.axis2.apache.org")
@XmlSeeAlso({
        ObjectFactory.class
        })
public interface Calculator {


    /**
     * @return returns javax.xml.ws.wsaddressing.W3CEndpointReference
     */
    @WebMethod
    @WebResult(targetNamespace = "http://calculator.jaxws.axis2.apache.org")
    @RequestWrapper(localName = "getTicket", targetNamespace = "http://calculator.jaxws.axis2.apache.org", className = "org.apache.axis2.jaxws.calculator.client.GetTicket")
    @ResponseWrapper(localName = "getTicketResponse", targetNamespace = "http://calculator.jaxws.axis2.apache.org", className = "org.apache.axis2.jaxws.calculator.client.GetTicketResponse")
    public W3CEndpointReference getTicket();

    /**
     * @param value1
     * @param value2
     * @return returns int
     * @throws AddNumbersException_Exception
     */
    @WebMethod
    @WebResult(targetNamespace = "http://calculator.jaxws.axis2.apache.org")
    @RequestWrapper(localName = "add", targetNamespace = "http://calculator.jaxws.axis2.apache.org", className = "org.apache.axis2.jaxws.calculator.client.Add")
    @ResponseWrapper(localName = "addResponse", targetNamespace = "http://calculator.jaxws.axis2.apache.org", className = "org.apache.axis2.jaxws.calculator.client.AddResponse")
    public int add(
            @WebParam(name = "value1", targetNamespace = "http://calculator.jaxws.axis2.apache.org")
            int value1,
            @WebParam(name = "value2", targetNamespace = "http://calculator.jaxws.axis2.apache.org")
            int value2)
            throws AddNumbersException_Exception
            ;

}
