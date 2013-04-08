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
package samples.quickstart.clients;

import org.apache.axiom.om.OMAbstractFactory;
import org.apache.axiom.om.OMElement;
import org.apache.axiom.om.OMFactory;
import org.apache.axiom.om.OMNamespace;
import org.apache.axis2.Constants;
import org.apache.axis2.addressing.EndpointReference;
import org.apache.axis2.client.Options;
import org.apache.axis2.client.ServiceClient;

public class AXIOMClient {

    private static EndpointReference targetEPR = 
        new EndpointReference(
                              "http://localhost:8080/axis2/services/StockQuoteService");

    public static OMElement getPricePayload(String symbol) {
        OMFactory fac = OMAbstractFactory.getOMFactory();
        OMNamespace omNs = fac.createOMNamespace(
                                                 "http://quickstart.samples/xsd", "tns");

        OMElement method = fac.createOMElement("getPrice", omNs);
        OMElement value = fac.createOMElement("symbol", omNs);
        value.addChild(fac.createOMText(value, symbol));
        method.addChild(value);
        return method;
    }

    public static OMElement updatePayload(String symbol, double price) {
        OMFactory fac = OMAbstractFactory.getOMFactory();
        OMNamespace omNs = fac.createOMNamespace(
                                                 "http://quickstart.samples/xsd", "tns");

        OMElement method = fac.createOMElement("update", omNs);

        OMElement value1 = fac.createOMElement("symbol", omNs);
        value1.addChild(fac.createOMText(value1, symbol));
        method.addChild(value1);

        OMElement value2 = fac.createOMElement("price", omNs);
        value2.addChild(fac.createOMText(value2,
                                         Double.toString(price)));
        method.addChild(value2);
        return method;
    }

    public static void main(String[] args) {
        try {
            OMElement getPricePayload = getPricePayload("WSO");
            OMElement updatePayload = updatePayload("WSO", 123.42);
            Options options = new Options();
            options.setTo(targetEPR);
            options.setTransportInProtocol(Constants.TRANSPORT_HTTP);

            ServiceClient sender = new ServiceClient();
            sender.setOptions(options);

            sender.fireAndForget(updatePayload);
            System.err.println("price updated");
            Thread.sleep(3000);
            OMElement result = sender.sendReceive(getPricePayload);

            String response = result.getFirstElement().getText();
            System.err.println("Current price of WSO: " + response);

        } catch (Exception e) {
            e.printStackTrace();
        }
    }
    
}
