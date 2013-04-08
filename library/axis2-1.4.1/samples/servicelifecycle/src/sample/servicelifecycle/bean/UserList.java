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
package sample.servicelifecycle.bean;

import org.apache.axis2.AxisFault;

import java.util.HashMap;
import java.util.Iterator;

public class UserList {
    private HashMap userList;
    private HashMap loggedUsers;

    public UserList() {
        this.userList = new HashMap();
        loggedUsers = new HashMap();
    }

    public void addUser(User user) throws AxisFault {
        if (userList.get(user.getUserName().trim()) != null) {
            throw new AxisFault("User has already registered.");
        }
        userList.put(user.getUserName(), user);
    }

    public boolean login(String userName, String passWord) throws AxisFault {
        User user = (User) userList.get(userName.trim());
        if (user == null) {
            throw new AxisFault("user has not registerd");
        }
        if (user.getPassWord().equals(passWord)) {
            loggedUsers.put(userName, user);
            return true;
        } else {
            throw new AxisFault("Invalid user , pass owrd incorrect");
        }
    }

    public boolean isLogged(String userName) {
        User user = (User) loggedUsers.get(userName.trim());
        return user != null;
    }

    public User[] getUsers() {
        User [] users = new User[userList.size()];
        Iterator users_itr = userList.values().iterator();
        int count = 0;
        while (users_itr.hasNext()) {
            users[count] = (User) users_itr.next();
            count ++;
        }
        return users;
    }
}
