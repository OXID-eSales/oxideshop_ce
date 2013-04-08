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
import javax.swing.event.HyperlinkListener;
import javax.swing.event.HyperlinkEvent;
import java.awt.*;
import java.awt.event.ActionListener;
import java.awt.event.ActionEvent;

public class UserInterface extends JPanel implements HyperlinkListener {

    private JEditorPane jep;
    private JScrollPane scrollPane;

    private JTextField schField;
    private JTextField formatField;

    private JButton schButton;
    private JButton backButton;

    private JLabel schLabel;
    private JLabel formatLabel;


    private JSONSearchModel model;
    private JSONSearchClient parent;

    private String response;
    public UserInterface(JSONSearchClient parent) {
        this.parent = parent;
        model =  new JSONSearchModel();
        initComponents();

        schButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent e) {
                handleSearch();
            }
        });

        backButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent e) {
                handleBack();
            }
        });

        Container pane = parent.getContentPane();
        pane.setLayout(null);

        pane.add(schLabel);
        pane.add(schField);
        pane.add(formatLabel);
        pane.add(formatField);
        pane.add(backButton);
        pane.add(schButton);
        pane.add(scrollPane);
    }

    public void initComponents() {
        schLabel = new JLabel("Search for");
        schLabel.setBounds(20, 10, 80, 25);
        schField = new JTextField();
        schField.setBounds(90, 10, 250, 25);

        formatLabel = new JLabel("format");
        formatLabel.setBounds(350, 10, 50, 25);
        formatField = new JTextField();
        formatField.setBounds(400, 10, 100, 25);

        backButton = new JButton("Back to Results");
        backButton.setBounds(670, 10, 150, 25);
        backButton.setEnabled(false);

        schButton =  new JButton("Search");
        schButton.setBounds(510, 10, 150, 25);


        jep = new JEditorPane();
        jep.setEditable(false);
        jep.setContentType("text/html");
        jep.addHyperlinkListener(this);

        scrollPane = new JScrollPane(jep);
        scrollPane.setBounds(10, 80, (JSONSearchClient.width - 30), (JSONSearchClient.height - 160));


    }

    private void handleSearch(){
        String query = schField.getText();

        if(!query.equals("")){
            response = model.searchYahoo(query, formatField.getText());
            jep.setText(response);
        }
    }

    private void handleBack(){
        jep.setText(response);
        backButton.setEnabled(false);
    }

    public void hyperlinkUpdate(HyperlinkEvent he) {
        if (he.getEventType() == HyperlinkEvent.EventType.ACTIVATED) {
            try {
                jep.setPage(he.getURL());
                backButton.setEnabled(true);
            }
            catch (Exception e) {
                  JOptionPane.showMessageDialog(parent, "Page could not be loaded",
                                "Page Error", JOptionPane.ERROR_MESSAGE);
            }
        }

    }
}

