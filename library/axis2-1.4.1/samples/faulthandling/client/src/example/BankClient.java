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

package example;

import java.rmi.RemoteException;

public final class BankClient {

	/**
	 * If account# == "13", then you will get a AccountNotExistFault.
	 * otherwise if you provide an amount > 1000, you will get a InsufficientFundFaultMessageException,
	 * otherwise you will get a response with a balance equal to 1000 - amountWithdrawn. 
	 */
	public static void main(String[] args) {

		if (args.length != 3) {
			System.err.println("Usage: BankClient -Durl=<url> -Daccount=<account> -Damt=<amount>");
			return;
		}
		
		final String url = args[0];
		final String account = args[1];
		final Integer withdrawalAmount = new Integer(args[2]);
		
		System.out.println();
		System.out.println("Withdrawing " + withdrawalAmount + " dollars from account#" + account);
		
        try {
            final BankService bankService = new BankServiceStub(url);
            final Withdraw withdrawRequest = new Withdraw();
            withdrawRequest.setAccount(account);
            withdrawRequest.setAmount(withdrawalAmount.intValue());
            
            final WithdrawResponse withdrawResponse = bankService.withdraw(withdrawRequest);
            System.out.println("Balance = " + withdrawResponse.getBalance());
            
        } catch (AccountNotExistFaultMessage e) {
            final AccountNotExistFault fault = e.getFaultMessage();
            System.out.println("Account#" + fault.getAccount() + " does not exist");
        } catch (InsufficientFundFaultMessage e) {
            final InsufficientFundFault fault = e.getFaultMessage();
            System.out.println("Account#" + fault.getAccount() + " has balance of " + fault.getBalance() + ". It cannot support withdrawal of " + fault.getRequestedFund());
        } catch (RemoteException e) {
            e.printStackTrace();  
        } catch (Exception e) {
            e.printStackTrace(); 
        }


		
	}
}
