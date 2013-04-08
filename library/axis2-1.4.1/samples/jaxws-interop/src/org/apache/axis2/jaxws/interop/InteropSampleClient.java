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

package org.apache.axis2.jaxws.interop;

import org.tempuri.*;
import javax.xml.ws.BindingProvider;

public class InteropSampleClient
{
  public static void main(String[] args)
  {
    /*Create the dynamic proxy*/
    IBaseDataTypesDocLitB proxy = new BaseDataTypesDocLitBService().getBasicHttpBindingIBaseDataTypesDocLitB();

    /*Invoke the service*/
    if (!proxy.retBool(true))
    {
      System.err.println("The service should have returned 'true'");
      return;
    }

    if (proxy.retInt(42) != 42)
    {
      System.err.println("The service should have returned '42'");
      return;
    }

    String testString = "This is a test";
    if (!testString.equals(proxy.retString(testString)))
    {
      System.err.println("The service should have returned '"+testString+"'");
      return;
    }

    System.out.println("The test completed successfully.");
  }
}