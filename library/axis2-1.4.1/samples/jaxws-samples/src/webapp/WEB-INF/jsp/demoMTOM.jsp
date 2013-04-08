<%@ page language="java" contentType="text/html; charset=ISO-8859-1"
         pageEncoding="ISO-8859-1" %>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

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

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
    <title>JAX-WS Web Services MTOM Sample</title>
    <script language="JavaScript" type="text/javascript">
        <!--
        function checkform(form)
        {
            if (form.filedef.value == "") {
                alert("Please enter a Source Filename");
                form.filedef.focus();
                return false;
            }
            return true;
        }
        //-->
    </script>
</head>
<body>
<P align="center"><B><FONT size="+2">JAX-WS Web Services MTOM Sample
</FONT></B></P>

<H3><B> <U>Message Options</U></B></H3>

<FORM name="mtomdemo" method="POST" action="/jaxws-samples/demoMTOM" onsubmit="return checkform(this);" enctype="multipart/form-data">
    <TABLE border="0" cellpadding="0" cellspacing="1">
        <TBODY>
            <TR>
                <TD>
                    <TABLE border="0" cellpadding="0" cellspacing="1" bgcolor="#CCCC22">
                        <TBODY>
                            <TR>
                                <TD><B>Service Type:</B></TD>
                                <TD><B><SELECT name="msgservice">
                                    <OPTION value="Dispatch" <%=request.getAttribute("DispatchSelected")%>>Dispatch
                                    </OPTION>
                                    <OPTION value="Proxy" <%=request.getAttribute("ProxySelected")%>>Proxy</OPTION>
                                </SELECT></B></TD>
                                <TD></TD>
                            </TR>
                            <TR>
                                <TD><B>Source Filename:</B></TD>
                                <TD><B><INPUT type="file" name="filedef" size="32"
                                              value="<%=request.getAttribute("filedef")%>"></B></TD>
                            </TR>
                            <TR>
                                <TD><BR>
                                </TD>
                                <TD></TD>
                                <TD></TD>
                            </TR>
                            <TR>
                                <TD><B>Service URI:</B></TD>
                                <TD><B><INPUT type="text" name="uridef" size="40"
                                              value="<%=request.getAttribute("uridef")%>"></B></TD>
                                <TD></TD>
                            </TR>
                            <TR>
                                <TD><BR>
                                </TD>
                                <TD><FONT size="-1">example:
                                    http://ServiceHostname:port</FONT></TD>
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
                                <TD><INPUT type="checkbox" name="soapdef"
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
                                              style="background:#FAE622" style="font-weight: bold"
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
</body>
</html>