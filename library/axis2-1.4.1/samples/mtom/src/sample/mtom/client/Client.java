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

package sample.mtom.client;

import java.io.File;
import java.io.FileNotFoundException;
import java.rmi.RemoteException;
import java.util.List;
import java.util.Map;

import javax.activation.DataHandler;
import javax.activation.FileDataSource;

import org.apache.axis2.Constants;
import org.apache.axis2.util.CommandLineOption;
import org.apache.axis2.util.CommandLineOptionParser;
import org.apache.axis2.util.OptionsValidator;
import org.apache.ws.axis2.mtomsample.MTOMSampleMTOMSampleSOAP11Port_httpStub;


public class Client {

	/**
	 * @param args
	 * @throws Exception
	 */
	public static void main(String[] args) throws Exception {
		CommandLineOptionParser optionsParser = new CommandLineOptionParser(
				args);
		List invalidOptionsList = optionsParser
				.getInvalidOptions(new OptionsValidator() {
					public boolean isInvalid(CommandLineOption option) {
						String optionType = option.getOptionType();
						return !("dest".equalsIgnoreCase(optionType) || "file"
								.equalsIgnoreCase(optionType));
					}
				});

		if ((invalidOptionsList.size() > 0) || (args.length != 4)) {
			// printUsage();
			System.out.println("Invalid Parameters.");
			return;
		}

		Map optionsMap = optionsParser.getAllOptions();

		CommandLineOption fileOption = (CommandLineOption) optionsMap
				.get("file");
		CommandLineOption destinationOption = (CommandLineOption) optionsMap
				.get("dest");
		File file = new File(fileOption.getOptionValue());
		if (file.exists())
			transferFile(file, destinationOption.getOptionValue());
		else
			throw new FileNotFoundException();
	}

	public static void transferFile(File file, String destination)
			throws RemoteException {
		// uncomment the following if you need to capture the messages from
		// TCPMON. Please look at http://ws.apache.org/commons/tcpmon/tcpmontutorial.html
		// to learn how to setup tcpmon
		MTOMSampleMTOMSampleSOAP11Port_httpStub serviceStub = new MTOMSampleMTOMSampleSOAP11Port_httpStub(
				//"http://localhost:8081/axis2/rest/MTOMSample"
		);

		// Enable MTOM in the client side
		serviceStub._getServiceClient().getOptions().setProperty(
				Constants.Configuration.ENABLE_MTOM, Constants.VALUE_TRUE);
		//Increase the time out when sending large attachments
		serviceStub._getServiceClient().getOptions().setTimeOutInMilliSeconds(10000);

		// Uncomment and fill the following if you want to have client side file
		// caching switched ON.
		/*
		 * serviceStub._getServiceClient().getOptions().setProperty(
		 * Constants.Configuration.CACHE_ATTACHMENTS, Constants.VALUE_TRUE);
		 * serviceStub._getServiceClient().getOptions().setProperty(
		 * Constants.Configuration.ATTACHMENT_TEMP_DIR, "your temp dir");
		 * serviceStub._getServiceClient().getOptions().setProperty(
		 * Constants.Configuration.FILE_SIZE_THRESHOLD, "4000");
		 */

		// Populating the code generated beans
		MTOMSampleMTOMSampleSOAP11Port_httpStub.AttachmentRequest attachmentRequest = new MTOMSampleMTOMSampleSOAP11Port_httpStub.AttachmentRequest();
		MTOMSampleMTOMSampleSOAP11Port_httpStub.AttachmentType attachmentType = new MTOMSampleMTOMSampleSOAP11Port_httpStub.AttachmentType();
		MTOMSampleMTOMSampleSOAP11Port_httpStub.Base64Binary base64Binary = new MTOMSampleMTOMSampleSOAP11Port_httpStub.Base64Binary();

		// Creating a javax.activation.FileDataSource from the input file.
		FileDataSource fileDataSource = new FileDataSource(file);

		// Create a dataHandler using the fileDataSource. Any implementation of
		// javax.activation.DataSource interface can fit here.
		DataHandler dataHandler = new DataHandler(fileDataSource);
		base64Binary.setBase64Binary(dataHandler);
        MTOMSampleMTOMSampleSOAP11Port_httpStub.ContentType_type0 param = new MTOMSampleMTOMSampleSOAP11Port_httpStub.ContentType_type0();
        param.setContentType_type0(dataHandler.getContentType());
        base64Binary.setContentType(param);
		attachmentType.setBinaryData(base64Binary);
		attachmentType.setFileName(destination);
		attachmentRequest.setAttachmentRequest(attachmentType);

		MTOMSampleMTOMSampleSOAP11Port_httpStub.AttachmentResponse response = serviceStub.attachment(attachmentRequest);
		System.out.println(response.getAttachmentResponse());
	}

}
