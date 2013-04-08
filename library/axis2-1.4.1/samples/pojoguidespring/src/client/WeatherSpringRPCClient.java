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
package client;

import javax.xml.namespace.QName;

import org.apache.axis2.AxisFault;
import org.apache.axis2.addressing.EndpointReference;
import org.apache.axis2.client.Options;
import org.apache.axis2.rpc.client.RPCServiceClient;

import sample.spring.bean.Weather;

public class WeatherSpringRPCClient {

    public static void main(String[] args1) throws AxisFault {

        RPCServiceClient serviceClient = new RPCServiceClient();

        Options options = serviceClient.getOptions();

        EndpointReference targetEPR 
                = new EndpointReference(
                "http://localhost:8080/axis2/services/WeatherSpringService"); 
        
        options.setTo(targetEPR);

        // Get the weather (no setting, the Spring Framework has already initialized it for us)
        QName opGetWeather = new QName("http://service.spring.sample/xsd", "getWeather");

        Object[] opGetWeatherArgs = new Object[] { };
        Class[] returnTypes = new Class[] { Weather.class };
        
        
        Object[] response = serviceClient.invokeBlocking(opGetWeather,
                opGetWeatherArgs, returnTypes);
        
        Weather result = (Weather) response[0];
        
        // display results
        if (result == null) {
            System.out.println("Weather didn't initialize!");
        }else{
            System.out.println("Temperature               : " +
                               result.getTemperature());
            System.out.println("Forecast                  : " +
                               result.getForecast());
            System.out.println("Rain                      : " +
                               result.getRain());
            System.out.println("How much rain (in inches) : " +
                               result.getHowMuchRain());
        }
    }
}
