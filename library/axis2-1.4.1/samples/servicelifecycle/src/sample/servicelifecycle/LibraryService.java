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

import org.apache.axis2.AxisFault;
import org.apache.axis2.context.ServiceContext;
import org.apache.axis2.description.AxisService;
import org.apache.axis2.description.Parameter;
import sample.servicelifecycle.bean.Book;
import sample.servicelifecycle.bean.BookList;
import sample.servicelifecycle.bean.User;
import sample.servicelifecycle.bean.UserList;

public class LibraryService {

    //To store all the available books
    private BookList availableBookList;
    //to keep all the book in the system
    private BookList allBookList;
    //to keep all the lended books
    private BookList lendBookList;
    //to keep the system users
    private UserList userList;


    public Book[] listAvailableBook() {
        return availableBookList.getBookList();
    }

    public Book[] listAllBook() {
        return allBookList.getBookList();
    }

    public Book[] listLendBook() {
        return lendBookList.getBookList();
    }

    public Book lendBook(String isbn, String userName) throws AxisFault {
        if (isLooged(userName)) {
            Book book = availableBookList.getBook(isbn);
            if (book == null) {
                book = lendBookList.getBook(isbn);
                if (book != null) {
                    throw new AxisFault("Someone has borrowed the book");
                }
                throw new AxisFault("Book is not available for lending");
            }
            availableBookList.removeBook(book);
            lendBookList.addBook(book);
            return book;
        } else {
            throw new AxisFault("First log into system");
        }
    }

    public void returnBook(String isbn) {
        Book tempBook = allBookList.getBook(isbn);
        availableBookList.addBook(tempBook);
        lendBookList.removeBook(tempBook);
    }

    private boolean isLooged(String userName) {
        return userList.isLogged(userName);
    }

    public boolean register(String userName,
                            String passWord) throws AxisFault {
        userList.addUser(new User(userName, passWord));
        return true;
    }

    public boolean login(String userName, String passWord) throws AxisFault {
        return userList.login(userName, passWord);
    }

    /**
     * Session related methods
     */
    public void init(ServiceContext serviceContext) {
        AxisService service = serviceContext.getAxisService();
        this.availableBookList = (BookList) service.getParameterValue(LibraryConstants.AVAILABLE_BOOK);
        this.availableBookList.setListName(LibraryConstants.AVAILABLE_BOOK);
        this.allBookList = (BookList) service.getParameterValue(LibraryConstants.ALL_BOOK);
        this.lendBookList = (BookList) service.getParameterValue(LibraryConstants.LEND_BOOK);
        this.userList = (UserList) service.getParameterValue(LibraryConstants.USER_LIST);
    }

    public void destroy(ServiceContext serviceContext) throws AxisFault {
        AxisService service = serviceContext.getAxisService();
        service.addParameter(new Parameter(LibraryConstants.AVAILABLE_BOOK, availableBookList));
        service.addParameter(new Parameter(LibraryConstants.ALL_BOOK, allBookList));
        service.addParameter(new Parameter(LibraryConstants.LEND_BOOK, lendBookList));
        service.addParameter(new Parameter(LibraryConstants.USER_LIST, userList));
    }
}
