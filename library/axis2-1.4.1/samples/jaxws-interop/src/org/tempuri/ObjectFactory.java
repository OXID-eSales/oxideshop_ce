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


package org.tempuri;

import java.math.BigDecimal;
import java.math.BigInteger;
import javax.xml.bind.JAXBElement;
import javax.xml.bind.annotation.XmlElementDecl;
import javax.xml.bind.annotation.XmlRegistry;
import javax.xml.datatype.Duration;
import javax.xml.datatype.XMLGregorianCalendar;
import javax.xml.namespace.QName;
import org.datacontract.schemas._2004._07.system.DateTimeOffset;


/**
 * This object contains factory methods for each 
 * Java content interface and Java element interface 
 * generated in the org.tempuri package. 
 * <p>An ObjectFactory allows you to programatically 
 * construct new instances of the Java representation 
 * for XML content. The Java representation of XML 
 * content can consist of schema derived interfaces 
 * and classes representing the binding of schema 
 * type definitions, element declarations and model 
 * groups.  Factory methods for each of these are 
 * provided in this class.
 * 
 */
@XmlRegistry
public class ObjectFactory {

    private final static QName _InChar_QNAME = new QName("http://tempuri.org/", "inChar");
    private final static QName _RetQNameResult_QNAME = new QName("http://tempuri.org/", "RetQNameResult");
    private final static QName _InDouble_QNAME = new QName("http://tempuri.org/", "inDouble");
    private final static QName _RetSingleResult_QNAME = new QName("http://tempuri.org/", "RetSingleResult");
    private final static QName _RetDoubleResult_QNAME = new QName("http://tempuri.org/", "RetDoubleResult");
    private final static QName _InULong_QNAME = new QName("http://tempuri.org/", "inULong");
    private final static QName _RetByteArrayResult_QNAME = new QName("http://tempuri.org/", "RetByteArrayResult");
    private final static QName _RetDateTimeOffsetResult_QNAME = new QName("http://tempuri.org/", "RetDateTimeOffsetResult");
    private final static QName _RetUriResult_QNAME = new QName("http://tempuri.org/", "RetUriResult");
    private final static QName _RetBoolResult_QNAME = new QName("http://tempuri.org/", "RetBoolResult");
    private final static QName _InUShort_QNAME = new QName("http://tempuri.org/", "inUShort");
    private final static QName _InDateTime_QNAME = new QName("http://tempuri.org/", "inDateTime");
    private final static QName _InObject_QNAME = new QName("http://tempuri.org/", "inObject");
    private final static QName _InSByte_QNAME = new QName("http://tempuri.org/", "inSByte");
    private final static QName _InQName_QNAME = new QName("http://tempuri.org/", "inQName");
    private final static QName _InUInt_QNAME = new QName("http://tempuri.org/", "inUInt");
    private final static QName _InBool_QNAME = new QName("http://tempuri.org/", "inBool");
    private final static QName _InShort_QNAME = new QName("http://tempuri.org/", "inShort");
    private final static QName _RetIntResult_QNAME = new QName("http://tempuri.org/", "RetIntResult");
    private final static QName _RetByteResult_QNAME = new QName("http://tempuri.org/", "RetByteResult");
    private final static QName _RetUIntResult_QNAME = new QName("http://tempuri.org/", "RetUIntResult");
    private final static QName _InString_QNAME = new QName("http://tempuri.org/", "inString");
    private final static QName _RetDateTimeResult_QNAME = new QName("http://tempuri.org/", "RetDateTimeResult");
    private final static QName _InGuid_QNAME = new QName("http://tempuri.org/", "inGuid");
    private final static QName _RetULongResult_QNAME = new QName("http://tempuri.org/", "RetULongResult");
    private final static QName _InInt_QNAME = new QName("http://tempuri.org/", "inInt");
    private final static QName _InByteArray_QNAME = new QName("http://tempuri.org/", "inByteArray");
    private final static QName _InFloat_QNAME = new QName("http://tempuri.org/", "inFloat");
    private final static QName _RetTimeSpanResult_QNAME = new QName("http://tempuri.org/", "RetTimeSpanResult");
    private final static QName _RetGuidResult_QNAME = new QName("http://tempuri.org/", "RetGuidResult");
    private final static QName _InLong_QNAME = new QName("http://tempuri.org/", "inLong");
    private final static QName _InUri_QNAME = new QName("http://tempuri.org/", "inUri");
    private final static QName _RetStringResult_QNAME = new QName("http://tempuri.org/", "RetStringResult");
    private final static QName _RetDecimalResult_QNAME = new QName("http://tempuri.org/", "RetDecimalResult");
    private final static QName _InTimeSpan_QNAME = new QName("http://tempuri.org/", "inTimeSpan");
    private final static QName _RetLongResult_QNAME = new QName("http://tempuri.org/", "RetLongResult");
    private final static QName _RetShortResult_QNAME = new QName("http://tempuri.org/", "RetShortResult");
    private final static QName _InByte_QNAME = new QName("http://tempuri.org/", "inByte");
    private final static QName _InDateTimeOffset_QNAME = new QName("http://tempuri.org/", "inDateTimeOffset");
    private final static QName _RetFloatResult_QNAME = new QName("http://tempuri.org/", "RetFloatResult");
    private final static QName _RetCharResult_QNAME = new QName("http://tempuri.org/", "RetCharResult");
    private final static QName _InSingle_QNAME = new QName("http://tempuri.org/", "inSingle");
    private final static QName _RetSByteResult_QNAME = new QName("http://tempuri.org/", "RetSByteResult");
    private final static QName _RetUShortResult_QNAME = new QName("http://tempuri.org/", "RetUShortResult");
    private final static QName _RetObjectResult_QNAME = new QName("http://tempuri.org/", "RetObjectResult");
    private final static QName _InDecimal_QNAME = new QName("http://tempuri.org/", "inDecimal");

