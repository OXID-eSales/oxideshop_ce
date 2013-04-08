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
package sample.soapwithattachments.service;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;

import javax.activation.DataHandler;

import org.apache.axiom.attachments.Attachments;
import org.apache.axis2.context.MessageContext;

public class AttachmentService {

	public String uploadFile(String name, String attchmentID) throws IOException
	{
        MessageContext msgCtx = MessageContext.getCurrentMessageContext();
        Attachments attachment = msgCtx.getAttachmentMap();
        DataHandler dataHandler = attachment.getDataHandler(attchmentID);
        File file = new File(
				name);
		FileOutputStream fileOutputStream = new FileOutputStream(file);
		dataHandler.writeTo(fileOutputStream);
		fileOutputStream.flush();
		fileOutputStream.close();
		
		return "File saved succesfully.";
	}

}
