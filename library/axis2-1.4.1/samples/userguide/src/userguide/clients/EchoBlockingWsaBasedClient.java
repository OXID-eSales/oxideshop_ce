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
import org.apache.axis2.Constants;
import org.apache.axis2.addressing.EndpointReference;
import org.apache.axis2.client.Options;
import org.apache.axis2.client.ServiceClient;

import java.io.StringWriter;

/**
 * To run this sample you have to deploy WsaMappingService.aar to the
 * service folder.
 */
public class EchoBlockingWsaBasedClient {

    private static EndpointReference targetEPR = new EndpointReference("http://localhost:8080/axis2/services/WsaMappingTest");

    private static OMElement getBody() {
        OMFactory fac = OMAbstractFactory.getOMFactory();
        OMNamespace omNs = fac
                .createOMNamespace("http://example1.org/example1", "example1");
        OMElement id = fac.createOMElement("id", omNs);
        id.addChild(fac.createOMText(id, "Axis2"));
        return id;
    }

    public static void main(String[] args) throws Exception {
        Options options = new Options();
        options.setTo(targetEPR);
        options.setTransportInProtocol(Constants.TRANSPORT_HTTP);

        //Blocking invocation via wsa mapping
        options.setAction("urn:echo");

        ServiceClient sender = new ServiceClient();
        sender.setOptions(options);
        OMElement result = sender.sendReceive(getBody());

        StringWriter writer = new StringWriter();
        result.serialize(writer);
        writer.flush();

        System.out.println(writer.toString());

    }
}
