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

package org.apache.axis2.jaxws.samples.client.mtom;

import org.apache.axis2.jaxws.samples.mtom.ImageDepot;
import org.apache.axis2.jaxws.samples.mtom.ObjectFactory;
import org.apache.axis2.jaxws.samples.mtom.SendImage;
import org.apache.axis2.jaxws.samples.mtom.SendImageResponse;

import javax.activation.DataHandler;
import javax.activation.FileDataSource;
import javax.xml.bind.JAXBContext;
import javax.xml.namespace.QName;
import javax.xml.ws.BindingProvider;
import javax.xml.ws.Dispatch;
import javax.xml.ws.Service;
import javax.xml.ws.soap.SOAPBinding;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.net.URL;

/**
 * A sample client that uses MTOM to send an image using both the JAX-WS
 * Dispatch and the Proxy.
 */
public class SampleMTOMTests {
    private String urlHost = "localhost";
    private String urlPort = "8080";
    private String urlSuffix = "";
    private static final String CONTEXT_BASE = "/jaxws-samples/services/";
    private static final String MTOM_CONTEXT11 = CONTEXT_BASE + "MtomSampleService.MtomSamplePort";
    private static final String MTOM_CONTEXT12 = CONTEXT_BASE + "MtomSampleService12.MtomSamplePort";
    private static final String NAMESPACE = "http://org/apache/axis2/jaxws/samples/mtom/";
    private String uriString = "http://" + urlHost + ":" + urlPort;
    private QName serviceName11 = new QName(NAMESPACE, "MtomSampleService");
    private QName serviceName12 = new QName(NAMESPACE, "MtomSampleService12");
    private QName portNameDispatch = new QName(NAMESPACE, "MtomSamplePortDispatch");
    private DataHandler content;
    private String imageFilename = "";
    private Boolean tryDispatch = true;
    private Boolean tryProxy = true;
    private Boolean soap12 = false;

    /**
     * Command Line Program Entry Point
     *
     * @param args - see printUsage output
     */
    public static void main(String[] args) throws Exception {
        SampleMTOMTests tests = new SampleMTOMTests();
        tests.parseArgs(args);
        if (tests.tryDispatch) {
            tests.testMtomWithDispatch(null);
        }
        if (tests.tryProxy) {
            tests.testMtomWithProxy(null, null);
        }
    }

    /**
     * Read and interpret the command-line arguments.
     * See printUsage output for supported parameters
     *
     * @param args String[] From the Command Line
     */
    public void parseArgs(String[] args) {
        // Parse the arguments

        if (args.length >= 1) {
            for (int i = 0; i < args.length; i++) {
                try {
                    if ('-' == args[i].charAt(0)) {
                        switch (args[i].charAt(1)) {
                            case '?':
                                printUsage(null);
                                System.exit(0);
                                break;
                            case 'd':
                            case 'D':
                                tryDispatch = true;
                                tryProxy = false;
                                break;
                            case 'x':
                            case 'X':
                                tryDispatch = false;
                                tryProxy = true;
                                break;
                            case 'h':
                            case 'H':
                                urlHost = args[++i];
                                break;
                            case 'p':
                            case 'P':
                                urlPort = args[++i];
                                break;
                            case 'f':
                            case 'F':
                                urlSuffix = args[++i];
                                break;
                            case 'i':
                            case 'I':
                                imageFilename = args[++i];
                                break;
                            case '1':
                                soap12 = false;
                                break;
                            case '2':
                                soap12 = true;
                                break;
                            default:
                                printUsage(args[i]);
                                System.exit(0);
                                break;
                        }
                    }

                } catch (Exception e) {
                    System.out.println("Invalid option format.");
                    printUsage(null);
                    System.exit(0);
                }
                uriString = "http://" + urlHost + ":" + urlPort;
            }
        }

        // Display a warning if there is no input filename
        if (0 == imageFilename.length()) {
            System.out
                    .println(">> [WARNING} - No attachment file specified.  No data will be included.");
        }

        // Use the default suffix if it was not specified
        if (0 == urlSuffix.length()) {
            if (soap12) {
                urlSuffix = MTOM_CONTEXT12;
            } else {
                urlSuffix = MTOM_CONTEXT11;
            }
        }
    }

