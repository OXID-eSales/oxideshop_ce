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

import org.apache.axiom.om.OMElement;
import org.apache.axis2.AxisFault;
import org.apache.axis2.Constants;
import org.apache.axis2.addressing.EndpointReference;
import org.apache.axis2.client.Options;
import org.apache.axis2.client.ServiceClient;

import javax.xml.namespace.QName;
import javax.xml.stream.XMLOutputFactory;
import java.io.StringWriter;

/**
 * Sample for synchronous dual channel blocking service invocation.
 * Message Exchage Pattern IN-OUT
 */
public class EchoBlockingDualClient {
    private static EndpointReference targetEPR = new EndpointReference("http://127.0.0.1:8080/axis2/services/MyService");

    public static void main(String[] args) {
        ServiceClient sender = null;
        try {
            OMElement payload = ClientUtil.getEchoOMElement();
            Options options = new Options();
            options.setTo(targetEPR);
            options.setAction("urn:echo");
            //The boolean flag informs the axis2 engine to use two separate transport connection
            //to retrieve the response.
            options.setTransportInProtocol(Constants.TRANSPORT_HTTP);
            options.setUseSeparateListener(true);

            //Blocking Invocation
            sender = new ServiceClient();
            sender.engageModule(Constants.MODULE_ADDRESSING);
            sender.setOptions(options);
            OMElement result = sender.sendReceive(payload);

            StringWriter writer = new StringWriter();
            result.serialize(XMLOutputFactory.newInstance()
                    .createXMLStreamWriter(writer));
            writer.flush();
            System.out.println(writer.toString());

            //Need to close the Client Side Listener.

        } catch (AxisFault axisFault) {
            axisFault.printStackTrace();
        } catch (Exception ex) {
            ex.printStackTrace();
        } finally{
            try {
                sender.cleanup();
            } catch (AxisFault axisFault) {
                //
            }
        }

    }
}
