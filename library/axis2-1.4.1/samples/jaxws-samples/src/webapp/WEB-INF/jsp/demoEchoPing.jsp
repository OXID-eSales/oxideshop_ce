<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<%--
  ~ Licensed to the Apache Software Foundation (ASF) under one
  ~ or more contributor license agreements. See the NOTICE file
  ~ distributed with this work for additional information
  ~ regarding copyright ownership. The ASF licenses this file
  ~ to you under the Apache License, Version 2.0 (the
  ~ "License"); you may not use this file except in compliance
  ~ with the License. You may obtain a copy of the License at
  ~
  ~ http://www.apache.org/licenses/LICENSE-2.0
  ~
  ~ Unless required by applicable law or agreed to in writing,
  ~ software distributed under the License is distributed on an
  ~ "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
  ~ KIND, either express or implied. See the License for the
  ~ specific language governing permissions and limitations
  ~ under the License.
  --%>

<HTML>
<HEAD>
    <%@ page language="java" contentType="text/html; charset=ISO-8859-1"
             pageEncoding="ISO-8859-1" %>
    <META http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
    <TITLE>JAX-WS Web Services Ping and Echo Sample</TITLE>
</HEAD>
<BODY>
<P align="center"><B><FONT size="+2">JAX-WS Web Services Ping and Echo Sample
</FONT></B></P>

<H3><B> <U>Message Options</U></B></H3>

<FORM name="demoMe" method="POST" action="/jaxws-samples/demoEchoPing">
<TABLE border="0" cellpadding="0" cellspacing="1">
<TBODY>
<TR>
    <TD>
        <TABLE border="0" cellpadding="0" cellspacing="1" bgcolor="#99FFBB">
            <TBODY>
                <TR>
                    <TD><B>Message Type:</B></TD>
                    <TD><B><SELECT name="msgservice">
                        <OPTION value="Ping" <%=request.getAttribute("PingSelected")%>>One-Way
                            Ping
                        </OPTION>
                        <OPTION value="Echo" <%=request.getAttribute("EchoSelected")%>>Synchronous
                            Echo
                        </OPTION>
                        <OPTION value="Async" <%=request.getAttribute("AsyncSelected")%>>Asynchronous
                            Echo with Sync Communication
                        </OPTION>
                        <OPTION value="AsyncWire"
                                <%=request.getAttribute("AsyncWireSelected")%>>Asynchronous
                            Echo with Async Communication
                        </OPTION>
                    </SELECT></B></TD>
                    <TD></TD>
                </TR>
                <TR>
                    <TD><BR>
                    </TD>
                    <TD></TD>
                    <TD></TD>
                </TR>
                <TR>
                    <TD><B>Message String:</B></TD>
                    <TD><INPUT type="text" name="msgstring" size="40"></TD>
                    <TD></TD>
                </TR>
                <TR>
                    <TD><BR>
                    </TD>
                    <TD></TD>
                    <TD></TD>
                </TR>
                <TR>
                    <TD><B>Message Count:</B></TD>
                    <TD><B><INPUT type="text" name="msgcount" size="4"
                                  value="<%=request.getAttribute("msgcount")%>"></B></TD>
                </TR>
                <TR>
                    <TD><BR>
                    </TD>
                    <TD></TD>
                    <TD></TD>
                </TR>
                <TR>
                    <TD><B>Service URI:</B></TD>
                    <TD><B><INPUT type="text" name="uri" size="40"
                                  value="<%=request.getAttribute("uridef")%>"></B></TD>
                    <TD></TD>
                </TR>
                <TR>
                    <TD><BR>
                    </TD>
                    <TD><FONT size="-1">"example:
                        http://ServiceHostname:port"</FONT></TD>
                    <TD></TD>
                </TR>
                <TR>
                    <TD><BR>
                    </TD>
                    <TD></TD>
                    <TD></TD>
                </TR>
                <TR>
                    <TD><B>SOAP:</B></TD>
                    <TD><INPUT type="checkbox" name="soap12"
                    <%=request.getAttribute("soapdef")%>>Use SOAP 1.2
                    </TD>
                    <TD></TD>
                </TR>
            </TBODY>
        </TABLE>
    </TD>
</TR>
<TR>
    <TD>
        <TABLE width="50" border="0" cellpadding="0" cellspacing="1">
            <TBODY>
                <TR>
                    <TD><INPUT type="submit" name="SUBMIT" value="Send Message">
                </TR>
                <TR>
                    <TD><TEXTAREA rows="20" cols="60" name="OUTPUT" readonly
                                  style="background:#E6E6FA" style="font-weight: bold"
                                  style="border-style:solid">
                        <%=request.getAttribute("messageS")%>
                        <%=request.getAttribute("messageR")%>
                    </TEXTAREA>
                    </TD>
                    <TD></TD>
                </TR>
            </TBODY>
        </TABLE>
    </TD>
</TR>
</TBODY>
</TABLE>
</FORM>
</BODY>
</HTML>
