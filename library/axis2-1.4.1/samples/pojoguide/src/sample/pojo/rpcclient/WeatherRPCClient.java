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
package sample.pojo.rpcclient;

import javax.xml.namespace.QName;

import org.apache.axis2.AxisFault;
import org.apache.axis2.addressing.EndpointReference;
import org.apache.axis2.client.Options;
import org.apache.axis2.rpc.client.RPCServiceClient;

import sample.pojo.data.Weather;


public class WeatherRPCClient {

    public static void main(String[] args1) throws AxisFault {

        RPCServiceClient serviceClient = new RPCServiceClient();

        Options options = serviceClient.getOptions();

        EndpointReference targetEPR = new EndpointReference("http://localhost:8080/axis2/services/WeatherService");

        options.setTo(targetEPR);

        // Setting the weather
        QName opSetWeather = new QName("http://service.pojo.sample", "setWeather");

        Weather w = new Weather();

        w.setTemperature((float)39.3);
        w.setForecast("Cloudy with showers");
        w.setRain(true);
        w.setHowMuchRain((float)4.5);

        Object[] opSetWeatherArgs = new Object[] { w };

        serviceClient.invokeRobust(opSetWeather, opSetWeatherArgs);


        // Getting the weather
        QName opGetWeather = new QName("http://service.pojo.sample", "getWeather");

        Object[] opGetWeatherArgs = new Object[] { };
        Class[] returnTypes = new Class[] { Weather.class };
        
        
        Object[] response = serviceClient.invokeBlocking(opGetWeather,
                opGetWeatherArgs, returnTypes);
        
        Weather result = (Weather) response[0];
        
        if (result == null) {
            System.out.println("Weather didn't initialize!");
            return;
        }
        
        // Displaying the result
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
