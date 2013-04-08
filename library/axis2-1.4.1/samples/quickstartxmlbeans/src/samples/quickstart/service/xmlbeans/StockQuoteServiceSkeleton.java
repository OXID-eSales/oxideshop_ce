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

/**
 * StockQuoteServiceSkeleton.java
 *
 * This file was auto-generated from WSDL
 * by the Apache Axis2 version: 1.1-RC2 Nov 02, 2006 (02:37:50 LKT)
 */
package samples.quickstart.service.xmlbeans;
import samples.quickstart.service.xmlbeans.xsd.GetPriceDocument;
import samples.quickstart.service.xmlbeans.xsd.GetPriceResponseDocument;
import samples.quickstart.service.xmlbeans.xsd.UpdateDocument;

import java.util.HashMap;
/**
 * StockQuoteServiceSkeleton java skeleton for the axisService
 */
public class StockQuoteServiceSkeleton implements StockQuoteServiceSkeletonInterface {

    private HashMap map = new HashMap();

    public void update(UpdateDocument param0) {
        map.put(param0.getUpdate().getSymbol(), new Double(param0.getUpdate().getPrice()));
    }

    public GetPriceResponseDocument getPrice(GetPriceDocument param1) {
        Double price = (Double) map.get(param1.getGetPrice().getSymbol());
        double ret = 42;
        if(price != null){
            ret = price.doubleValue();
        }
        System.err.println();
        GetPriceResponseDocument resDoc =
                GetPriceResponseDocument.Factory.newInstance();
        GetPriceResponseDocument.GetPriceResponse res =
                resDoc.addNewGetPriceResponse();
        res.setReturn(ret);
        return resDoc;
    }
}
    