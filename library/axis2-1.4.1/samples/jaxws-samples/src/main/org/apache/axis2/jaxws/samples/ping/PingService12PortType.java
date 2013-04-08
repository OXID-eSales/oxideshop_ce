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

import javax.jws.Oneway;
import javax.jws.WebMethod;
import javax.jws.WebParam;
import javax.jws.WebService;
import javax.jws.soap.SOAPBinding;
import javax.jws.soap.SOAPBinding.ParameterStyle;


@WebService(name = "PingService12PortType", targetNamespace = "http://org/apache/axis2/jaxws/samples/ping/")
@SOAPBinding(parameterStyle = ParameterStyle.BARE)
public interface PingService12PortType {


    /**
     * @param parameter
     */
    @WebMethod(action = "pingOperation")
    @Oneway
    public void pingOperation(
            @WebParam(name = "pingStringInput", targetNamespace = "http://org/apache/axis2/jaxws/samples/ping/", partName = "parameter")
            PingStringInput parameter);

}