    /**
     * Set multiple class variables and init content.
     *
     * @param uri      - String - base service URI
     * @param soapdef  - Boolean - true for SOAP 1.2
     * @param fileName - source file to send
     */
    public void setOptions(String uri, Boolean soapdef, String fileName)
            throws Exception {
        uriString = uri;
        urlSuffix = (soapdef) ? MTOM_CONTEXT12 : MTOM_CONTEXT11;
        soap12 = soapdef;
        imageFilename = fileName;
        init();
    }

    /**
     * Print usage help to output
     *
     * @param invalidOpt -
     *                   if non-null, is the invalid parameter
     */
    private void printUsage(String invalidOpt) {
        if (null != invalidOpt) {
            System.out.println("Invalid Option: " + invalidOpt);
        }
        System.out.println("Usage:");
        System.out
                .println("  runSampleMtom -h [hostname] -p [port] -f [urlSuffix] -i [imageFilename] -d -x -1 -2");
        System.out.println("Default values:");
        System.out.println("  hostname = localhost");
        System.out.println("  port = 8080");
        System.out.println("  urlSuffix = /jaxws-samples/services/MtomSampleService.MtomSamplePort");
        System.out.println("  -d = Use Dispatch Only");
        System.out.println("  -x = Use Proxy Only");
        System.out.println("  -1 = soap 1.1 (default)");
        System.out.println("  -2 = soap 1.2");
    }

    /**
     * Creates the content object if required
     */
    private void init() throws Exception {
        if (content == null) {
            content = getBinaryContent();
        }
    }

    /**
     * Tests sending an image with MTOM, using the JAX-WS Dispatch API.
     *
     * @param result - String - used to pass back results to servlet.
     * @return String - the input string or null
     * @throws Exception
     */
    public String testMtomWithDispatch(String result) throws Exception {
        Service svc;
        System.out.println(">>---------------------------------------");
        System.out.println(">>MTOM Dispatch Test");

        init();

        // Set the data inside of the appropriate object
        ImageDepot imageDepot = new ObjectFactory().createImageDepot();
        imageDepot.setImageData(content);

        if (soap12) {
            svc = Service.create(serviceName12);
            svc.addPort(portNameDispatch, SOAPBinding.SOAP12HTTP_BINDING, uriString + urlSuffix);
        } else {
            svc = Service.create(serviceName11);
            svc.addPort(portNameDispatch, SOAPBinding.SOAP11HTTP_BINDING, uriString + urlSuffix);
        }

        // Setup the necessary JAX-WS artifacts
        JAXBContext jbc = JAXBContext
                .newInstance("org.apache.axis2.jaxws.samples.mtom");
        Dispatch<Object> dispatch = svc.createDispatch(portNameDispatch, jbc,
                Service.Mode.PAYLOAD);
        BindingProvider bp = (BindingProvider) dispatch;
        bp.getRequestContext().put(BindingProvider.SOAPACTION_USE_PROPERTY,
                Boolean.TRUE);
        bp.getRequestContext().put(BindingProvider.SOAPACTION_URI_PROPERTY,
                "sendImage");

        // Set the actual flag to enable MTOM
        SOAPBinding binding = (SOAPBinding) dispatch.getBinding();
        binding.setMTOMEnabled(true);

        // Create the request wrapper bean
        ObjectFactory factory = new ObjectFactory();
        SendImage request = factory.createSendImage();
        request.setInput(imageDepot);

        if (null != result) {
            result = result.concat("Invoking Dispatch<Object> with a binary payload\n");
        } else {
            System.out
                    .println(">>MTOM Invoking Dispatch<Object> with a binary payload");
        }

        // Send the image and process the response image
        try {
            SendImageResponse response = (SendImageResponse) dispatch.invoke(request);
            if (null != result) {
                if (response != null) {
                    result = result.concat("MTOM Dispatch Response received - " + response.getOutput().getImageData().getContentType());
                } else {
                    result = result.concat("ERROR: MTOM Dispatch NULL Response received");
                }
            } else {
                if (response != null) {
                    System.out.println(">>MTOM Response received");
                    System.out
                            .println(">>MTOM Writing returned image to dispatch_response.gif");
                    ImageDepot responseContent = response.getOutput();
                    processImageDepot(responseContent, "dispatch_response.gif");
                } else {
                    System.out.println(">> [ERROR] - Response from the server was NULL");
                }

            }
        }
        catch (Exception e) {
            if (null != result) {
                result = result.concat(">> [ERROR] - Exception making connection.");
            }
            System.out.println(">> [ERROR] - Exception making connection.");
            e.printStackTrace();
        }
        System.out.println(">>MTOM Dispatch Done");
        return (result);
    }

