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
package org.apache.axis2.jaxws.calculator;

import org.w3c.dom.Document;
import org.w3c.dom.Element;

import javax.annotation.Resource;
import javax.jws.WebService;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.ws.WebServiceContext;
import javax.xml.ws.WebServiceException;
import javax.xml.ws.handler.MessageContext;
import javax.xml.ws.soap.Addressing;
import javax.xml.ws.wsaddressing.W3CEndpointReference;
import java.util.List;

@Addressing
@WebService(endpointInterface = "org.apache.axis2.jaxws.calculator.Calculator",
        serviceName = "CalculatorService",
        portName = "CalculatorServicePort",
        targetNamespace = "http://calculator.jaxws.axis2.apache.org",
        wsdlLocation = "META-INF/CalculatorService.wsdl")
public class CalculatorService implements Calculator {

    @Resource
    private WebServiceContext context;

    /*
     *  (non-Javadoc)
     * @see org.apache.axis2.jaxws.calculator.Calculator#getTicket()
     */
    public W3CEndpointReference getTicket() {
        DocumentBuilderFactory factory = DocumentBuilderFactory.newInstance();
        factory.setNamespaceAware(true);
        Element element = null;

        try {
            DocumentBuilder builder = factory.newDocumentBuilder();
            Document document = builder.newDocument();

            element =
                    document.createElementNS("http://calculator.jaxws.axis2.apache.org", "TicketId");
            element.appendChild(document.createTextNode("123456789"));
        } catch (ParserConfigurationException pce) {
            // Parser with specified options can't be built
            pce.printStackTrace();
            throw new WebServiceException("Unable to create ticket.", pce);
        }

        return (W3CEndpointReference) getContext().getEndpointReference(element);
    }

    /*
     *  (non-Javadoc)
     * @see org.apache.axis2.jaxws.calculator.Calculator#add(int, int)
     */
    public int add(int value1, int value2) throws AddNumbersException_Exception {
        List list = (List) getContext().getMessageContext().get(MessageContext.REFERENCE_PARAMETERS);

        if (list.isEmpty()) {
            AddNumbersException faultInfo = new AddNumbersException();
            faultInfo.setMessage("No ticket found.");
            throw new AddNumbersException_Exception(faultInfo.getMessage(), faultInfo);
        }
        Element element = (Element) list.get(0);

        if (!"123456789".equals(element.getTextContent())) {
            AddNumbersException faultInfo = new AddNumbersException();
            faultInfo.setMessage("Invalid ticket: " + element.getTextContent());
            throw new AddNumbersException_Exception(faultInfo.getMessage(), faultInfo);
        }

        System.out.println("value1: " + value1 + " value2: " + value2);
        return value1 + value2;
    }

    //Return the WebServiceContext
    private WebServiceContext getContext() {
        return context;
    }
}