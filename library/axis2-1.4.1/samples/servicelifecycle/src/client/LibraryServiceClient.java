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
package client;

import org.apache.axiom.om.OMElement;
import org.apache.axis2.addressing.EndpointReference;
import org.apache.axis2.client.Options;
import org.apache.axis2.databinding.utils.BeanUtil;
import org.apache.axis2.engine.DefaultObjectSupplier;
import org.apache.axis2.rpc.client.RPCServiceClient;
import sample.servicelifecycle.bean.Book;

import javax.xml.namespace.QName;
import java.io.IOException;
import java.util.ArrayList;
import java.util.Iterator;

public class LibraryServiceClient {
    private static final String USER_NAME = "-userName";
    private static final String PASS_WORD = "-passWord";
    private static final String ISBN = "-isbn";

    public static void main(String[] args) throws Exception {
        LibraryServiceClient client = new LibraryServiceClient();
        client.runClient();
    }

    public void runClient() throws Exception {
        System.out.println("=====Welcome to Libraray client======");
        System.out.println("                                     ");
        System.out.println(" To list All books type 1");
        System.out.println(" To list Available books type 2");
        System.out.println(" To list Lend books type 3");
        System.out.println(" To register  4  - -userName -passWord");
        System.out.println(" To login     5 - -userName -passWord");
        System.out.println(" To lend a book 6 - -isbn -userName   ");
        System.out.println(" To return a book 7 - -isbn");
        System.out.println(" To exit type -1 ");
        System.out.println("                                      ");
        System.out.println("                                      ");
        System.out.println("Enter service epr address :          ");
        String epr = getInput();
        RPCServiceClient rpcClient = new RPCServiceClient();
        Options opts = new Options();
        opts.setTo(new EndpointReference(epr));
        rpcClient.setOptions(opts);
        LibraryServiceClient client = new LibraryServiceClient();
        while (true) {
            System.out.println("Type your command here : ");
            String commandsParms = getInput();
            if (commandsParms != null) {
                String[] args = commandsParms.split(" ");
                String firstarg = args[0];
                int command = Integer.parseInt(firstarg);
                switch (command) {
                    case 1 : {
                        client.listAllBook(rpcClient);
                        break;
                    }
                    case 2 : {
                        client.listAvailableBook(rpcClient);
                        break;
                    }
                    case 3 : {
                        client.listLendBook(rpcClient);
                        break;
                    }
                    case 4 : {
                        String usreName = null;
                        String passWord = null;
                        if (args.length < 5) {
                            throw new Exception("No enough number of arguments");
                        }
                        if (USER_NAME.equals(args[1])) {
                            usreName = args[2];
                        } else if (USER_NAME.equals(args[3])) {
                            usreName = args[4];
                        }

                        if (PASS_WORD.equals(args[1])) {
                            passWord = args[2];
                        } else if (PASS_WORD.equals(args[3])) {
                            passWord = args[4];
                        }
                        client.register(usreName, passWord, rpcClient);
                        break;
                    }
                    case 5 : {
                        String usreName = null;
                        String passWord = null;
                        if (args.length < 5) {
                            throw new Exception("No enough number of arguments");
                        }
                        if (USER_NAME.equals(args[1])) {
                            usreName = args[2];
                        } else if (USER_NAME.equals(args[3])) {
                            usreName = args[4];
                        }

                        if (PASS_WORD.equals(args[1])) {
                            passWord = args[2];
                        } else if (PASS_WORD.equals(args[3])) {
                            passWord = args[4];
                        }
                        client.login(usreName, passWord, rpcClient);
                        break;
                    }
                    case 6 : {
                        String isbn = null;
                        String userName = null;
                        if (args.length < 5) {
                            throw new Exception("No enough number of arguments");
                        }
                        if (USER_NAME.equals(args[1])) {
                            userName = args[2];
                        } else if (USER_NAME.equals(args[3])) {
                            userName = args[4];
                        }

                        if (ISBN.equals(args[1])) {
                            isbn = args[2];
                        } else if (ISBN.equals(args[3])) {
                            isbn = args[4];
                        }
                        client.lendBook(isbn, userName, rpcClient);
                        break;
                    }
                    case 7 : {
                        String isbn = null;
                        if (args.length < 3) {
                            throw new Exception("No enough number of arguments");
                        }
                        if (ISBN.equals(args[1])) {
                            isbn = args[2];
                        }
                        client.returnBook(isbn, rpcClient);
                        break;
                    }
                    case -1 : {
                        System.exit(0);
                    }
                }
            }
        }

        //  System.in.read()
    }

