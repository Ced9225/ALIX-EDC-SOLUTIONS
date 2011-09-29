<?xml version="1.0" encoding="UTF-8"?>
<!--
    /**************************************************************************\
    * ALIX EDC SOLUTIONS                                                       *
    * Copyright 2011 Business & Decision Life Sciences                         *
    * http://www.alix-edc.com                                                  *
    *                                                                          *
    * This file is part of ALIX.                                               *
    *                                                                          *
    * ALIX is free software: you can redistribute it and/or modify             *
    * it under the terms of the GNU General Public License as published by     *
    * the Free Software Foundation, either version 3 of the License, or        *
    * (at your option) any later version.                                      *
    *                                                                          *
    * ALIX is distributed in the hope that it will be useful,                  *
    * but WITHOUT ANY WARRANTY; without even the implied warranty of           *
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            *
    * GNU General Public License for more details.                             *
    *                                                                          *
    * You should have received a copy of the GNU General Public License        *
    * along with ALIX.  If not, see <http://www.gnu.org/licenses/>.            *
    \**************************************************************************/
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="xml" encoding="UTF-8" indent="no"/>

  <xsl:template name="AuditTrail">
    <xsl:param name="SubjectKey"/>
    <xsl:param name="StudyEventOID"/>
    <xsl:param name="StudyEventRepeatKey"/>
    <xsl:param name="FormOID"/>
    <xsl:param name="FormRepeatKey"/>
    <xsl:param name="ItemGroupOID"/>
    <xsl:param name="ItemGroupRepeatKey"/>
    <xsl:param name="ItemOID"/>
    <xsl:param name="Title"/>
    <xsl:param name="CurrentApp"/>
  	
  	<!--xsl:variable name="ItemOID" select="translate($ItemOID,'.','-')"/-->
  	
    	<xsl:variable name="DivId" select="concat('auditTrail_div_',$ItemOID,'_',$ItemGroupRepeatKey)"/>
    	
    	<a href="javascript:void(0)">
        <xsl:element name='img'>
          <xsl:attribute name='id'><xsl:value-of select="concat($DivId,'_picture')"/></xsl:attribute>
          <xsl:attribute name="src"><xsl:value-of select="$CurrentApp"/>/templates/default/images/clock-history.png</xsl:attribute>
          <xsl:attribute name="onClick">toggleAuditTrail('<xsl:value-of select="$DivId"/>');</xsl:attribute>
          <xsl:attribute name="altbox">Edit the history of this item</xsl:attribute>
        </xsl:element>
      </a>
      <div id="{$DivId}" class='dialog-auditTrail' title='{$Title}' style="display:none;" keys="{$SubjectKey}_{$StudyEventOID}_{$StudyEventRepeatKey}_{$FormOID}_{$FormRepeatKey}_{$ItemGroupOID}_{$ItemGroupRepeatKey}_{$ItemOID}">
        Chargement ...
      </div>
      
  </xsl:template>

</xsl:stylesheet>
