#!/bin/sh

# Licensed to the Apache Software Foundation (ASF) under one
# or more contributor license agreements. See the NOTICE file
# distributed with this work for additional information
# regarding copyright ownership. The ASF licenses this file
# to you under the Apache License, Version 2.0 (the
# "License"); you may not use this file except in compliance
# with the License. You may obtain a copy of the License at
# 
# http://www.apache.org/licenses/LICENSE-2.0
# 
# Unless required by applicable law or agreed to in writing,
# software distributed under the License is distributed on an
# "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
# KIND, either express or implied. See the License for the
# specific language governing permissions and limitations
# under the License.

# ----------------------------------------------------------------------------
#    Axis2 Script
#
# Pre-requisites
#   - setenv.sh must have been called.
#
#   AXIS2_HOME   Home of Axis2 installation. If not set I will  try
#                   to figure it out.
#
#   JAVA_HOME       Must point at your Java Development Kit installation.
#
# -----------------------------------------------------------------------------

# Get the context and from that find the location of setenv.sh
dir=`dirname "$0"`
. "${dir}"/setenv.sh

#add any user given classpath's
USER_COMMANDS=""
prearg=""
for arg in "$@"
do
   if [ "$arg" != -classpath ] && [ "$arg" != -cp ] && [ "$prearg" != -classpath ] && [ "$prearg" != -cp  ]
   then
      USER_COMMANDS="$USER_COMMANDS ""$arg"
   fi

   if [ "$prearg"=-classpath ] || [ "$prearg"=-cp  ]
   then
      AXIS2_CLASSPATH="$arg":"$AXIS2_CLASSPATH"
   fi
   prearg="$arg"
done 


"$JAVA_HOME"/bin/java -classpath "$AXIS2_CLASSPATH" \
-Daxis2.xml="$AXIS2_HOME/conf/axis2.xml" -Daxis2.repo="$AXIS2_HOME/repository"  $USER_COMMANDS
