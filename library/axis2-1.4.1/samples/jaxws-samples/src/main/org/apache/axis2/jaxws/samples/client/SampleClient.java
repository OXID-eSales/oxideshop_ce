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
package org.apache.axis2.jaxws.samples.client;

import org.apache.axis2.jaxws.samples.client.echo.EchoService12PortProxy;
import org.apache.axis2.jaxws.samples.client.echo.EchoServiceCallbackHandler;
import org.apache.axis2.jaxws.samples.client.echo.EchoServicePortProxy;
import org.apache.axis2.jaxws.samples.client.ping.PingService12PortProxy;
import org.apache.axis2.jaxws.samples.client.ping.PingServicePortProxy;
import org.apache.axis2.jaxws.samples.echo.EchoStringInput;
import org.apache.axis2.jaxws.samples.echo.EchoStringResponse;
import org.apache.axis2.jaxws.samples.ping.ObjectFactory;
import org.apache.axis2.jaxws.samples.ping.PingStringInput;
import org.apache.axis2.jaxws.ClientConfigurationFactory;
import org.apache.axis2.metadata.registry.MetadataFactoryRegistry;
import org.apache.axis2.AxisFault;
import org.apache.axis2.deployment.FileSystemConfigurator;
import org.apache.axis2.context.ConfigurationContextFactory;
import org.apache.axis2.context.ConfigurationContext;

import javax.xml.namespace.QName;
import javax.xml.ws.BindingProvider;
import java.util.concurrent.Future;
import java.net.URL;

/**
 * SampleClient
 * main entry point for thinclient jar sample
 * and worker class to communicate with the services
 */
public class SampleClient {

    private int timeout = 240;                 // Error timeout in seconds
    private static final int SLEEPER = 2;     // Poll delay for async
    private String urlHost = "localhost";
    private String urlPort = "8080";
    private static final String CONTEXT_BASE = "/jaxws-samples/services/";
    private static final String PING_CONTEXT = CONTEXT_BASE + "PingService.PingServicePort";
    private static final String ECHO_CONTEXT = CONTEXT_BASE + "EchoService.EchoServicePort";
    private static final String PING_CONTEXT12 = CONTEXT_BASE + "PingService12.PingService12Port";
    private static final String ECHO_CONTEXT12 = CONTEXT_BASE + "EchoService12.EchoService12Port";
    private String urlSuffix = "";
    private String message = "HELLO";
    private String servtype = "async";
    private String uriString = "http://" + urlHost + ":" + urlPort;
    private Boolean wireasync = true;
    private Boolean soap12 = false;
    private int count = 1;
    private ClientConfigurationFactory clientConfigurationFactory = null;

    /**
     * main()
     * <p/>
     * see printusage() for command-line arguments
     *
     * @param args
     */
    public static void main(String[] args) {
        SampleClient sample = new SampleClient();
        sample.parseArgs(args);
        sample.CallService();
    }

