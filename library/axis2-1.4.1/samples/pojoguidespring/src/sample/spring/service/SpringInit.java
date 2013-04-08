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
package sample.spring.service;

import org.apache.axiom.om.OMElement;
import org.apache.axis2.engine.ServiceLifeCycle;
import org.apache.axis2.context.ConfigurationContext;
import org.apache.axis2.context.OperationContext;
import org.apache.axis2.context.ServiceContext;
import org.apache.axis2.description.AxisService;
import org.springframework.context.support.ClassPathXmlApplicationContext;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;

public class SpringInit implements ServiceLifeCycle {
        
    private static Log logger = LogFactory
        .getLog(SpringInit .class);

    // The web service
    public OMElement springInit(OMElement ignore) {

        return null;
    }

    public void init(ServiceContext serviceContext) {
        
    }

    public void setOperationContext(OperationContext arg0) {

    }

    public void destroy(ServiceContext arg0) {

    }

     /**
     * this will be called during the deployement time of the service. irrespective
     * of the service scope this method will be called
     */
    public void startUp(ConfigurationContext ignore, AxisService service) {
        ClassLoader classLoader = service.getClassLoader();
        ClassPathXmlApplicationContext appCtx = new
            ClassPathXmlApplicationContext(new String[] {"applicationContext.xml"}, false);
        appCtx.setClassLoader(classLoader);
        appCtx.refresh();
        if (logger.isDebugEnabled()) {
            logger.debug("\n\nstartUp() set spring classloader via axisService.getClassLoader() ... ");
        }
    }
    /**
     * this will be called during the deployement time of the service. irrespective
     * of the service scope this method will be called
     */
    public void shutDown(ConfigurationContext ignore, AxisService service) {
        
    }
}