    /**
     * Create a new ObjectFactory that can be used to create new instances of schema derived classes for package: org.tempuri
     * 
     */
    public ObjectFactory() {
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Integer }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inChar")
    public JAXBElement<Integer> createInChar(Integer value) {
        return new JAXBElement<Integer>(_InChar_QNAME, Integer.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link QName }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetQNameResult")
    public JAXBElement<QName> createRetQNameResult(QName value) {
        return new JAXBElement<QName>(_RetQNameResult_QNAME, QName.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Double }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inDouble")
    public JAXBElement<Double> createInDouble(Double value) {
        return new JAXBElement<Double>(_InDouble_QNAME, Double.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Float }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetSingleResult")
    public JAXBElement<Float> createRetSingleResult(Float value) {
        return new JAXBElement<Float>(_RetSingleResult_QNAME, Float.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Double }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetDoubleResult")
    public JAXBElement<Double> createRetDoubleResult(Double value) {
        return new JAXBElement<Double>(_RetDoubleResult_QNAME, Double.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link BigInteger }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inULong")
    public JAXBElement<BigInteger> createInULong(BigInteger value) {
        return new JAXBElement<BigInteger>(_InULong_QNAME, BigInteger.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link byte[]}{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetByteArrayResult")
    public JAXBElement<byte[]> createRetByteArrayResult(byte[] value) {
        return new JAXBElement<byte[]>(_RetByteArrayResult_QNAME, byte[].class, null, ((byte[]) value));
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link DateTimeOffset }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetDateTimeOffsetResult")
    public JAXBElement<DateTimeOffset> createRetDateTimeOffsetResult(DateTimeOffset value) {
        return new JAXBElement<DateTimeOffset>(_RetDateTimeOffsetResult_QNAME, DateTimeOffset.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link String }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetUriResult")
    public JAXBElement<String> createRetUriResult(String value) {
        return new JAXBElement<String>(_RetUriResult_QNAME, String.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Boolean }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetBoolResult")
    public JAXBElement<Boolean> createRetBoolResult(Boolean value) {
        return new JAXBElement<Boolean>(_RetBoolResult_QNAME, Boolean.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Integer }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inUShort")
    public JAXBElement<Integer> createInUShort(Integer value) {
        return new JAXBElement<Integer>(_InUShort_QNAME, Integer.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link XMLGregorianCalendar }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inDateTime")
    public JAXBElement<XMLGregorianCalendar> createInDateTime(XMLGregorianCalendar value) {
        return new JAXBElement<XMLGregorianCalendar>(_InDateTime_QNAME, XMLGregorianCalendar.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Object }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inObject")
    public JAXBElement<Object> createInObject(Object value) {
        return new JAXBElement<Object>(_InObject_QNAME, Object.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Byte }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inSByte")
    public JAXBElement<Byte> createInSByte(Byte value) {
        return new JAXBElement<Byte>(_InSByte_QNAME, Byte.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link QName }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inQName")
    public JAXBElement<QName> createInQName(QName value) {
        return new JAXBElement<QName>(_InQName_QNAME, QName.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Long }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inUInt")
    public JAXBElement<Long> createInUInt(Long value) {
        return new JAXBElement<Long>(_InUInt_QNAME, Long.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Boolean }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inBool")
    public JAXBElement<Boolean> createInBool(Boolean value) {
        return new JAXBElement<Boolean>(_InBool_QNAME, Boolean.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Short }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inShort")
    public JAXBElement<Short> createInShort(Short value) {
        return new JAXBElement<Short>(_InShort_QNAME, Short.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Integer }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetIntResult")
    public JAXBElement<Integer> createRetIntResult(Integer value) {
        return new JAXBElement<Integer>(_RetIntResult_QNAME, Integer.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Short }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetByteResult")
    public JAXBElement<Short> createRetByteResult(Short value) {
        return new JAXBElement<Short>(_RetByteResult_QNAME, Short.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Long }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetUIntResult")
    public JAXBElement<Long> createRetUIntResult(Long value) {
        return new JAXBElement<Long>(_RetUIntResult_QNAME, Long.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link String }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inString")
    public JAXBElement<String> createInString(String value) {
        return new JAXBElement<String>(_InString_QNAME, String.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link XMLGregorianCalendar }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetDateTimeResult")
    public JAXBElement<XMLGregorianCalendar> createRetDateTimeResult(XMLGregorianCalendar value) {
        return new JAXBElement<XMLGregorianCalendar>(_RetDateTimeResult_QNAME, XMLGregorianCalendar.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link String }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inGuid")
    public JAXBElement<String> createInGuid(String value) {
        return new JAXBElement<String>(_InGuid_QNAME, String.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link BigInteger }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetULongResult")
    public JAXBElement<BigInteger> createRetULongResult(BigInteger value) {
        return new JAXBElement<BigInteger>(_RetULongResult_QNAME, BigInteger.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Integer }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inInt")
    public JAXBElement<Integer> createInInt(Integer value) {
        return new JAXBElement<Integer>(_InInt_QNAME, Integer.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link byte[]}{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inByteArray")
    public JAXBElement<byte[]> createInByteArray(byte[] value) {
        return new JAXBElement<byte[]>(_InByteArray_QNAME, byte[].class, null, ((byte[]) value));
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Float }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inFloat")
    public JAXBElement<Float> createInFloat(Float value) {
        return new JAXBElement<Float>(_InFloat_QNAME, Float.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Duration }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetTimeSpanResult")
    public JAXBElement<Duration> createRetTimeSpanResult(Duration value) {
        return new JAXBElement<Duration>(_RetTimeSpanResult_QNAME, Duration.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link String }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetGuidResult")
    public JAXBElement<String> createRetGuidResult(String value) {
        return new JAXBElement<String>(_RetGuidResult_QNAME, String.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Long }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inLong")
    public JAXBElement<Long> createInLong(Long value) {
        return new JAXBElement<Long>(_InLong_QNAME, Long.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link String }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inUri")
    public JAXBElement<String> createInUri(String value) {
        return new JAXBElement<String>(_InUri_QNAME, String.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link String }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetStringResult")
    public JAXBElement<String> createRetStringResult(String value) {
        return new JAXBElement<String>(_RetStringResult_QNAME, String.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link BigDecimal }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetDecimalResult")
    public JAXBElement<BigDecimal> createRetDecimalResult(BigDecimal value) {
        return new JAXBElement<BigDecimal>(_RetDecimalResult_QNAME, BigDecimal.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Duration }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inTimeSpan")
    public JAXBElement<Duration> createInTimeSpan(Duration value) {
        return new JAXBElement<Duration>(_InTimeSpan_QNAME, Duration.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Long }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetLongResult")
    public JAXBElement<Long> createRetLongResult(Long value) {
        return new JAXBElement<Long>(_RetLongResult_QNAME, Long.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Short }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetShortResult")
    public JAXBElement<Short> createRetShortResult(Short value) {
        return new JAXBElement<Short>(_RetShortResult_QNAME, Short.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Short }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inByte")
    public JAXBElement<Short> createInByte(Short value) {
        return new JAXBElement<Short>(_InByte_QNAME, Short.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link DateTimeOffset }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inDateTimeOffset")
    public JAXBElement<DateTimeOffset> createInDateTimeOffset(DateTimeOffset value) {
        return new JAXBElement<DateTimeOffset>(_InDateTimeOffset_QNAME, DateTimeOffset.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Float }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetFloatResult")
    public JAXBElement<Float> createRetFloatResult(Float value) {
        return new JAXBElement<Float>(_RetFloatResult_QNAME, Float.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Integer }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetCharResult")
    public JAXBElement<Integer> createRetCharResult(Integer value) {
        return new JAXBElement<Integer>(_RetCharResult_QNAME, Integer.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Float }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inSingle")
    public JAXBElement<Float> createInSingle(Float value) {
        return new JAXBElement<Float>(_InSingle_QNAME, Float.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Byte }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetSByteResult")
    public JAXBElement<Byte> createRetSByteResult(Byte value) {
        return new JAXBElement<Byte>(_RetSByteResult_QNAME, Byte.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Integer }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetUShortResult")
    public JAXBElement<Integer> createRetUShortResult(Integer value) {
        return new JAXBElement<Integer>(_RetUShortResult_QNAME, Integer.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link Object }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "RetObjectResult")
    public JAXBElement<Object> createRetObjectResult(Object value) {
        return new JAXBElement<Object>(_RetObjectResult_QNAME, Object.class, null, value);
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link BigDecimal }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://tempuri.org/", name = "inDecimal")
    public JAXBElement<BigDecimal> createInDecimal(BigDecimal value) {
        return new JAXBElement<BigDecimal>(_InDecimal_QNAME, BigDecimal.class, null, value);
    }

}
