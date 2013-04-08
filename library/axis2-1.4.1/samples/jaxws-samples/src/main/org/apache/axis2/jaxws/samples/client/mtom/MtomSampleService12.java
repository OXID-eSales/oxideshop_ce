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

package org.apache.axis2.jaxws.samples.client.mtom;

import org.apache.axis2.jaxws.samples.mtom.MtomSample12;

import javax.xml.namespace.QName;
import javax.xml.ws.Service;
import javax.xml.ws.WebEndpoint;
import javax.xml.ws.WebServiceClient;
import java.net.MalformedURLException;
import java.net.URL;


@WebServiceClient(name = "MtomSampleService12",
        targetNamespace = "http://org/apache/axis2/jaxws/samples/mtom/",
        wsdlLocation = "WEB-INF/wsdl/ImageDepot12.wsdl")
public class MtomSampleService12
        extends Service {

    private final static URL MTOMSAMPLESERVICE12_WSDL_LOCATION;

    static {
        URL url = null;
        try {
            url = new URL("file:/WEB-INF/wsdl/ImageDepot12.wsdl");
        } catch (MalformedURLException e) {
            e.printStackTrace();
        }
        MTOMSAMPLESERVICE12_WSDL_LOCATION = url;
    }

    public MtomSampleService12(URL wsdlLocation, QName serviceName) {
        super(wsdlLocation, serviceName);
    }

    public MtomSampleService12() {
        super(MTOMSAMPLESERVICE12_WSDL_LOCATION, new QName("http://org/apache/axis2/jaxws/samples/mtom/", "MtomSampleService12"));
    }

    /**
     * @return returns MtomSample12
     */
    @WebEndpoint(name = "MtomSamplePort")
    public MtomSample12 getMtomSamplePort() {
        return (MtomSample12) super.getPort(new QName("http://org/apache/axis2/jaxws/samples/mtom/", "MtomSamplePort"), MtomSample12.class);
    }

}