    /**
     * parseArgs Read and interpret the command-line arguments
     *
     * @param args
     */
    public void parseArgs(String[] args) {
        if (args.length >= 1) {
            for (int i = 0; i < args.length; i++) {
                try {
                    if ('-' == args[i].charAt(0)) {
                        switch (args[i].charAt(1)) {
                            case '?':
                                printUsage(null);
                                System.exit(0);
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
                            case 'm':
                            case 'M':
                                message = args[++i];
                                break;
                            case 's':
                            case 'S':
                                servtype = args[++i];
                                if ((!servtype.equalsIgnoreCase("async")) &&
                                        (!servtype.equalsIgnoreCase("echo")) &&
                                        (!servtype.equalsIgnoreCase("ping"))) {
                                    System.out
                                            .println("ERROR: Attempt to invoke a service that is not supported");
                                    printUsage(null);
                                    System.exit(0);
                                }
                                break;
                            case 't':
                            case 'T':
                                timeout = new Integer(args[++i]).intValue();
                                break;
                            case 'c':
                            case 'C':
                                count = new Integer(args[++i]).intValue();
                                break;
                            case 'w':
                            case 'W':
                                String parm = args[++i];
                                if (parm.equalsIgnoreCase("y")) {
                                    wireasync = true;
                                } else {
                                    wireasync = false;
                                }
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
    }

    /**
     * printUsage Print usage help to output
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
                .println("  runSampleSei -h [hostname] -p [port] -f [urlSuffix] -m [testMessage] -s [echo|ping|async] -t [timeout] -w [y|n] -c [count] -1 -2");
        System.out.println("Default values:");
        System.out.println("  hostname = localhost");
        System.out.println("  port = 8080");
        System.out.println("  testMessage = HELLO");
        System.out.println("  urlSuffix = /jaxws-samples/services/EchoService.EchoServicePort");
        System.out.println("  service = async");
        System.out.println("  timeout = 240 (seconds)");
        System.out.println("  wireasync = y (yes)");
        System.out.println("  count = 1");
        System.out.println("  -1 = soap 1.1 (default)");
        System.out.println("  -2 = soap 1.2");
    }

    /**
     * CallService Parms were already read. Now call the service proxy classes
     */
    void CallService() {
        for (int index = 0; index < count; index++) {
            if (soap12) {
                if (servtype.equalsIgnoreCase("echo")) {
                    if (0 == urlSuffix.length()) {
                        urlSuffix = ECHO_CONTEXT12;
                    }
                    buildEcho12(uriString + urlSuffix, null, message);
                } else if (servtype.equalsIgnoreCase("async")) {
                    if (0 == urlSuffix.length()) {
                        urlSuffix = ECHO_CONTEXT12;
                    }

                    if (clientConfigurationFactory == null) {
                        ClientConfigurationFactory factory = initConfigurationFactory();
                        buildAsync12(uriString + urlSuffix, null, message, timeout, wireasync);
                        destroyConfigurationFactory(factory);
                    } else {
                        buildAsync12(uriString + urlSuffix, null, message, timeout, wireasync);
                    }
                } else {
                    if (0 == urlSuffix.length()) {
                        urlSuffix = PING_CONTEXT12;
                    }
                    buildPing12(uriString + urlSuffix, null, message);
                }
            } else {
                if (servtype.equalsIgnoreCase("echo")) {
                    if (0 == urlSuffix.length()) {
                        urlSuffix = ECHO_CONTEXT;
                    }
                    buildEcho(uriString + urlSuffix, null, message);
                } else if (servtype.equalsIgnoreCase("async")) {
                    if (0 == urlSuffix.length()) {
                        urlSuffix = ECHO_CONTEXT;
                    }
                    if (clientConfigurationFactory == null) {
                        ClientConfigurationFactory factory = initConfigurationFactory();
                        buildAsync(uriString + urlSuffix, null, message, timeout, wireasync);
                        destroyConfigurationFactory(factory);
                    } else {
                        buildAsync(uriString + urlSuffix, null, message, timeout, wireasync);
                    }
                } else {
                    if (0 == urlSuffix.length()) {
                        urlSuffix = PING_CONTEXT;
                    }
                    buildPing(uriString + urlSuffix, null, message);
                }
            }
        }
    }

    private ClientConfigurationFactory initConfigurationFactory() {
        String axis2xml = System.getProperty("org.apache.axis2.jaxws.config.path");
        if (axis2xml == null) {
            throw new RuntimeException("Please set org.apache.axis2.jaxws.config.path system property to a valid axis2.xml file (with addressing module enabled)");
        }
        ClientConfigurationFactory factory = null;
        try {
            FileSystemConfigurator configurator = new FileSystemConfigurator(null, axis2xml);
            factory = new ClientConfigurationFactory(configurator);
            MetadataFactoryRegistry.setFactory(ClientConfigurationFactory.class, factory);
        } catch (AxisFault axisFault) {
            throw new RuntimeException(axisFault);
        }
        return factory;
    }

    private void destroyConfigurationFactory(ClientConfigurationFactory factory) {
        try {
            factory.getClientConfigurationContext().terminate();
        } catch (AxisFault axisFault) {
            throw new RuntimeException(axisFault);
        }
    }

    /**
     * buildPing
     * Call the ping service
     *
     * @param endpointURL The Service endpoint URL
     * @param input       The message string
     * @return Boolean true if the ping works
     */
    public boolean buildPing(String endpointURL, URL wsdlURL, String input) {
        try {
            PingServicePortProxy ping = new PingServicePortProxy(wsdlURL);
            ping._getDescriptor().setEndpoint(endpointURL);
            System.out.println(">> CLIENT: SEI Ping to " + endpointURL);

            // Configure SOAPAction properties
            BindingProvider bp = (BindingProvider) (ping._getDescriptor()
                    .getProxy());
            bp.getRequestContext().put(BindingProvider.ENDPOINT_ADDRESS_PROPERTY,
                    endpointURL);
            bp.getRequestContext().put(BindingProvider.SOAPACTION_USE_PROPERTY,
                    Boolean.TRUE);
            bp.getRequestContext().put(BindingProvider.SOAPACTION_URI_PROPERTY,
                    "pingOperation");

            // Build the input object
            PingStringInput pingParm =
                    new ObjectFactory().createPingStringInput();
            pingParm.setPingInput(input);

            // Call the service
            ping.pingOperation(pingParm);
            System.out.println(">> CLIENT: SEI Ping SUCCESS.");
            return true;
        } catch (Exception e) {
            System.out.println(">> CLIENT: ERROR: SEI Ping EXCEPTION.");
            e.printStackTrace();
            return false;
        }
    }

    /**
     * buildEcho
     * Call the Echo service (Sync)
     *
     * @param endpointURL The Service endpoint URL
     * @param input       The message string
     * @return String from the service
     */
    public String buildEcho(String endpointURL, URL wsdlURL, String input) {
        String response = "ERROR!:";
        try {
            EchoServicePortProxy echo = new EchoServicePortProxy(wsdlURL);
            echo._getDescriptor().setEndpoint(endpointURL);

            // Configure SOAPAction properties
            BindingProvider bp = (BindingProvider) (echo._getDescriptor()
                    .getProxy());
            bp.getRequestContext().put(BindingProvider.ENDPOINT_ADDRESS_PROPERTY,
                    endpointURL);
            bp.getRequestContext().put(BindingProvider.SOAPACTION_USE_PROPERTY,
                    Boolean.TRUE);
            bp.getRequestContext().put(BindingProvider.SOAPACTION_URI_PROPERTY,
                    "echoOperation");

            // Build the input object
            EchoStringInput echoParm =
                    new org.apache.axis2.jaxws.samples.echo.ObjectFactory().createEchoStringInput();
            echoParm.setEchoInput(input);
            System.out.println(">> CLIENT: SEI Echo to " + endpointURL);

            // Call the service
            response = echo.echoOperation(echoParm).getEchoResponse();
            System.out.println(">> CLIENT: SEI Echo invocation complete.");
            System.out.println(">> CLIENT: SEI Echo response is: " + response);
        } catch (Exception e) {
            System.out.println(">> CLIENT: ERROR: SEI Echo EXCEPTION.");
            e.printStackTrace();
            return response + ">>>ECHO SERVICE EXCEPTION<<< ";
        }
        return response;
    }

    /**
     * buildAsync
     * Call the Echo service (Async)
     *
     * @param endpointURL The Service endpoint URL
     * @param input       The message string
     * @param waiting     The Async timeout
     * @param wireasync   true to use Async on the wire
     * @return String from the service
     */
    public String buildAsync(String endpointURL, URL wsdlURL, String input, int waiting, Boolean wireasync) {
        String response = "ERROR!:";
        try {
            EchoServicePortProxy echo = new EchoServicePortProxy(wsdlURL);
            echo._getDescriptor().setEndpoint(endpointURL);

            // Configure SOAPAction properties
            BindingProvider bp = (BindingProvider) (echo._getDescriptor()
                    .getProxy());
            bp.getRequestContext().put(BindingProvider.ENDPOINT_ADDRESS_PROPERTY,
                    endpointURL);
            bp.getRequestContext().put(BindingProvider.SOAPACTION_USE_PROPERTY,
                    Boolean.TRUE);
            bp.getRequestContext().put(BindingProvider.SOAPACTION_URI_PROPERTY,
                    "echoOperation");
            if (wireasync) {
                bp.getRequestContext().put(org.apache.axis2.jaxws.util.Constants.USE_ASYNC_MEP,
                        Boolean.TRUE);
            }

            // Set up the callback handler and create the input object
            EchoServiceCallbackHandler callbackHandler = new EchoServiceCallbackHandler();
            EchoStringInput echoParm =
                    new org.apache.axis2.jaxws.samples.echo.ObjectFactory().createEchoStringInput();
            echoParm.setEchoInput(input);
            System.out.println(">> CLIENT: SEI Async to " + endpointURL);

            // Call the service
            Future<?> resp = echo.echoOperationAsync(echoParm, callbackHandler);
            Thread.sleep(1000);
            while (!resp.isDone()) {
                // Check for timeout
                if (waiting <= 0) {
                    System.out
                            .println(">> CLIENT: ERROR - SEI Async Timeout waiting for reply.");
                    return response + "Async timeout waiting for reply.";
                }
                System.out
                        .println(">> CLIENT: SEI Async invocation still not complete");
                Thread.sleep(1000 * SLEEPER);
                waiting -= SLEEPER;
            }

            // Get the response and print it, then return
            EchoStringResponse esr = callbackHandler.getResponse();
            System.out.println(">> CLIENT: SEI Async invocation complete.");
            if (null != esr) {
                response = esr.getEchoResponse();
                if (null != response) {
                    System.out.println(">> CLIENT: SEI Async response is: " + response);
                }
            }
        } catch (Exception e) {
            System.out.println(">> CLIENT: ERROR: SEI Async EXCEPTION.");
            e.printStackTrace();
            return response + ">>>ASYNC SERVICE EXCEPTION<<<";
        }
        return response;
    }

    /**
     * buildPing12
     * Call the ping service
     *
     * @param endpointURL The Service endpoint URL
     * @param input       The message string
     * @return Boolean true if the ping works
     */
    public boolean buildPing12(String endpointURL, URL wsdlURL, String input) {
        try {
            PingService12PortProxy ping = new PingService12PortProxy(wsdlURL);
            ping._getDescriptor().setEndpoint(endpointURL);
            System.out.println(">> CLIENT: SEI Ping to " + endpointURL);

            // Build the input object
            PingStringInput pingParm =
                    new org.apache.axis2.jaxws.samples.ping.ObjectFactory().createPingStringInput();
            pingParm.setPingInput(input);

            // Call the service
            ping.pingOperation(pingParm);
            System.out.println(">> CLIENT: SEI Ping SUCCESS.");
            return true;
        } catch (Exception e) {
            System.out.println(">> CLIENT: ERROR: SEI Ping EXCEPTION.");
            e.printStackTrace();
            return false;
        }
    }

    /**
     * buildEcho12
     * Call the Echo service (Sync)
     *
     * @param endpointURL The Service endpoint URL
     * @param input       The message string
     * @return String from the service
     */
    public String buildEcho12(String endpointURL, URL wsdlURL, String input) {
        String response = "ERROR!:";
        try {
            EchoService12PortProxy echo = new EchoService12PortProxy(wsdlURL);
            echo._getDescriptor().setEndpoint(endpointURL);

            // Build the input object
            EchoStringInput echoParm =
                    new org.apache.axis2.jaxws.samples.echo.ObjectFactory().createEchoStringInput();
            echoParm.setEchoInput(input);

            System.out.println(">> CLIENT: SEI Echo to " + endpointURL);

            // Call the service
            response = echo.echoOperation(echoParm).getEchoResponse();

            System.out.println(">> CLIENT: SEI Echo invocation complete.");
            System.out.println(">> CLIENT: SEI Echo response is: " + response);
        } catch (Exception e) {
            System.out.println(">> CLIENT: ERROR: SEI Echo EXCEPTION.");
            e.printStackTrace();
            return response + ">>>ECHO SERVICE EXCEPTION<<< ";
        }
        return response;
    }

    /**
     * buildAsync12
     * Call the Echo service (Async)
     *
     * @param endpointURL The Service endpoint URL
     * @param input       The message string
     * @param waiting     The Async timeout
     * @param wireasync   true to use Async on the wire
     * @return String from the service
     */
    public String buildAsync12(String endpointURL, URL wsdlURL, String input, int waiting, Boolean wireasync) {
        String response = "ERROR!:";
        try {
            EchoService12PortProxy echo = new EchoService12PortProxy(wsdlURL);
            echo._getDescriptor().setEndpoint(endpointURL);

            // Configure over-the-wire async if specified
            if (wireasync) {
                BindingProvider bp = (BindingProvider) (echo._getDescriptor()
                        .getProxy());
                bp.getRequestContext().put(org.apache.axis2.jaxws.util.Constants.USE_ASYNC_MEP,
                        Boolean.TRUE);
            }

            // Set up the callback handler and create the input object
            EchoServiceCallbackHandler callbackHandler = new EchoServiceCallbackHandler();
            EchoStringInput echoParm =
                    new org.apache.axis2.jaxws.samples.echo.ObjectFactory().createEchoStringInput();
            echoParm.setEchoInput(input);
            System.out.println(">> CLIENT: SEI Async to " + endpointURL);

            // Call the service
            Future<?> resp = echo.echoOperationAsync(echoParm, callbackHandler);
            Thread.sleep(1000);
            while (!resp.isDone()) {
                // Check for timeout
                if (waiting <= 0) {
                    System.out
                            .println(">> CLIENT: ERROR - SEI Async Timeout waiting for reply.");
                    return response + "Async timeout waiting for reply.";
                }
                System.out
                        .println(">> CLIENT: SEI Async invocation still not complete");
                Thread.sleep(1000 * SLEEPER);
                waiting -= SLEEPER;
            }

            // Get the response and print it, then return
            EchoStringResponse esr = callbackHandler.getResponse();
            System.out.println(">> CLIENT: SEI Async invocation complete.");
            if (null != esr) {
                response = esr.getEchoResponse();
                if (null != response) {
                    System.out.println(">> CLIENT: SEI Async response is: " + response);
                }
            }

        } catch (Exception e) {
            System.out.println(">> CLIENT: ERROR: SEI Async EXCEPTION.");
            e.printStackTrace();
            return response + ">>>ASYNC SERVICE EXCEPTION<<<";
        }
        return response;
    }

    public void setClientConfigurationFactory(ClientConfigurationFactory clientConfigurationFactory) {
        this.clientConfigurationFactory = clientConfigurationFactory;
    }
}
	