    //


    private String getInput() {
        try {
            byte b [] = new byte [256];
            int i = System.in.read(b);
            String msg = "";
            if (i != -1) {
                msg = new String(b).substring(0, i - 1).trim();
            }
            return msg;
        } catch (IOException e) {
            System.err.println(" occurred while reading in command : " + e);
            return null;
        }
    }


    public void returnBook(String isbn, RPCServiceClient rpcClient) throws Exception {
        rpcClient.getOptions().setAction("urn:returnBook");
        ArrayList args = new ArrayList();
        args.add(isbn);
        rpcClient.invokeRobust(new QName("http://servicelifecycle.sample",
                "returnBook"), args.toArray());
    }

    public void lendBook(String isbn, String userName,
                         RPCServiceClient rpcClient) throws Exception {
        rpcClient.getOptions().setAction("urn:lendBook");
        ArrayList args = new ArrayList();
        args.add(isbn);
        args.add(userName);
        Object obj [] = rpcClient.invokeBlocking(new QName("http://servicelifecycle.sample",
                "lendBook"), args.toArray(), new Class[]{Book.class});
        Book book = (Book) obj[0];
        System.out.println("Title : " + book.getTitle());
        System.out.println("Isbn : " + book.getIsbn());
        System.out.println("Author : " + book.getAuthor());

    }

    public boolean register(String userName,
                            String passWord,
                            RPCServiceClient rpcClient) throws Exception {
        rpcClient.getOptions().setAction("urn:register");
        ArrayList args = new ArrayList();
        args.add(userName);
        args.add(passWord);
        Object obj [] = rpcClient.invokeBlocking(new QName("http://servicelifecycle.sample",
                "register"), args.toArray(), new Class[]{Boolean.class});
        return ((Boolean) obj[0]).booleanValue();
    }

    public boolean login(String userName,
                         String passWord,
                         RPCServiceClient rpcClient) throws Exception {
        rpcClient.getOptions().setAction("urn:login");
        ArrayList args = new ArrayList();
        args.add(userName);
        args.add(passWord);
        Object obj [] = rpcClient.invokeBlocking(new QName("http://servicelifecycle.sample",
                "login"), args.toArray(), new Class[]{Boolean.class});
        return ((Boolean) obj[0]).booleanValue();
    }

    public void listAvailableBook(RPCServiceClient rpcClient) throws Exception {
        rpcClient.getOptions().setAction("urn:listAvailableBook");
        OMElement elemnt = rpcClient.invokeBlocking(new QName("http://servicelifecycle.sample",
                "listAvailableBook"), new Object[]{null});
        printBookData(elemnt);
    }

    private void printBookData(OMElement element) throws Exception {
        if (element != null) {
            Iterator values = element.getChildrenWithName(new QName("http://servicelifecycle.sample", "return"));
            while (values.hasNext()) {
                OMElement omElement = (OMElement) values.next();
                Book book = (Book) BeanUtil.deserialize(Book.class, omElement, new DefaultObjectSupplier(), "book");
                System.out.println("Isbn : " + book.getIsbn());
                System.out.println("Author : " + book.getAuthor());
                System.out.println("Title : " + book.getTitle());
                System.out.println("");
            }

        }
    }

    public void listAllBook(RPCServiceClient rpcClient) throws Exception {
        rpcClient.getOptions().setAction("urn:listAllBook");
        OMElement elemnt = rpcClient.invokeBlocking(new QName("http://servicelifecycle.sample",
                "listAllBook"), new Object[]{null});
        printBookData(elemnt);
    }

    public void listLendBook(RPCServiceClient rpcClient) throws Exception {
        rpcClient.getOptions().setAction("urn:listLendBook");
        OMElement elemnt = rpcClient.invokeBlocking(new QName("http://servicelifecycle.sample",
                "listLendBook"), new Object[]{null});
        printBookData(elemnt);
    }

}
