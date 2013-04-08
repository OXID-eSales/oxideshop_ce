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

import javax.xml.bind.JAXBElement;
import javax.xml.bind.annotation.XmlElementDecl;
import javax.xml.bind.annotation.XmlRegistry;
import javax.xml.namespace.QName;


/**
 * This object contains factory methods for each
 * Java content interface and Java element interface
 * generated in the org.apache.axis2.jaxws.calculator.client package.
 * <p>An ObjectFactory allows you to programatically
 * construct new instances of the Java representation
 * for XML content. The Java representation of XML
 * content can consist of schema derived interfaces
 * and classes representing the binding of schema
 * type definitions, element declarations and model
 * groups.  Factory methods for each of these are
 * provided in this class.
 */
@XmlRegistry
public class ObjectFactory {

    private final static QName _GetTicket_QNAME = new QName("http://calculator.jaxws.axis2.apache.org", "getTicket");
    private final static QName _Add_QNAME = new QName("http://calculator.jaxws.axis2.apache.org", "add");
    private final static QName _AddResponse_QNAME = new QName("http://calculator.jaxws.axis2.apache.org", "addResponse");
    private final static QName _GetTicketResponse_QNAME = new QName("http://calculator.jaxws.axis2.apache.org", "getTicketResponse");
    private final static QName _AddNumbersException_QNAME = new QName("http://calculator.jaxws.axis2.apache.org", "AddNumbersException");

    /**
     * Create a new ObjectFactory that can be used to create new instances of schema derived classes for package: org.apache.axis2.jaxws.calculator.client
     */
    public ObjectFactory() {
    }

    /**
     * Create an instance of {@link Add }
     */
    public Add createAdd() {
        return new Add();
    }

    /**
     * Create an instance of {@link AddResponse }
     */
    public AddResponse createAddResponse() {
        return new AddResponse();
    }

    /**
     * Create an instance of {@link AddNumbersException }
     */
    public AddNumbersException createAddNumbersException() {
        return new AddNumbersException();
    }

    /**
     * Create an instance of {@link GetTicket }
     */
    public GetTicket createGetTicket() {
        return new GetTicket();
    }

    /**
     * Create an instance of {@link GetTicketResponse }
     */
    public GetTicketResponse createGetTicketResponse() {
        return new GetTicketResponse();
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link GetTicket }{@code >}}
     */
    @XmlElementDecl(namespace = "http://calculator.jaxws.axis2.apache.org", name = "getTicket")
    public JAXBElement<GetTicket> createGetTicket(GetTicket value) {
        return new JAXBElement<GetTicket>(_GetTicket_QNAME, GetTicket.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Add }{@code >}}
     */
    @XmlElementDecl(namespace = "http://calculator.jaxws.axis2.apache.org", name = "add")
    public JAXBElement<Add> createAdd(Add value) {
        return new JAXBElement<Add>(_Add_QNAME, Add.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link AddResponse }{@code >}}
     */
    @XmlElementDecl(namespace = "http://calculator.jaxws.axis2.apache.org", name = "addResponse")
    public JAXBElement<AddResponse> createAddResponse(AddResponse value) {
        return new JAXBElement<AddResponse>(_AddResponse_QNAME, AddResponse.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link GetTicketResponse }{@code >}}
     */
    @XmlElementDecl(namespace = "http://calculator.jaxws.axis2.apache.org", name = "getTicketResponse")
    public JAXBElement<GetTicketResponse> createGetTicketResponse(GetTicketResponse value) {
        return new JAXBElement<GetTicketResponse>(_GetTicketResponse_QNAME, GetTicketResponse.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link AddNumbersException }{@code >}}
     */
    @XmlElementDecl(namespace = "http://calculator.jaxws.axis2.apache.org", name = "AddNumbersException")
    public JAXBElement<AddNumbersException> createAddNumbersException(AddNumbersException value) {
        return new JAXBElement<AddNumbersException>(_AddNumbersException_QNAME, AddNumbersException.class, null, value);
    }

}
