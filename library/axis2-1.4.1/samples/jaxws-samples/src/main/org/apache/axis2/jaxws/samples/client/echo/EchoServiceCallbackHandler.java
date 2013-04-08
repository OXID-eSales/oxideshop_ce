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
package org.apache.axis2.jaxws.samples.client.echo;

import org.apache.axis2.jaxws.samples.echo.EchoStringResponse;

import javax.xml.ws.AsyncHandler;
import javax.xml.ws.Response;
import java.util.concurrent.ExecutionException;


public class EchoServiceCallbackHandler implements AsyncHandler<EchoStringResponse> {

    private EchoStringResponse output;

    /*
    *
    * @see javax.xml.ws.AsyncHandler#handleResponse(javax.xml.ws.Response)
    */
    public void handleResponse(Response<EchoStringResponse> response) {
        try {
            output = response.get();
        } catch (ExecutionException e) {
            System.out.println(">> CLIENT: Connection Exception");
        } catch (InterruptedException e) {
            System.out.println(">> CLIENT: Interrupted Exception");
        }
    }

    public EchoStringResponse getResponse() {
        return output;
    }
}
