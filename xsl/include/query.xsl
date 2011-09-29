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

  <xsl:template name="Query">
    <xsl:param name="CurrentApp"/>
  	<xsl:param name="CurrentItemGroupOID"/>
  	<xsl:param name="CurrentItemGroupRepeatKey"/>
    <xsl:param name="ItemOID"/>
    <xsl:param name="DataType"/>
    <xsl:param name="Title"/>
    <xsl:param name="ProfileId"/>
    
    <xsl:if test="$ProfileId='CRA' or $ProfileId='DM'">
  
      <!--On doit modifier les OID, car à la soumission d'un formulaire les navigateurs remplacent les "." par des "_" -->
    	<xsl:variable name="ItemOID" select="translate($ItemOID,'.','-')"/>
  
  	  <!--Valeurs modifiables-->          
      <xsl:variable name="DivId" select="concat('query_div_',$ItemOID,'_',$CurrentItemGroupRepeatKey)"/>
      <a href="javascript:void(0)">
        <xsl:element name='img'>
          <xsl:attribute name='id'><xsl:value-of select="concat($DivId,'_picture')"/></xsl:attribute>
          <xsl:attribute name="src"><xsl:value-of select="$CurrentApp" />/templates/default/images/query_add.png</xsl:attribute>
          <xsl:attribute name="onClick">toggleQuery('<xsl:value-of select="$DivId"/>');</xsl:attribute>
          <xsl:attribute name="altbox">Add a query on this item</xsl:attribute>
        </xsl:element>
      </a>
      <div id="{$DivId}" class='dialog-query' title='{$Title}' style="display:none;" itemgroupoid='{$CurrentItemGroupOID}' itemgrouprepeatkey='{$CurrentItemGroupRepeatKey}' itemoid='{$ItemOID}' itemtitle='{$Title}'>
        New query :<br /> <br />                 
        <b>Type :</b><br />
        <select><!--option value="SC" selected="selected">Information</option--><option value="HC" selected="selected">Inconsistency</option><option value="CM">Bad or missing data</option></select><br /><br />
        <b>Description :</b> (a short and descriptive title)<br />
        <input type="text" size="63" value="{$Title}" /><br /><br />
        <b>Comment :</b> (optional)<br />
        <xsl:element name="textarea">
          <xsl:attribute name="cols">60</xsl:attribute>
          <xsl:attribute name="rows">3</xsl:attribute>
.</xsl:element> <!--indentation : laisser cette balise fermante avec un seul '.' à gauche-->
      </div>
      
    </xsl:if>
  </xsl:template>

</xsl:stylesheet>
