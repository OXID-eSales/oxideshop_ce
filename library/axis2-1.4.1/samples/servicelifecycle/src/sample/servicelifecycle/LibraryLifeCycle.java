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
package sample.servicelifecycle;

import org.apache.axiom.om.OMAbstractFactory;
import org.apache.axiom.om.OMElement;
import org.apache.axiom.om.OMFactory;
import org.apache.axiom.om.impl.builder.StAXOMBuilder;
import org.apache.axiom.om.util.StAXUtils;
import org.apache.axis2.AxisFault;
import org.apache.axis2.context.ConfigurationContext;
import org.apache.axis2.databinding.utils.BeanUtil;
import org.apache.axis2.description.AxisService;
import org.apache.axis2.description.Parameter;
import org.apache.axis2.engine.DefaultObjectSupplier;
import org.apache.axis2.engine.ServiceLifeCycle;
import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import sample.servicelifecycle.bean.Book;
import sample.servicelifecycle.bean.BookList;
import sample.servicelifecycle.bean.User;
import sample.servicelifecycle.bean.UserList;

import javax.xml.namespace.QName;
import javax.xml.stream.XMLStreamReader;
import java.io.*;
import java.util.Iterator;

public class LibraryLifeCycle implements ServiceLifeCycle {
    private static final Log log = LogFactory.getLog(LibraryLifeCycle.class);

    public void startUp(ConfigurationContext configctx,
                        AxisService service) {
        try {
            String tempDir = System.getProperty("java.io.tmpdir");
            File tempFile = new File(tempDir);
            File libFile = new File(tempFile, "library.xml");
            OMElement libraryElement;
            boolean noFile = true;
            if (!libFile.exists()) {
                //Service starting at the first time or user has clean the temp.dir
                Parameter allBooks = service.getParameter(LibraryConstants.ALL_BOOK);
                libraryElement = allBooks.getParameterElement();
            } else {
                InputStream in = new FileInputStream(libFile);
                XMLStreamReader xmlReader = StAXUtils
                        .createXMLStreamReader(in);
                StAXOMBuilder staxOMBuilder = new StAXOMBuilder(xmlReader);
                libraryElement = staxOMBuilder.getDocumentElement();
                noFile = false;
            }
            processOmelemnt(libraryElement, service, noFile);
        } catch (Exception exception) {
            log.info(exception);
        }
    }

    public void shutDown(ConfigurationContext configctx,
                         AxisService service) {
        try {
            BookList availableBookList = (BookList) service.getParameterValue(LibraryConstants.AVAILABLE_BOOK);
            BookList allBookList = (BookList) service.getParameterValue(LibraryConstants.ALL_BOOK);
            BookList lendBookList = (BookList) service.getParameterValue(LibraryConstants.LEND_BOOK);
            UserList userList = (UserList) service.getParameterValue(LibraryConstants.USER_LIST);
            OMFactory fac = OMAbstractFactory.getOMFactory();
            OMElement libElement = fac.createOMElement("library", null);
            Book[] bookList = allBookList.getBookList();
            libElement.addChild(BeanUtil.getOMElement(
                    new QName(LibraryConstants.ALL_BOOK),
                    bookList, new QName("book"), false, null));
            libElement.addChild(BeanUtil.getOMElement(
                    new QName(LibraryConstants.AVAILABLE_BOOK),
                    availableBookList.getBookList(), new QName("book"), false, null));
            libElement.addChild(BeanUtil.getOMElement(
                    new QName(LibraryConstants.LEND_BOOK),
                    lendBookList.getBookList(), new QName("book"), false, null));

            libElement.addChild(BeanUtil.getOMElement(
                    new QName(LibraryConstants.USER_LIST),
                    userList.getUsers(), new QName("user"), false, null));

            String tempDir = System.getProperty("java.io.tmpdir");
            File tempFile = new File(tempDir);
            File libFile = new File(tempFile, "library.xml");
            OutputStream out = new FileOutputStream(libFile);
            libElement.serialize(out);
            out.flush();
            out.close();
        } catch (Exception e) {
            log.info(e);
        }
    }

    private void processOmelemnt(OMElement element, AxisService service, boolean fileFound) throws AxisFault {
        BookList allBookList = new BookList(LibraryConstants.ALL_BOOK);
        OMElement bookEle = element.getFirstChildWithName(new QName(LibraryConstants.ALL_BOOK));
        Iterator book_itr = bookEle.getChildren();
        while (book_itr.hasNext()) {
            Object obj = book_itr.next();
            if (obj instanceof OMElement) {
                OMElement omElement = (OMElement) obj;
                allBookList.addBook((Book) BeanUtil.deserialize(Book.class, omElement, new DefaultObjectSupplier(), "book"));
            }
        }

        BookList availableBookList = new BookList(LibraryConstants.AVAILABLE_BOOK);
        OMElement avaliableBooksEle =
                element.getFirstChildWithName(new QName(LibraryConstants.AVAILABLE_BOOK));
        if (avaliableBooksEle != null) {
            Iterator available_book_itr = avaliableBooksEle.getChildren();
            while (available_book_itr.hasNext()) {
                Object obj = available_book_itr.next();
                if (obj instanceof OMElement) {
                    OMElement omElement = (OMElement) obj;
                    availableBookList.addBook((Book) BeanUtil.deserialize(Book.class, omElement, new DefaultObjectSupplier(), "book"));
                }

            }
        }


        BookList lendBookList = new BookList(LibraryConstants.LEND_BOOK);
        OMElement lendBooksEle =
                element.getFirstChildWithName(new QName(LibraryConstants.LEND_BOOK));
        if (lendBooksEle != null) {
            Iterator lend_book_itr = lendBooksEle.getChildren();
            while (lend_book_itr.hasNext()) {
                Object obj = lend_book_itr.next();
                if (obj instanceof OMElement) {
                    OMElement omElement = (OMElement) obj;
                    lendBookList.addBook((Book) BeanUtil.deserialize(Book.class, omElement, new DefaultObjectSupplier(), "book"));
                }
            }
        }
        UserList users = new UserList();
        OMElement usersEle =
                element.getFirstChildWithName(new QName(LibraryConstants.USER_LIST));
        if (usersEle != null) {
            Iterator usre_itr = usersEle.getChildren();
            while (usre_itr.hasNext()) {
                Object obj = usre_itr.next();
                if (obj instanceof OMElement) {
                    OMElement omElement = (OMElement) obj;
                    users.addUser((User) BeanUtil.deserialize(User.class, omElement,
                            new DefaultObjectSupplier(), "user"));
                }

            }
        }
        if (fileFound) {
            availableBookList = allBookList.copy();
            service.addParameter(new Parameter(LibraryConstants.AVAILABLE_BOOK, availableBookList));
        } else {
            service.addParameter(new Parameter(LibraryConstants.AVAILABLE_BOOK, availableBookList));
        }


        service.addParameter(new Parameter(LibraryConstants.ALL_BOOK, allBookList));
        service.addParameter(new Parameter(LibraryConstants.LEND_BOOK, lendBookList));
        service.addParameter(new Parameter(LibraryConstants.USER_LIST, users));
    }
}
