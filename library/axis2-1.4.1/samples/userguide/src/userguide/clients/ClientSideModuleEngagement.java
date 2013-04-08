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
package userguide.clients;

import org.apache.axis2.client.ServiceClient;
import org.apache.axis2.context.ConfigurationContext;
import org.apache.axis2.context.ConfigurationContextFactory;

import javax.xml.namespace.QName;
import java.io.File;
import java.io.FileNotFoundException;

/**
 * Sample for engaged modules in Client side.
 * Logging module has been used.
 */
public class ClientSideModuleEngagement {
    public static void main(String[] args) throws Exception {
        String home = System.getProperty("user.home");
        // create this folder at your home. This folder could be anything
        //then create the "modules" folder

        File repository = new File(home + File.separator + "client-repository");
        if (!repository.exists()) {
            throw new FileNotFoundException("Repository Doesnot Exist");
        }
        //copy the LoggingModule.mar to "modules" folder.
        //then modify the axis2.xml that is generating there according to
        //phases that being included in the "module.xml"
        ConfigurationContext configContext = ConfigurationContextFactory.
                createConfigurationContextFromFileSystem(repository.getAbsolutePath(),
                        repository.getName() + "/axis2.xml");
        ServiceClient serviceClient = new ServiceClient(configContext, null);
        serviceClient.engageModule("LoggingModule");
    }
}