    /**
     * Tests sending an image with MTOM, using a JAX-WS Dynamic Proxy.
     *
     * @param result - String - used to pass back results to servlet.
     * @return String - the input string or null
     * @throws Exception
     */
    public String testMtomWithProxy(String result, URL url) throws Exception {
        ImageDepot response;
        System.out.println(">>---------------------------------------");
        System.out.println(">>MTOM Proxy Test");

        init();

        // Set the data inside of the appropriate object
        ImageDepot imageDepot = new ObjectFactory().createImageDepot();
        imageDepot.setImageData(content);

        if (null != result) {
            result = result.concat("Invoking MTOM proxy with a binary payload\n");
        } else {
            System.out.println(">>MTOM Invoking proxy with a binary payload");
        }

        // Setup the necessary JAX-WS artifacts
        try {
            if (soap12) {
                // Use the generated proxy
                MtomSample12PortProxy proxy = new MtomSample12PortProxy(url);
                proxy._getDescriptor().setEndpoint(uriString + urlSuffix);

                // Enable MTOM
                BindingProvider bp = (BindingProvider) proxy._getDescriptor().getProxy();
                SOAPBinding binding = (SOAPBinding) bp.getBinding();
                binding.setMTOMEnabled(true);

                // Send the image and process the response image
                response = proxy.sendImage(imageDepot);
            } else {
                // SOAP 1.1 Create the service
                //				 Use the generated proxy
                MtomSamplePortProxy proxy = new MtomSamplePortProxy(url);
                proxy._getDescriptor().setEndpoint(uriString + urlSuffix);

                // Enable MTOM
                BindingProvider bp = (BindingProvider) proxy._getDescriptor().getProxy();
                SOAPBinding binding = (SOAPBinding) bp.getBinding();
                binding.setMTOMEnabled(true);

                // Send the image and process the response image
                response = proxy.sendImage(imageDepot);
            }

            if (null != result) {
                if (response != null) {
                    result = result.concat("MTOM Proxy Response received - " + response.getImageData().getContentType());
                } else {
                    result = result.concat("ERROR: MTOM Proxy NULL Response received\n");
                }
            } else {
                if (response != null) {
                    System.out.println(">>MTOM Response received");
                    System.out
                            .println(">>MTOM Writing returned image to proxy_response.gif");
                    processImageDepot(response, "proxy_response.gif");
                } else {
                    System.out
                            .println(">> [ERROR] - Response from the server was NULL");
                }
            }
        }
        catch (Exception e) {
            if (null != result) {
                result = result.concat(">> [ERROR] - Exception making connection.");
            }
            System.out.println(">> [ERROR] - Exception making connection.");
            e.printStackTrace();
        }

        System.out.println(">>MTOM Proxy Done");
        return (result);
    }

    /*
      * Get a DataHandler that contains the binary data we'd like to send.
      * @return DataHandler
      * @throws Exception
      */
    private DataHandler getBinaryContent() throws Exception {
        DataHandler dh = null;
        if (imageFilename != null) {
            File file = new File(imageFilename);
            if (!file.exists()) {
                throw new FileNotFoundException();
            }
            System.out.println(">>MTOM Loading data from: '"
                    + file.toURI().toURL().toExternalForm() + "'");
            FileDataSource fds = new FileDataSource(file);
            dh = new DataHandler(fds);
        }

        return dh;
    }

    /*
      * Takes the data from the ImageDepot and writes it out to a file named by
      * the provided String.
      * @param ImageDepot data
      * @param String fname
      * @throws Exception
      */
    private void processImageDepot(ImageDepot data, String fname)
            throws Exception {
        DataHandler dh = data.getImageData();
        if (dh != null) {
            File f = new File(fname);
            if (f.exists()) {
                f.delete();
            }

            FileOutputStream fos = new FileOutputStream(f);
            dh.writeTo(fos);
        } else {
            System.out
                    .println(">> [ERROR] - ImageDepot was not null, but did not contain binary data");
        }
    }
}
