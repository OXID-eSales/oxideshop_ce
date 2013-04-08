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

package sample.yahooservices.RESTSearch;

import org.apache.axiom.om.OMAbstractFactory;
import org.apache.axiom.om.OMElement;
import org.apache.axiom.om.OMFactory;
import org.apache.axis2.Constants;
import org.apache.axis2.addressing.EndpointReference;
import org.apache.axis2.client.Options;
import org.apache.axis2.client.ServiceClient;

import java.io.UnsupportedEncodingException;
import java.net.URLEncoder;
import java.util.Iterator;

public class RESTSearchModel {
    /**
     * HTML Header to desplay snippet text
     */
    private String beginHTML = "<HTML><HEAD><TITLE>Search Results</TITLE></HEAD><BODY>";

    /**
     * HTML footer
     */
    private String endHTML = "</BODY></HTML>";

    /**
     * Store the texts read by NavigationURL of soap
     */
    private String snippet;

    public String searchYahoo(String query, String format) {
        try {
            snippet = beginHTML;
            String epr = "http://search.yahooapis.com/WebSearchService/V1/webSearch";

            ServiceClient client = new ServiceClient();
            Options options = new Options();
            client.setOptions(options);
            options.setTo(new EndpointReference(epr));
            options.setProperty(Constants.Configuration.ENABLE_REST, Constants.VALUE_TRUE);
            options.setProperty(Constants.Configuration.HTTP_METHOD, Constants.Configuration.HTTP_METHOD_GET);
	    options.setProperty(Constants.Configuration.MESSAGE_TYPE,org.apache.axis2.transport.http.HTTPConstants.MEDIA_TYPE_X_WWW_FORM);

            //if post is through GET of HTTP
            OMElement response = client.sendReceive(getPayloadForYahooSearchCall(query, format));
            generateSnippet(response);
            return snippet;

        } catch (Exception e) {
            e.printStackTrace();
            snippet = "<H2>Error occurred during the invocation to Yahoo search service</H2>" +
                    "<p>" + e.getMessage() + "</p>" + endHTML;
        }
        return null;
    }

    private static OMElement getPayloadForYahooSearchCall(String queryStr, String formatStr) throws UnsupportedEncodingException {
        OMFactory fac = OMAbstractFactory.getOMFactory();
        OMElement rootElement = fac.createOMElement("webSearch", null);

        OMElement appId = fac.createOMElement("appid", null, rootElement);
        appId.setText("ApacheRestDemo");

        OMElement query = fac.createOMElement("query", null, rootElement);
        query.setText(URLEncoder.encode(queryStr, "UTF-8"));

        if (formatStr != null && formatStr.length() != 0) {
            OMElement format = fac.createOMElement("format", null, rootElement);
            format.setText(URLEncoder.encode(formatStr, "UTF-8"));
        }
        return rootElement;
    }

    private void generateSnippet(OMElement response) {
        String title = null;
        String summary = null;
        String clickUrl = null;
        String url = null;
        OMElement result = null;
        //get an iterator for Result elements
        Iterator itr = response.getChildElements();
        Iterator innerItr;
        while (itr.hasNext()) {
            result = (OMElement) itr.next();
            innerItr = result.getChildElements();
            title = ((OMElement) innerItr.next()).getText();
            summary = ((OMElement) innerItr.next()).getText();
            url = ((OMElement) innerItr.next()).getText();
            clickUrl = ((OMElement) innerItr.next()).getText();
            snippet += "<a href=" + clickUrl + ">" + title + "</a>" + "<br>" + summary +
                    "<br>" + url + "<br>" + "<br>";
        }
        snippet += endHTML;
    }
}

