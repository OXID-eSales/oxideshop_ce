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

package userguide.clients;

import org.apache.axiom.om.OMAbstractFactory;
import org.apache.axiom.om.OMElement;
import org.apache.axiom.om.OMFactory;
import org.apache.axiom.om.OMNamespace;
import org.apache.axis2.AxisFault;
import org.apache.axis2.Constants;
import org.apache.axis2.addressing.EndpointReference;
import org.apache.axis2.client.Options;
import org.apache.axis2.client.ServiceClient;

import javax.xml.namespace.QName;
import javax.xml.stream.FactoryConfigurationError;
import javax.xml.stream.XMLOutputFactory;
import javax.xml.stream.XMLStreamException;
import javax.xml.stream.XMLStreamWriter;

/**
 * This is a Client progam that accesses 'MyService' web service in Axis2 samples
 */
public class TCPClient {

    private static String toEpr = "tcp://localhost:8080/axis2/services/MyService";

    public static void main(String[] args) throws AxisFault {

        Options options = new Options();
        options.setTo(new EndpointReference(toEpr));
        options.setTransportInProtocol(Constants.TRANSPORT_TCP);
        options.setAction("urn:echo");

        ServiceClient sender = new ServiceClient();
        sender.engageModule(Constants.MODULE_ADDRESSING);
        sender.setOptions(options);
        OMElement result = sender.sendReceive(getPayload());

        try {
            XMLStreamWriter writer = XMLOutputFactory.newInstance()
                    .createXMLStreamWriter(System.out);
            result.serialize(writer);
            writer.flush();
        } catch (XMLStreamException e) {
            e.printStackTrace();
        } catch (FactoryConfigurationError e) {
            e.printStackTrace();
        }
    }


    private static OMElement getPayload() {
        OMFactory fac = OMAbstractFactory.getOMFactory();
        OMNamespace omNs = fac.createOMNamespace(
                "tcp://localhost:8080/axis2/services/MyService", "example1");
        OMElement method = fac.createOMElement("echo", omNs);
        OMElement value = fac.createOMElement("Text", omNs);
        value.addChild(fac.createOMText(value, "Axis2 Echo String "));
        method.addChild(value);

        return method;
    }
}
