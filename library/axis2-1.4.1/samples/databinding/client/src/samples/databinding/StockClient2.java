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
package samples.databinding;
import org.apache.axiom.om.OMAbstractFactory;
import org.apache.axiom.om.OMDataSource;
import org.apache.axiom.om.OMElement;
import org.apache.axiom.om.OMFactory;
import org.apache.axiom.om.OMOutputFormat;
import org.apache.axiom.om.impl.builder.SAXOMBuilder;
import org.jdom.Document;
import org.jdom.Element;
import org.jdom.JDOMException;
import org.jdom.input.StAXBuilder;
import org.jdom.output.SAXOutputter;
import org.jdom.output.StAXOutputter;
import org.jdom.output.XMLOutputter;
import org.jdom.xpath.XPath;

import javax.xml.stream.XMLStreamException;
import javax.xml.stream.XMLStreamReader;
import javax.xml.stream.XMLStreamWriter;
import java.io.IOException;
import java.io.OutputStream;
import java.io.Writer;
public final class StockClient2 {
    public static void main(String[] args) throws Exception {
        if (args.length != 2) {
            System.err.println("Usage: StockClient <url> <symbol>");
            return;
        }
        final String url = args[0];
        final String symbol = args[1];

        System.out.println();
        System.out.println("Getting Stock Quote for " + symbol);

        StockQuoteServiceStub stub =
                new StockQuoteServiceStub(url);
        stub._getServiceClient().getOptions().setAction("getStockQuote");

        Element element1 = new Element(
            "getStockQuote", "nsl", "http://w3.ibm.com/schemas/services/2002/11/15/stockquote");
        Element element2 = new Element("symbol");
        element2.addContent(symbol);
        element1.addContent(element2);

        OMFactory factory = OMAbstractFactory.getOMFactory();
        org.apache.axiom.om.OMDataSource src = new JDOMDataSource(element1);
        org.apache.axiom.om.OMNamespace appns = factory.createOMNamespace("http://w3.ibm.com/schemas/services/2002/11/15/stockquote", "ns1");
        OMElement child = factory.createOMElement(src, "getStockQuote", appns);

        OMElement response = stub.getStockQuote(child);

        StAXBuilder builder = new StAXBuilder();
        Document doc = builder.build(response.getXMLStreamReader());
        XPath path = XPath.newInstance("//price");
        Element price = (Element) path.selectSingleNode(doc.getRootElement());
        System.out.println("Price = " + price.getText());
    }

    private static class JDOMDataSource implements OMDataSource {
        private final Element data;

        private JDOMDataSource(Element data) {
            this.data = data;
        }

        public void serialize(OutputStream output, OMOutputFormat format) throws XMLStreamException {
            try {
                XMLOutputter outputter = new XMLOutputter();
                outputter.output(data, output);
            } catch (IOException e) {
                throw new XMLStreamException(e);
            }
        }

        public void serialize(Writer writer, OMOutputFormat format) throws XMLStreamException {
            try {
                XMLOutputter outputter = new XMLOutputter();
                outputter.output(data, writer);
            } catch (IOException e) {
                throw new XMLStreamException(e);
            }
        }

        public void serialize(XMLStreamWriter xmlWriter) throws XMLStreamException {
            StAXOutputter outputter = new StAXOutputter(xmlWriter);
            try {
                outputter.outputFragment(data);
            } catch (JDOMException e) {
                throw new XMLStreamException(e);
            }
        }

        public XMLStreamReader getReader() throws XMLStreamException {
            SAXOMBuilder builder = new SAXOMBuilder();
            SAXOutputter outputter = new SAXOutputter();
            outputter.setContentHandler(builder);
            outputter.setEntityResolver(builder);
            outputter.setDTDHandler(builder);
            outputter.setEntityResolver(builder);
            return builder.getRootElement().getXMLStreamReader();
        }
    }
}
