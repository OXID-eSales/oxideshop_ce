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
package org.apache.axis2.jaxws.samples.servlet;

import org.apache.axis2.jaxws.samples.client.SampleClient;
import org.apache.axis2.jaxws.ClientConfigurationFactory;
import org.apache.axis2.metadata.registry.MetadataFactoryRegistry;
import org.apache.axis2.deployment.FileSystemConfigurator;

import javax.servlet.Servlet;
import javax.servlet.ServletContext;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.IOException;
import java.net.URL;
import java.net.MalformedURLException;

/**
 * EchoPingSampleServlet main entry point for JSP servlet
 */
public class EchoPingSampleServlet extends HttpServlet implements Servlet {

    private static final int TIMEOUT = 240; // Async timeout
    private static final long serialVersionUID = 1039362106123493799L;
    private static final String CONTEXT_BASE = "/jaxws-samples/services/";
    private static final String PING_CONTEXT = CONTEXT_BASE + "PingService.PingServicePort";
    private static final String ECHO_CONTEXT = CONTEXT_BASE + "EchoService.EchoServicePort";
    private static final String PING_CONTEXT12 = CONTEXT_BASE + "PingService12.PingService12Port";
    private static final String ECHO_CONTEXT12 = CONTEXT_BASE + "EchoService12.EchoService12Port";
    private static final String INDEX_JSP_LOCATION = "/WEB-INF/jsp/demoEchoPing.jsp";
    private static final String PING_RESPONSE_GOOD = "Message delivered successfully. Please check server logs to confirm message delivery.";
    private static final String PING_RESPONSE_BAD = "ERROR: Failure in client before message delivery.";
    private String uriString = "";
    private String soapString = "";
    private int count = 1;
    private ClientConfigurationFactory factory = null;

    public EchoPingSampleServlet() {
        super();
    }

    protected void doGet(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {
        processRequest(req, resp);
    }

    protected void doPost(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {
        processRequest(req, resp);
    }

    /**
     * processRequest Reads the posted parameters and calls the service
     */
    private void processRequest(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {
        
        if(factory == null) {
            String path = getServletConfig().getServletContext().getRealPath("/WEB-INF/axis2.xml");
            FileSystemConfigurator configurator = new FileSystemConfigurator(null, path);
            factory = new ClientConfigurationFactory(configurator);
            MetadataFactoryRegistry.setFactory(ClientConfigurationFactory.class, factory);
        }

        ServletContext context = getServletContext();
        String msgString = req.getParameter("msgstring");
        String svc = req.getParameter("msgservice");
        String cnt = req.getParameter("msgcount");
        String result = "";
        req.setAttribute("PingSelected", " ");
        req.setAttribute("EchoSelected", " ");
        req.setAttribute("AsyncSelected", " ");
        req.setAttribute("AsyncWireSelected", " ");

        if (null == msgString || "" == msgString) {
            // Set up the default values to use
            uriString = "http://localhost:" + req.getServerPort();
            soapString = "";
            formatOutput(req, uriString, "", "");
            req.setAttribute("uridef", uriString);
            req.setAttribute("soapdef", soapString);
            req.setAttribute("messageS", " ");
            req.setAttribute("messageR", " ");
            req.setAttribute("msgcount", new Integer(count).toString());
            req.setAttribute("PingSelected", "selected");
            context.getRequestDispatcher(INDEX_JSP_LOCATION).forward(req, resp);
        } else {
            // Get the parms from the request
            uriString = req.getParameter("uri");
            soapString = req.getParameter("soap12");
            if (null != soapString) {
                soapString = "checked";
            } else {
                soapString = "";
            }
            // Get count
            if ((null != cnt) && ("" != cnt)) {
                count = new Integer(cnt).intValue();
            }

            // Set the values to be on the refreshed page			
            req.setAttribute("msgstring", req.getAttribute("msgstring"));
            req.setAttribute("uridef", uriString);
            req.setAttribute("soapdef", soapString);
            req.setAttribute("msgcount", new Integer(count).toString());
            req.setAttribute(svc + "Selected", "selected");

            // Now call the service
            SampleClient client = new SampleClient();
            client.setClientConfigurationFactory(factory);
            System.out.println(">> SERVLET: Request count = " + count);

            // Loop on the count
            for (int index = 0; index < count; index++) {
                System.out.println(">> SERVLET: Request index: " + (index + 1));
                if (0 == soapString.length()) {
                    if (svc.equalsIgnoreCase(("Async"))) {
                        result += client.buildAsync(uriString + ECHO_CONTEXT, getWSDLURL("/WEB-INF/wsdl/Echo.wsdl"), msgString,
                                TIMEOUT, false);
                    } else if (svc.equalsIgnoreCase(("AsyncWire"))) {
                        result += client.buildAsync(uriString + ECHO_CONTEXT, getWSDLURL("/WEB-INF/wsdl/Echo.wsdl"), msgString,
                                TIMEOUT, true);
                    } else if (svc.equalsIgnoreCase("Echo")) {
                        result += client.buildEcho(uriString + ECHO_CONTEXT, getWSDLURL("/WEB-INF/wsdl/Echo.wsdl"), msgString);
                    } else {
                        if (client.buildPing(uriString + PING_CONTEXT, getWSDLURL("/WEB-INF/wsdl/Ping.wsdl"), msgString)) {
                            result += PING_RESPONSE_GOOD;
                        } else {
                            result += PING_RESPONSE_BAD;
                        }
                    }
                } else  // SOAP1.2
                {
                    if (svc.equalsIgnoreCase(("Async"))) {
                        result += client.buildAsync12(uriString + ECHO_CONTEXT12, getWSDLURL("/WEB-INF/wsdl/Echo12.wsdl"), msgString,
                                TIMEOUT, false);
                    } else if (svc.equalsIgnoreCase(("AsyncWire"))) {
                        result += client.buildAsync12(uriString + ECHO_CONTEXT12, getWSDLURL("/WEB-INF/wsdl/Echo12.wsdl"), msgString,
                                TIMEOUT, true);
                    } else if (svc.equalsIgnoreCase("Echo")) {
                        result += client.buildEcho12(uriString + ECHO_CONTEXT12, getWSDLURL("/WEB-INF/wsdl/Echo12.wsdl"), msgString);
                    } else {
                        if (client.buildPing12(uriString + PING_CONTEXT12, getWSDLURL("/WEB-INF/wsdl/Ping12.wsdl"), msgString)) {
                            result += PING_RESPONSE_GOOD;
                        } else {
                            result += PING_RESPONSE_BAD;
                        }
                    }
                }
                result += "\n";
            }

            // Format the output and refresh the panel
            formatOutput(req, uriString, msgString, result);
            context.getRequestDispatcher(INDEX_JSP_LOCATION).forward(req, resp);
        }
    }

    /**
     * formatOutput Format the transaction data into the HTML text area
     */
    private void formatOutput(HttpServletRequest req, String endpointURL,
                              String request, String received) {
        req.setAttribute("messageS", "\n" + "Connecting to... " + endpointURL
                + "\n\n" + "Message Request: \n" + request + "\n");
        req.setAttribute("messageR", "\n" + "Message Response: \n" + received
                + "\n");
    }

    private URL getWSDLURL(String file) throws MalformedURLException {
        return getServletConfig().getServletContext().getResource(file);
    }
}