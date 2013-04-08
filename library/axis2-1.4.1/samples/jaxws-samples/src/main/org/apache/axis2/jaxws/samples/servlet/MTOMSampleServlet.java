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

import org.apache.axis2.jaxws.samples.client.mtom.SampleMTOMTests;

import javax.servlet.ServletContext;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.DataInputStream;
import java.io.FileOutputStream;
import java.io.IOException;

/**
 * Servlet implementation class for Servlet: MTOMSampleServlet
 * <p/>
 * web.servlet
 * name="MTOMSampleServlet"
 * display-name="MTOMSampleServlet"
 * description="Provides a servlet interface to the MTOM Service Sample"
 * <p/>
 * web.servlet-mapping
 * url-pattern="/MTOMSampleServlet"
 */
public class MTOMSampleServlet extends javax.servlet.http.HttpServlet implements javax.servlet.Servlet {
    private static final long serialVersionUID = 1039362106123493799L;
    private static final String INDEX_JSP_LOCATION = "/WEB-INF/jsp/demoMTOM.jsp";
    private String uriString = "";
    private String soapString = "";
    private String fileName = "";

    public MTOMSampleServlet() {
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
     *
     * @param req  - HttpServletRequest
     * @param resp - HttpServletResponse
     * @throws ServletException
     * @throws IOException
     */
    private void processRequest(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {
        ServletContext context = getServletContext();

        // Set the default values
        String svc = null;
        req.setAttribute("DispatchSelected", " ");
        req.setAttribute("ProxySelected", " ");

        // See if the form was posted 
        String contentType = req.getContentType();
        System.out.println(">>SERVLET: Content type is: " + contentType);

        // verify we have multipart/form-data
        if ((contentType != null)
                && (contentType.indexOf("multipart/form-data") >= 0)) {

            // Read the post data
            DataInputStream in = new DataInputStream(req
                    .getInputStream());
            int formDataLength = req.getContentLength();

            byte dataBytes[] = new byte[formDataLength];
            int byteRead = 0;
            int totalBytesRead = 0;
            while (totalBytesRead < formDataLength) {
                byteRead = in.read(dataBytes, totalBytesRead,
                        formDataLength);
                totalBytesRead += byteRead;
            }

            // Change it to a string
            String data = new String(dataBytes);

            // Parse out the parms
            svc = getparm("msgservice", data);
            uriString = getparm("uridef", data);
            soapString = getparm("soapdef", data);

            // Filename is part of the file data block.
            fileName = data.substring(data.indexOf("filename=\"") + 10);
            fileName = fileName.substring(0, fileName.indexOf("\n"));

            // Strip path info out, windows or unix
            fileName = fileName.substring(fileName.lastIndexOf("\\") + 1, fileName.indexOf("\""));
            fileName = fileName.substring(fileName.lastIndexOf("/") + 1);

            // Now look for the file data
            int lastIndex = contentType.lastIndexOf("=");
            String boundary = contentType.substring(lastIndex + 1,
                    contentType.length());

            int pos;
            pos = data.indexOf("filename=\"");
            pos = data.indexOf("\n", pos) + 1;
            pos = data.indexOf("\n", pos) + 1;
            pos = data.indexOf("\n", pos) + 1;

            // Determine the boundaries
            int boundaryLocation = data.indexOf(boundary, pos) - 4;
            int startPos = ((data.substring(0, pos)).getBytes()).length;
            int endPos = ((data.substring(0, boundaryLocation)).getBytes()).length;

            // Write the file locally
            FileOutputStream fileOut = new FileOutputStream(fileName);
            fileOut.write(dataBytes, startPos, (endPos - startPos));
            fileOut.flush();
            fileOut.close();

            System.out.println(">>SERVLET: File saved as " + fileName);
        }

        // Null means this is not a post, so we set the defaults
        if (null == svc || "" == svc) {
            // Set up the default values to use
            uriString = "http://localhost:" + req.getServerPort();
            soapString = "";
            req.setAttribute("uridef", uriString);
            req.setAttribute("soapdef", soapString);
            req.setAttribute("filedef", "");
            req.setAttribute("messageS", " ");
            req.setAttribute("messageR", " ");
            context.getRequestDispatcher(INDEX_JSP_LOCATION).forward(req, resp);
        } else {
            // This is a post, work with the data
            String result = "";

            // Setup soap return
            soapString = (null != soapString) ? "checked" : "";

            // Set the values to be on the refreshed page			
            req.setAttribute("uridef", uriString);
            req.setAttribute("soapdef", soapString);
            req.setAttribute("filedef", fileName);
            req.setAttribute(svc + "Selected", "selected");

            // Create an instance of the tests client
            SampleMTOMTests client = new SampleMTOMTests();
            System.out.println(">>SERVLET: Filename = " + fileName);

            // Call the test class
            try {
                client.setOptions(uriString, (0 != soapString.length()), fileName);
                if (svc.equals("Dispatch")) {
                    result = client.testMtomWithDispatch(result);
                } else {
                    result = client.testMtomWithProxy(result, getServletConfig().getServletContext().getResource("/WEB-INF/wsdl/ImageDepot12.wsdl"));
                }
            }
            catch (Exception e) {
                result = result.concat("ERROR: SERVLET EXCEPTION " + e);
                System.out.println(">>SERVLET: EXCEPTION " + e);
            }

            // Format the output and refresh the panel
            formatOutput(req, uriString, "Sending '" + fileName + "' via MTOM " + ((0 != soapString.length()) ? "SOAP 1.2" : "SOAP 1.1"), result);
            context.getRequestDispatcher(INDEX_JSP_LOCATION).forward(req, resp);
        }
    }

    /**
     * getparm
     * <p/>
     * parses the parameters from the multipart-form data
     *
     * @param parm - String to search for
     * @param data - String with form data
     * @return - String - the parm value or null if not found
     */
    private String getparm(String parm, String data) {
        String retval = null;
        parm = "\"" + parm + "\"";
        int pos = data.indexOf(parm);
        if (0 <= pos) {
            pos = data.indexOf("\n", pos) + 1;
            pos = data.indexOf("\n", pos) + 1;
            retval = data.substring(pos);
            if (null != retval) {
                retval = retval.substring(0, retval.indexOf("\n"));
                retval = retval.substring(0, retval.indexOf("\r"));
            }
        }
        return retval;
    }

    /**
     * formatOutput
     * <p/>
     * Format the transaction data into the HTML text area
     *
     * @param req         - HttpServletRequest
     * @param endpointURL - String
     * @param request     - String with what we send
     * @param received    - String returned value
     */
    private void formatOutput(HttpServletRequest req, String endpointURL,
                              String request, String received) {
        req.setAttribute("messageS", "\n" + "Connecting to... " + endpointURL
                + "\n\n" + "Request: \n" + request + "\n");
        req.setAttribute("messageR", "\n" + "Response: \n" + received
                + "\n");
    }
}