@echo off

REM  Licensed to the Apache Software Foundation (ASF) under one
REM  or more contributor license agreements. See the NOTICE file
REM  distributed with this work for additional information
REM  regarding copyright ownership. The ASF licenses this file
REM  to you under the Apache License, Version 2.0 (the
REM  "License"); you may not use this file except in compliance
REM  with the License. You may obtain a copy of the License at
REM  
REM  http://www.apache.org/licenses/LICENSE-2.0
REM  
REM  Unless required by applicable law or agreed to in writing,
REM  software distributed under the License is distributed on an
REM  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
REM  KIND, either express or implied. See the License for the
REM  specific language governing permissions and limitations
REM  under the License.

rem ---------------------------------------------------------------------------
rem Startup script for the WSDLJava
rem
rem Environment Variable Prequisites
rem
rem   AXIS2_HOME      Must point at your AXIS2 directory 
rem
rem   JAVA_HOME       Must point at your Java Development Kit installation.
rem
rem   JAVA_OPTS       (Optional) Java runtime options 
rem ---------------------------------------------------------------------------
set CURRENT_DIR=%cd%

rem Make sure prerequisite environment variables are set
if not "%JAVA_HOME%" == "" goto gotJavaHome
echo The JAVA_HOME environment variable is not defined
echo This environment variable is needed to run this program
goto end
:gotJavaHome
if not exist "%JAVA_HOME%\bin\java.exe" goto noJavaHome
goto okJavaHome
:noJavaHome
echo The JAVA_HOME environment variable is not defined correctly
echo This environment variable is needed to run this program
echo NB: JAVA_HOME should point to a JDK/JRE
goto end
:okJavaHome

rem check the AXIS2_HOME environment variable
if not "%AXIS2_HOME%" == "" goto gotHome
set AXIS2_HOME=%CURRENT_DIR%
if exist "%AXIS2_HOME%\bin\axis2.bat" goto okHome

rem guess the home. Jump one directory up to check if that is the home
cd ..
set AXIS2_HOME=%cd%
cd %CURRENT_DIR%

:gotHome
if EXIST "%AXIS2_HOME%\lib\axis2*.jar" goto okHome
echo The AXIS2_HOME environment variable is not defined correctly
echo This environment variable is needed to run this program
goto end

:okHome
rem set the classes
setlocal EnableDelayedExpansion
rem append the existing classpath to AXIS2_CLASS_PATH
set AXIS2_CLASS_PATH=%CLASSPATH%;%AXIS2_HOME%
rem loop through the libs and add them to the class path
FOR %%c in ("%AXIS2_HOME%\lib\*.jar") DO set AXIS2_CLASS_PATH=!AXIS2_CLASS_PATH!;%%c

rem ----- Execute The Requested Command ---------------------------------------
echo Using AXIS2_HOME:   %AXIS2_HOME%
echo Using JAVA_HOME:    %JAVA_HOME%
set _RUNJAVA="%JAVA_HOME%\bin\java"

%_RUNJAVA% %JAVA_OPTS% -cp "!AXIS2_CLASS_PATH!" org.apache.ws.java2wsdl.Java2WSDL %*
endlocal
:end
