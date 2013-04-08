
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

    /**
     * BankServiceSkeleton.java
     *
     */
    package example;
    /**
     *  BankServiceSkeleton java skeleton for the axisService
     */
    public class BankServiceSkeleton{


        /**

          * @param param0

         */
        public  example.WithdrawResponse withdraw(example.Withdraw param0)
           throws InsufficientFundFaultMessage,AccountNotExistFaultMessage{
                final String account = param0.getAccount();
        if (account.equals("13")) {
            final AccountNotExistFault fault = new AccountNotExistFault();
            fault.setAccount(account);
            AccountNotExistFaultMessage messageException = new AccountNotExistFaultMessage("Account does not exist!");
            messageException.setFaultMessage(fault);
            throw messageException;
        }

        final int amount = param0.getAmount();
        if (amount > 1000) {
            final InsufficientFundFault fault = new InsufficientFundFault();
            fault.setAccount(account);
            fault.setBalance(1000);
            fault.setRequestedFund(amount);
            InsufficientFundFaultMessage messageException = new InsufficientFundFaultMessage("Insufficient funds");
            messageException.setFaultMessage(fault);
            throw messageException;
        }

        final WithdrawResponse response = new WithdrawResponse();
        response.setBalance(1000 - amount);
        return response;
        }

    }

