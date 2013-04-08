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

package sample.yahooservices.JSONSearch;

import javax.swing.*;
import java.awt.*;

public class JSONSearchClient extends JFrame {
    public static int width;
    public static int height;

    public JSONSearchClient(String title) throws HeadlessException {
        super(title);

        this.getContentPane().add(new UserInterface(this));
        this.setVisible(true);
    }

    public static void main(String[] args) {
        Dimension screenSize = Toolkit.getDefaultToolkit().getScreenSize();
        width = screenSize.width;
        height = screenSize.height;
        JSONSearchClient form = new JSONSearchClient("Axis2 Yahoo-JSON Search Client");


        int left = (width) / 2;
        int top = (height) / 2;
        form.setLocation(left, top);
        form.setSize(width, height);
        form.setDefaultCloseOperation(WindowConstants.EXIT_ON_CLOSE);
        form.setVisible(true);
    }
}
