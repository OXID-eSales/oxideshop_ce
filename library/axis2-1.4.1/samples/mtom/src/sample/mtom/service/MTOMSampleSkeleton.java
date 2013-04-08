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
package sample.mtom.service;

import java.io.File;
import java.io.FileOutputStream;

import javax.activation.DataHandler;

import org.apache.ws.axis2.mtomsample.AttachmentResponse;
import org.apache.ws.axis2.mtomsample.AttachmentType;
import org.w3.www._2005._05.xmlmime.Base64Binary;

/**
 * MTOMServiceSkeleton java skeleton for the axisService
 */
public class MTOMSampleSkeleton {

	/**
	 * Auto generated method signature
	 * 
	 * @param param0
	 * @throws Exception 
	 * 
	 */
	public org.apache.ws.axis2.mtomsample.AttachmentResponse attachment(
			org.apache.ws.axis2.mtomsample.AttachmentRequest param0)
			throws Exception

	{
		AttachmentType attachmentRequest = param0.getAttachmentRequest();
		Base64Binary binaryData = attachmentRequest.getBinaryData();
		DataHandler dataHandler = binaryData.getBase64Binary();
		File file = new File(
				attachmentRequest.getFileName());
		FileOutputStream fileOutputStream = new FileOutputStream(file);
		dataHandler.writeTo(fileOutputStream);
		fileOutputStream.flush();
		fileOutputStream.close();
		
		AttachmentResponse response = new AttachmentResponse();
		response.setAttachmentResponse("File saved succesfully.");
		return response;
	}

}
