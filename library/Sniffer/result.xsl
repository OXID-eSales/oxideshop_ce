<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<!--  <xsl:variable name="merged" select="document('result2.xml')" /> -->

<xsl:template match="phpcs">

  <html>
  <body>
    <h2>Summary: </h2>
    <h3>Errors: <xsl:value-of select="count(file/error)"/> Warnings: <xsl:value-of select="count(file/warning)"/></h3>
    <table border="1">
    <tr bgcolor="#9acd32">
       <th>File</th>
       <th>Errors</th>
       <th>Warnigs</th>
     </tr>
     <xsl:for-each select="file">
   <!--  <xsl:variable name="id"><xsl:value-of select="@name"/></xsl:variable> -->
     <tr>
       <td><xsl:value-of select="@name"/></td>
       <td><xsl:value-of select="@errors"/></td>
       <td><xsl:value-of select="@warnings"/></td>
     </tr>
     </xsl:for-each>
    </table>

    <h2>Details</h2>
    <xsl:for-each select="file">
    <h3><xsl:value-of select="@name"/>(<xsl:value-of select="@errors"/>,<xsl:value-of select="@warnings"/>)</h3>
    <table border="1">
    <tr bgcolor="#9acd32">
       <th></th>
       <th>Line</th>
       <th>Message</th>
     </tr>
     <xsl:for-each select="error">
     <tr>
       <td><input type="checkbox" /></td>
       <td><xsl:value-of select="@line"/></td>
       <td><xsl:value-of select="."/></td>
     </tr>
     </xsl:for-each>
     <xsl:for-each select="warning">
     <tr>
       <td><input type="checkbox" /></td>
       <td><xsl:value-of select="@line"/></td>
       <td><xsl:value-of select="."/></td>
     </tr>
     </xsl:for-each>
    </table>
    </xsl:for-each>
  </body>
  </html>
</xsl:template>

</xsl:stylesheet>
