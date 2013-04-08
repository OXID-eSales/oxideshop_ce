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

package org.apache.axis2.jaxws.interop;

import java.math.BigDecimal;
import java.math.BigInteger;
import javax.jws.WebMethod;
import javax.jws.WebParam;
import javax.jws.WebResult;
import javax.jws.WebService;
import javax.jws.soap.SOAPBinding;
import javax.xml.datatype.Duration;
import javax.xml.datatype.XMLGregorianCalendar;
import javax.xml.namespace.QName;
import org.datacontract.schemas._2004._07.system.DateTimeOffset;
import org.tempuri.*;

@WebService(serviceName="BaseDataTypesDocLitBService", portName="BasicHttpBinding_IBaseDataTypesDocLitB", name = "IBaseDataTypesDocLitB", targetNamespace = "http://tempuri.org/", wsdlLocation="META-INF/BaseDataTypesDocLitB.wsdl")
@SOAPBinding(parameterStyle = SOAPBinding.ParameterStyle.BARE)
public class InteropSampleService implements IBaseDataTypesDocLitB
{
    /**
     * 
     * @param inBool
     * @return
     *     returns boolean
     */
    @WebMethod(operationName = "RetBool", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetBool")
    @WebResult(name = "RetBoolResult", targetNamespace = "http://tempuri.org/", partName = "RetBoolResult")
    public boolean retBool(
        @WebParam(name = "inBool", targetNamespace = "http://tempuri.org/", partName = "inBool")
        boolean inBool)
  {
    return inBool;
  }

    /**
     * 
     * @param inByte
     * @return
     *     returns short
     */
    @WebMethod(operationName = "RetByte", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetByte")
    @WebResult(name = "RetByteResult", targetNamespace = "http://tempuri.org/", partName = "RetByteResult")
    public short retByte(
        @WebParam(name = "inByte", targetNamespace = "http://tempuri.org/", partName = "inByte")
        short inByte)
  {
    return inByte;
  }

    /**
     * 
     * @param inSByte
     * @return
     *     returns byte
     */
    @WebMethod(operationName = "RetSByte", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetSByte")
    @WebResult(name = "RetSByteResult", targetNamespace = "http://tempuri.org/", partName = "RetSByteResult")
    public byte retSByte(
        @WebParam(name = "inSByte", targetNamespace = "http://tempuri.org/", partName = "inSByte")
        byte inSByte)
  {
    return inSByte;
  }

    /**
     * 
     * @param inByteArray
     * @return
     *     returns byte[]
     */
    @WebMethod(operationName = "RetByteArray", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetByteArray")
    @WebResult(name = "RetByteArrayResult", targetNamespace = "http://tempuri.org/", partName = "RetByteArrayResult")
    public byte[] retByteArray(
        @WebParam(name = "inByteArray", targetNamespace = "http://tempuri.org/", partName = "inByteArray")
        byte[] inByteArray)
  {
    return inByteArray;
  }

    /**
     * 
     * @param inChar
     * @return
     *     returns int
     */
    @WebMethod(operationName = "RetChar", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetChar")
    @WebResult(name = "RetCharResult", targetNamespace = "http://tempuri.org/", partName = "RetCharResult")
    public int retChar(
        @WebParam(name = "inChar", targetNamespace = "http://tempuri.org/", partName = "inChar")
        int inChar)
  {
    return inChar;
  }

    /**
     * 
     * @param inDecimal
     * @return
     *     returns java.math.BigDecimal
     */
    @WebMethod(operationName = "RetDecimal", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetDecimal")
    @WebResult(name = "RetDecimalResult", targetNamespace = "http://tempuri.org/", partName = "RetDecimalResult")
    public BigDecimal retDecimal(
        @WebParam(name = "inDecimal", targetNamespace = "http://tempuri.org/", partName = "inDecimal")
        BigDecimal inDecimal)
  {
    return inDecimal;
  }

    /**
     * 
     * @param inFloat
     * @return
     *     returns float
     */
    @WebMethod(operationName = "RetFloat", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetFloat")
    @WebResult(name = "RetFloatResult", targetNamespace = "http://tempuri.org/", partName = "RetFloatResult")
    public float retFloat(
        @WebParam(name = "inFloat", targetNamespace = "http://tempuri.org/", partName = "inFloat")
        float inFloat)
  {
    return inFloat;
  }

    /**
     * 
     * @param inDouble
     * @return
     *     returns double
     */
    @WebMethod(operationName = "RetDouble", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetDouble")
    @WebResult(name = "RetDoubleResult", targetNamespace = "http://tempuri.org/", partName = "RetDoubleResult")
    public double retDouble(
        @WebParam(name = "inDouble", targetNamespace = "http://tempuri.org/", partName = "inDouble")
        double inDouble)
  {
    return inDouble;
  }

    /**
     * 
     * @param inSingle
     * @return
     *     returns float
     */
    @WebMethod(operationName = "RetSingle", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetSingle")
    @WebResult(name = "RetSingleResult", targetNamespace = "http://tempuri.org/", partName = "RetSingleResult")
    public float retSingle(
        @WebParam(name = "inSingle", targetNamespace = "http://tempuri.org/", partName = "inSingle")
        float inSingle)
  {
    return inSingle;
  }

    /**
     * 
     * @param inInt
     * @return
     *     returns int
     */
    @WebMethod(operationName = "RetInt", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetInt")
    @WebResult(name = "RetIntResult", targetNamespace = "http://tempuri.org/", partName = "RetIntResult")
    public int retInt(
        @WebParam(name = "inInt", targetNamespace = "http://tempuri.org/", partName = "inInt")
        int inInt)
  {
    return inInt;
  }

    /**
     * 
     * @param inShort
     * @return
     *     returns short
     */
    @WebMethod(operationName = "RetShort", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetShort")
    @WebResult(name = "RetShortResult", targetNamespace = "http://tempuri.org/", partName = "RetShortResult")
    public short retShort(
        @WebParam(name = "inShort", targetNamespace = "http://tempuri.org/", partName = "inShort")
        short inShort)
  {
    return inShort;
  }

    /**
     * 
     * @param inLong
     * @return
     *     returns long
     */
    @WebMethod(operationName = "RetLong", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetLong")
    @WebResult(name = "RetLongResult", targetNamespace = "http://tempuri.org/", partName = "RetLongResult")
    public long retLong(
        @WebParam(name = "inLong", targetNamespace = "http://tempuri.org/", partName = "inLong")
        long inLong)
  {
    return inLong;
  }

    /**
     * 
     * @param inObject
     * @return
     *     returns java.lang.Object
     */
    @WebMethod(operationName = "RetObject", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetObject")
    @WebResult(name = "RetObjectResult", targetNamespace = "http://tempuri.org/", partName = "RetObjectResult")
    public Object retObject(
        @WebParam(name = "inObject", targetNamespace = "http://tempuri.org/", partName = "inObject")
        Object inObject)
  {
    return inObject;
  }

    /**
     * 
     * @param inUInt
     * @return
     *     returns long
     */
    @WebMethod(operationName = "RetUInt", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetUInt")
    @WebResult(name = "RetUIntResult", targetNamespace = "http://tempuri.org/", partName = "RetUIntResult")
    public long retUInt(
        @WebParam(name = "inUInt", targetNamespace = "http://tempuri.org/", partName = "inUInt")
        long inUInt)
  {
    return inUInt;
  }

    /**
     * 
     * @param inUShort
     * @return
     *     returns int
     */
    @WebMethod(operationName = "RetUShort", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetUShort")
    @WebResult(name = "RetUShortResult", targetNamespace = "http://tempuri.org/", partName = "RetUShortResult")
    public int retUShort(
        @WebParam(name = "inUShort", targetNamespace = "http://tempuri.org/", partName = "inUShort")
        int inUShort)
  {
    return inUShort;
  }

    /**
     * 
     * @param inULong
     * @return
     *     returns java.math.BigInteger
     */
    @WebMethod(operationName = "RetULong", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetULong")
    @WebResult(name = "RetULongResult", targetNamespace = "http://tempuri.org/", partName = "RetULongResult")
    public BigInteger retULong(
        @WebParam(name = "inULong", targetNamespace = "http://tempuri.org/", partName = "inULong")
        BigInteger inULong)
  {
    return inULong;
  }

    /**
     * 
     * @param inString
     * @return
     *     returns java.lang.String
     */
    @WebMethod(operationName = "RetString", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetString")
    @WebResult(name = "RetStringResult", targetNamespace = "http://tempuri.org/", partName = "RetStringResult")
    public String retString(
        @WebParam(name = "inString", targetNamespace = "http://tempuri.org/", partName = "inString")
        String inString)
  {
    return inString;
  }

    /**
     * 
     * @param inGuid
     * @return
     *     returns java.lang.String
     */
    @WebMethod(operationName = "RetGuid", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetGuid")
    @WebResult(name = "RetGuidResult", targetNamespace = "http://tempuri.org/", partName = "RetGuidResult")
    public String retGuid(
        @WebParam(name = "inGuid", targetNamespace = "http://tempuri.org/", partName = "inGuid")
        String inGuid)
  {
    return inGuid;
  }

    /**
     * 
     * @param inUri
     * @return
     *     returns java.lang.String
     */
    @WebMethod(operationName = "RetUri", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetUri")
    @WebResult(name = "RetUriResult", targetNamespace = "http://tempuri.org/", partName = "RetUriResult")
    public String retUri(
        @WebParam(name = "inUri", targetNamespace = "http://tempuri.org/", partName = "inUri")
        String inUri)
  {
    return inUri;
  }

    /**
     * 
     * @param inDateTime
     * @return
     *     returns javax.xml.datatype.XMLGregorianCalendar
     */
    @WebMethod(operationName = "RetDateTime", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetDateTime")
    @WebResult(name = "RetDateTimeResult", targetNamespace = "http://tempuri.org/", partName = "RetDateTimeResult")
    public XMLGregorianCalendar retDateTime(
        @WebParam(name = "inDateTime", targetNamespace = "http://tempuri.org/", partName = "inDateTime")
        XMLGregorianCalendar inDateTime)
  {
    return inDateTime;
  }

    /**
     * 
     * @param inDateTimeOffset
     * @return
     *     returns org.datacontract.schemas._2004._07.system.DateTimeOffset
     */
    @WebMethod(operationName = "RetDateTimeOffset", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetDateTimeOffset")
    @WebResult(name = "RetDateTimeOffsetResult", targetNamespace = "http://tempuri.org/", partName = "RetDateTimeOffsetResult")
    public DateTimeOffset retDateTimeOffset(
        @WebParam(name = "inDateTimeOffset", targetNamespace = "http://tempuri.org/", partName = "inDateTimeOffset")
        DateTimeOffset inDateTimeOffset)
  {
    return inDateTimeOffset;
  }

    /**
     * 
     * @param inTimeSpan
     * @return
     *     returns javax.xml.datatype.Duration
     */
    @WebMethod(operationName = "RetTimeSpan", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetTimeSpan")
    @WebResult(name = "RetTimeSpanResult", targetNamespace = "http://tempuri.org/", partName = "RetTimeSpanResult")
    public Duration retTimeSpan(
        @WebParam(name = "inTimeSpan", targetNamespace = "http://tempuri.org/", partName = "inTimeSpan")
        Duration inTimeSpan)
  {
    return inTimeSpan;
  }

    /**
     * 
     * @param inQName
     * @return
     *     returns javax.xml.namespace.QName
     */
    @WebMethod(operationName = "RetQName", action = "http://tempuri.org/IBaseDataTypesDocLitB/RetQName")
    @WebResult(name = "RetQNameResult", targetNamespace = "http://tempuri.org/", partName = "RetQNameResult")
    public QName retQName(
        @WebParam(name = "inQName", targetNamespace = "http://tempuri.org/", partName = "inQName")
        QName inQName)
  {
    return inQName;
  }

}
