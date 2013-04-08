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


package org.tempuri;

import java.net.MalformedURLException;
import java.net.URL;
import javax.xml.namespace.QName;
import javax.xml.ws.Service;
import javax.xml.ws.WebEndpoint;
import javax.xml.ws.WebServiceClient;

@WebServiceClient(name = "BaseDataTypesDocLitBService", targetNamespace = "http://tempuri.org/", wsdlLocation = "/tmp/BaseDataTypesDocLitB.wsdl")
public class BaseDataTypesDocLitBService
    extends Service
{

    private final static URL BASEDATATYPESDOCLITBSERVICE_WSDL_LOCATION;

    static {
        URL url = null;
        try {
            url = new URL("file:BaseDataTypesDocLitB.wsdl");
        } catch (MalformedURLException e) {
            e.printStackTrace();
        }
        BASEDATATYPESDOCLITBSERVICE_WSDL_LOCATION = url;
    }

    public BaseDataTypesDocLitBService(URL wsdlLocation, QName serviceName) {
        super(wsdlLocation, serviceName);
    }

    public BaseDataTypesDocLitBService() {
        super(BASEDATATYPESDOCLITBSERVICE_WSDL_LOCATION, new QName("http://tempuri.org/", "BaseDataTypesDocLitBService"));
    }

    /**
     * 
     * @return
     *     returns IBaseDataTypesDocLitB
     */
    @WebEndpoint(name = "BasicHttpBinding_IBaseDataTypesDocLitB")
    public IBaseDataTypesDocLitB getBasicHttpBindingIBaseDataTypesDocLitB() {
        return (IBaseDataTypesDocLitB)super.getPort(new QName("http://tempuri.org/", "BasicHttpBinding_IBaseDataTypesDocLitB"), IBaseDataTypesDocLitB.class);
    }

}
