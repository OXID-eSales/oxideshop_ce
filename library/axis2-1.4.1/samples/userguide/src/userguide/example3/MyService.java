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
package userguide.example3;

import org.apache.axiom.om.OMElement;

import javax.xml.stream.XMLStreamException;


public class MyService {
    public OMElement echo(OMElement element) throws XMLStreamException {
            //Praparing the OMElement so that it can be attached to another OM Tree.
            //First the OMElement should be completely build in case it is not fully built and still
            //some of the xml is in the stream.
            element.build();
            //Secondly the OMElement should be detached from the current OMTree so that it can be attached
            //some other OM Tree. Once detached the OmTree will remove its connections to this OMElement.
            element.detach();
            return element;
        }

}
