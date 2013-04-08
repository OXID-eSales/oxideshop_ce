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
package org.apache.axis2.jaxws.samples.ping;

import javax.xml.ws.BindingType;
import javax.xml.ws.soap.SOAPBinding;


@javax.jws.WebService(endpointInterface = "org.apache.axis2.jaxws.samples.ping.PingService12PortType", targetNamespace = "http://org/apache/axis2/jaxws/samples/ping/", serviceName = "PingService12", portName = "PingService12Port", wsdlLocation = "WEB-INF/wsdl/Ping12.wsdl")
@BindingType(SOAPBinding.SOAP12HTTP_BINDING)
public class PingService12PortImpl {

    public void pingOperation(PingStringInput parameter) {
        System.out.println(">> SERVICE: SEI Ping SOAP12 Service: Request received.");
        if (parameter != null) {
            try {
                System.out.println(">> SERVICE: SOAP12 Ping Input String '" + parameter.pingInput + "'");
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
    }

}