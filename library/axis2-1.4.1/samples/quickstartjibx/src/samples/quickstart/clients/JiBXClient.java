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

import samples.quickstart.service.jibx.StockQuoteServiceStub;

public class JiBXClient{
    public static void main(java.lang.String args[]){
        try{
            StockQuoteServiceStub stub =
                new StockQuoteServiceStub
                ("http://localhost:8080/axis2/services/StockQuoteService");

            getPrice(stub);
            update(stub);
            getPrice(stub);

        } catch(Exception e){
            e.printStackTrace();
            System.err.println("\n\n\n");
        }
    }

    /* fire and forget */
    public static void update(StockQuoteServiceStub stub){
        try{
            stub.update("CDE", new Double(42.35));
            System.err.println("price updated");
        } catch(Exception e){
            e.printStackTrace();
            System.err.println("\n\n\n");
        }
    }

    /* two way call/receive */
    public static void getPrice(StockQuoteServiceStub stub){
        try{
            System.err.println(stub.getPrice("CDE"));
        } catch(Exception e){
            e.printStackTrace();
            System.err.println("\n\n\n");
        }
    }

}
