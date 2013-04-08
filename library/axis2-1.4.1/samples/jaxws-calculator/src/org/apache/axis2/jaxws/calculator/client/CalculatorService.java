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

import javax.xml.namespace.QName;
import javax.xml.ws.Service;
import javax.xml.ws.WebEndpoint;
import javax.xml.ws.WebServiceClient;
import javax.xml.ws.WebServiceFeature;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.logging.Logger;


@WebServiceClient(name = "CalculatorService", targetNamespace = "http://calculator.jaxws.axis2.apache.org", wsdlLocation = "file:/C:/Apache/axis2/modules/samples/jaxws-calculator/resources/META-INF/CalculatorService.wsdl")
public class CalculatorService
        extends Service {

    private final static URL CALCULATORSERVICE_WSDL_LOCATION;
    private final static Logger logger = Logger.getLogger(org.apache.axis2.jaxws.calculator.client.CalculatorService.class.getName());

    static {
        URL url = null;
        try {
            url = new URL("http://localhost:8080/axis2/services/CalculatorService.CalculatorServicePort?wsdl");
        } catch (MalformedURLException e) {
            e.printStackTrace();
        }
        CALCULATORSERVICE_WSDL_LOCATION = url;
    }

    public CalculatorService(URL wsdlLocation, QName serviceName) {
        super(wsdlLocation, serviceName);
    }

    public CalculatorService() {
        super(CALCULATORSERVICE_WSDL_LOCATION, new QName("http://calculator.jaxws.axis2.apache.org", "CalculatorService"));
    }

    /**
     * @return returns Calculator
     */
    @WebEndpoint(name = "CalculatorServicePort")
    public Calculator getCalculatorServicePort() {
        return super.getPort(new QName("http://calculator.jaxws.axis2.apache.org", "CalculatorServicePort"), Calculator.class);
    }

    /**
     * @param features A list of {@link javax.xml.ws.WebServiceFeature} to configure on the proxy.  Supported features not in the <code>features</code> parameter will have their default values.
     * @return returns Calculator
     */
    @WebEndpoint(name = "CalculatorServicePort")
    public Calculator getCalculatorServicePort(WebServiceFeature... features) {
        return super.getPort(new QName("http://calculator.jaxws.axis2.apache.org", "CalculatorServicePort"), Calculator.class, features);
    }

}
