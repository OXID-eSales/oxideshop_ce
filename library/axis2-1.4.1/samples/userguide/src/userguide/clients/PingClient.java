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
import org.apache.axis2.addressing.EndpointReference;
import org.apache.axis2.client.Options;
import org.apache.axis2.client.ServiceClient;

/**
 * Sample for fire-and-forget service invocation
 * Message Exchage Pattern IN-Only
 */
public class PingClient {
    private static EndpointReference targetEPR = new EndpointReference("http://localhost:8080/axis2/services/MyService");

    public static void main(String[] args) {
        try {
            OMElement payload = ClientUtil.getPingOMElement();
            ServiceClient serviceClient = new ServiceClient();

            Options options = new Options();
            serviceClient.setOptions(options);
            options.setTo(targetEPR);
            options.setAction("urn:ping");

            serviceClient.fireAndForget(payload);
            /**
             * We have to bock this thread untill we send the request , the problem
             * is if we go out of the main thread , then request wont send ,so
             * you have to wait some time :)
             */
          Thread.sleep(500);

        } catch (AxisFault axisFault) {
            axisFault.printStackTrace();
        } catch (InterruptedException e) {
            //
        }
    }

}
