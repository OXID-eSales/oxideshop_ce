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

import javax.xml.ws.soap.AddressingFeature;
import javax.xml.ws.wsaddressing.W3CEndpointReference;

public class AddSEIClient {

    /**
     * @param args
     */
    public static void main(String[] args) {
        long start = System.currentTimeMillis();

        try {
            //Validate input
            if (args.length != 2)
                throw new IllegalArgumentException("Usage: java AddClient <integer 1> <integer 2>");

            int value0 = Integer.parseInt(args[0]);
            int value1 = Integer.parseInt(args[1]);

            //Retrieve ticket
            CalculatorService service = new CalculatorService();
            Calculator port1 = service.getCalculatorServicePort();
            W3CEndpointReference epr = port1.getTicket();

            //Add numbers
            Calculator port2 = epr.getPort(Calculator.class, new AddressingFeature());
            int answer = port2.add(value0, value1);
            System.out.println("The answer is: " + answer);
        }
        catch (Exception e) {
            e.printStackTrace();
        }
        finally {
            long end = System.currentTimeMillis();
            long time = end - start;
            System.out.println("Time: " + time + " ms.");
        }
    }
}
