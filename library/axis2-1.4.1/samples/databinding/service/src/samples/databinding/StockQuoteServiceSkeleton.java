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
 * by the Apache Axis2 version: 1.1-SNAPSHOT Nov 03, 2006 (06:54:07 EST)
 */
package samples.databinding;
import javanet.staxutils.StAXSource;
import org.apache.axiom.om.impl.builder.SAXOMBuilder;
import org.apache.axis2.AxisFault;
import org.exolab.castor.xml.MarshalException;
import org.exolab.castor.xml.Marshaller;
import org.exolab.castor.xml.UnmarshalHandler;
import org.exolab.castor.xml.Unmarshaller;
import org.exolab.castor.xml.ValidationException;
import org.xml.sax.ContentHandler;
import org.xml.sax.SAXException;
import samples.databinding.data.Change;
import samples.databinding.data.GetStockQuote;
import samples.databinding.data.GetStockQuoteResponse;
import samples.databinding.data.LastTrade;
import samples.databinding.data.Quote;

import javax.xml.transform.TransformerException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.sax.SAXResult;
import java.io.IOException;
import java.util.Calendar;
/**
 * StockQuoteServiceSkeleton java skeleton for the axisService
 */
public class StockQuoteServiceSkeleton {
    /**
     * Auto generated method signature
     *
     * @param param0
     */
    public org.apache.axiom.om.OMElement getStockQuote(org.apache.axiom.om.OMElement param0) throws AxisFault {
        StAXSource staxSource =
                new StAXSource(param0.getXMLStreamReader());
        Unmarshaller unmarshaller = new Unmarshaller(GetStockQuote.class);
        UnmarshalHandler unmarshalHandler = unmarshaller.createHandler();
        GetStockQuote stockQuote;
        try {
            ContentHandler contentHandler = Unmarshaller.getContentHandler(unmarshalHandler);
            TransformerFactory.newInstance().newTransformer().transform(staxSource, new SAXResult(contentHandler));
            stockQuote = (GetStockQuote) unmarshalHandler.getObject();
        } catch (SAXException e) {
            throw new RuntimeException(e);
        } catch (TransformerException e) {
            throw new RuntimeException(e);
        }
        
        if (!stockQuote.getSymbol().equals("IBM")) {
			throw new AxisFault("StockQuote details for the symbol '"+ stockQuote.getSymbol() + "' are not available.");
        }
        GetStockQuoteResponse stockQuoteResponse = new GetStockQuoteResponse();
        Quote quote = new Quote();
        quote.setSymbol(stockQuote.getSymbol());
        quote.setVolume(5000);

        LastTrade lastTrade = new LastTrade();
        lastTrade.setPrice(99);
        lastTrade.setDate(Calendar.getInstance().getTimeInMillis());
        quote.setLastTrade(lastTrade);

        Change change = new Change();
        change.setDollar(1);
        change.setPercent(10);
        change.setPositive(true);
        quote.setChange(change);

        stockQuoteResponse.setQuote(quote);
        SAXOMBuilder builder = new SAXOMBuilder();
        try {
            Marshaller.marshal(stockQuoteResponse, builder);
        } catch (MarshalException e) {
            throw new RuntimeException(e);
        } catch (ValidationException e) {
            throw new RuntimeException(e);
        } catch (IOException e) {
            throw new RuntimeException(e);
        }
        return builder.getRootElement();
    }
}
    
