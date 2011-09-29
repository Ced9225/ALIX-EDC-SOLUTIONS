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
<xsl:include href="include/alixlib.xsl"/>

<!-- Nandomisation number attached to patient - cf hookFunctions.php-->
<xsl:param name="RANDOID"/>

<xsl:param name="country"/>
<xsl:param name="siteId"/>
<xsl:param name="subjId"/>
<xsl:param name="poso"/>
<xsl:param name="subjWeight"/>
<xsl:param name="subjInit"/>
<xsl:param name="visit"/> 
<xsl:param name="nbUT1"/>
<xsl:param name="nbUT2"/>
<xsl:param name="nbUTtotal"/>

<!--Initial Dispensation for visit-->
<xsl:param name="tblUTdisp1_1"/>
<xsl:param name="tblUTdisp1_2"/>
<xsl:param name="tblUTdisp1_3"/>
<xsl:param name="tblUTdisp1_4"/>
<xsl:param name="tblUTdisp1_5"/>

<!--New dispensation for the visit (broken, loss, ...)-->
<xsl:param name="tblUTdisp2_1"/>
<xsl:param name="tblUTdisp2_2"/>
<xsl:param name="tblUTdisp2_3"/>
<xsl:param name="tblUTdisp2_4"/>
<xsl:param name="tblUTdisp2_5"/>

<!--Catch all non treated tags, print them with no treatment-->
<xsl:template match="*">
   <xsl:copy>
       <xsl:copy-of select="@*"/>
       <xsl:apply-templates/>
   </xsl:copy>
</xsl:template>

<!--Add help Label-->
<xsl:template match="tr[@name='DA.DADISPN']">
   
   <tr style="background-color:#F4F8FF;"><td colspan="2"></td>
	 <td colspan = "4">
	 <table>
	 <tr><td><b>NEW DISPENSATION</b></td></tr>
<tr><td colspan = "3" class="ItemDataLabel underCondition">
In case of lost or broken bottle :
</td></tr>
<tr><td class="ItemDataLabel underCondition">
	<ul>
  <li><b>Fill the number of bottles lost/broken in the dispensed field</b></li>
	<li><b>Click on the "Save" button</b></li>
   </ul>
   </td></tr>
		</table>
		</td>
   </tr>
   
   <xsl:copy>
       <xsl:copy-of select="@*"/>
       <xsl:apply-templates/>
   </xsl:copy>   
</xsl:template>

<!--Add help label -->
<xsl:template match="tr[@name='DA.WEIGHT']">

   <tr style="background-color:#F4F8FF;"><td colspan="2"></td>
	 <td colspan = "4">
	 <table id="info_disp">
	 <tr><td><b>DISPENSATION</b></td></tr>
<tr><td colspan = "3" class="ItemDataLabel underCondition">
To dispense visit treatments: 
</td></tr>
<tr><td class="ItemDataLabel underCondition">
	<ul>
  <li><b>Fill the patient's weight field</b></li>
	<li><b>Click on the "Save" button</b></li>
   </ul>
   </td></tr>
<tr><td colspan = "3" class="ItemDataLabel underCondition">
Complete <b>Posology</b> to download dispensation prescription 
</td></tr>   

		</table>
		</td>
   </tr>
   
   <xsl:copy>
       <xsl:copy-of select="@*"/>
       <xsl:apply-templates/>
   </xsl:copy>   
</xsl:template>

<!--Button to show prescription -->
<xsl:template match="form[@name='DA']">
   <xsl:copy>
       <xsl:copy-of select="@*"/>
       <xsl:apply-templates/>
       <xsl:if test="$subjWeight!='' and $nbUT1!='0' and $poso !=''">
        <a class="ui-state-default ui-corner-all" href="{$currentApp}/custom/1.0.0/inc/getOrdonnance.php?country={$country}&amp;siteid={$siteId}&amp;subjWeight={$subjWeight}&amp;poso={$poso}&amp;subjid={$subjId}&amp;subjinit={$subjInit}&amp;visit={$visit}&amp;nbut={$nbUT1}&amp;ut1={$tblUTdisp1_1}&amp;ut2={$tblUTdisp1_2}&amp;ut3={$tblUTdisp1_3}&amp;ut4={$tblUTdisp1_4}&amp;ut5={$tblUTdisp1_5}" target="new">Download dispensation prescription</a>
       </xsl:if> 
   </xsl:copy>
</xsl:template>

<!--Button to show prescription - new dispensation broken bottles-->
<xsl:template match="form[@name='EXN']">
   <xsl:copy>
       <xsl:copy-of select="@*"/>
       <xsl:apply-templates/>
       <xsl:if test="$subjWeight!='' and $nbUT2!='0' and $poso !=''">
        <a class="ui-state-default ui-corner-all" href="{$currentApp}/custom/1.0.0/inc/getOrdonnance.php?country={$country}&amp;siteid={$siteId}&amp;subjWeight={$subjWeight}&amp;poso={$poso}&amp;subjid={$subjId}&amp;subjinit={$subjInit}&amp;visit={$visit}&amp;nbut={$nbUT2}&amp;ut1={$tblUTdisp2_1}&amp;ut2={$tblUTdisp2_2}&amp;ut3={$tblUTdisp2_3}&amp;ut4={$tblUTdisp2_4}&amp;ut5={$tblUTdisp2_5}" target="new">Download dispensation prescription</a>
       </xsl:if> 
   </xsl:copy>
</xsl:template>

<!--Add Format for calculated field-->
<xsl:template match="td[../@name='DA.DADISP' and @class='ItemDataLabel underCondition']">
  <xsl:copy>
    <xsl:attribute name="style">
      color: blue;
    </xsl:attribute>
		<xsl:copy-of select="@*"/>  
	  <xsl:value-of select="."/>
  </xsl:copy>  	
</xsl:template>

<xsl:template match="input[@id='DA.DADISP']">
   <xsl:copy>
       <xsl:copy-of select="@*"/>
       <xsl:if test="$nbUTtotal!='0'">
        <xsl:attribute name="value"><xsl:value-of select="$nbUTtotal"/></xsl:attribute>
       </xsl:if>
      <xsl:attribute name="readonly">true</xsl:attribute>       
       <xsl:apply-templates/>
   </xsl:copy> 
</xsl:template>

<xsl:template match="input[@id='EXI.RDNUM']">
   <xsl:copy>
       <xsl:copy-of select="@*"/>
       <xsl:attribute name="value"><xsl:value-of select="$RANDOID"/></xsl:attribute>     
       <xsl:apply-templates/>
   </xsl:copy>  
</xsl:template>

<xsl:template match="input[@id='EXI.EXLOT1']">
   <xsl:copy>
       <xsl:copy-of select="@*"/>
       <xsl:attribute name="value"><xsl:value-of select="$tblUTdisp1_1"/></xsl:attribute>
       <xsl:apply-templates/>
   </xsl:copy>  
</xsl:template>

<xsl:template match="input[@id='EXI.EXLOT2']">
   <xsl:copy>
       <xsl:copy-of select="@*"/>
       <xsl:attribute name="value"><xsl:value-of select="$tblUTdisp1_2"/></xsl:attribute>
       <xsl:apply-templates/>
   </xsl:copy>  
</xsl:template>

<xsl:template match="input[@id='EXI.EXLOT3']">
   <xsl:copy>
       <xsl:copy-of select="@*"/>
       <xsl:attribute name="value"><xsl:value-of select="$tblUTdisp1_3"/></xsl:attribute>
       <xsl:apply-templates/>
   </xsl:copy>  
</xsl:template>

<xsl:template match="input[@id='EXI.EXLOT4']">
   <xsl:copy>
       <xsl:copy-of select="@*"/>
       <xsl:attribute name="value"><xsl:value-of select="$tblUTdisp1_4"/></xsl:attribute>
       <xsl:apply-templates/>
   </xsl:copy>  
</xsl:template>

<xsl:template match="input[@id='EXI.EXLOT5']">
   <xsl:copy>
       <xsl:copy-of select="@*"/>
       <xsl:attribute name="value"><xsl:value-of select="$tblUTdisp1_5"/></xsl:attribute>
       <xsl:apply-templates/>
   </xsl:copy>  
</xsl:template>

<xsl:template match="input[@id='EXN.EXLOT1']">
   <xsl:copy>
       <xsl:copy-of select="@*"/>
       <xsl:attribute name="value"><xsl:value-of select="$tblUTdisp2_1"/></xsl:attribute>
       <xsl:apply-templates/>
   </xsl:copy>  
</xsl:template>

<xsl:template match="input[@id='EXN.EXLOT2']">
   <xsl:copy>
       <xsl:copy-of select="@*"/>
       <xsl:attribute name="value"><xsl:value-of select="$tblUTdisp2_2"/></xsl:attribute>
       <xsl:apply-templates/>
   </xsl:copy>  
</xsl:template>

<xsl:template match="input[@id='EXN.EXLOT3']">
   <xsl:copy>
       <xsl:copy-of select="@*"/>
       <xsl:attribute name="value"><xsl:value-of select="$tblUTdisp2_3"/></xsl:attribute>
       <xsl:apply-templates/>
   </xsl:copy>  
</xsl:template>

<xsl:template match="input[@id='EXN.EXLOT4']">
   <xsl:copy>
       <xsl:copy-of select="@*"/>
       <xsl:attribute name="value"><xsl:value-of select="$tblUTdisp2_4"/></xsl:attribute>
       <xsl:apply-templates/>
   </xsl:copy>  
</xsl:template>

<xsl:template match="input[@id='EXN.EXLOT5']">
   <xsl:copy>
       <xsl:copy-of select="@*"/>
       <xsl:attribute name="value"><xsl:value-of select="$tblUTdisp2_5"/></xsl:attribute>
       <xsl:apply-templates/>
   </xsl:copy>  
</xsl:template>

<!--Mask IECAT and 3 number of treatement-->
<xsl:template match="tr[@id='IE.IECAT_0'or @id='EXI.EXLOT3_0' or @id='EXI.EXLOT4_0' or @id='EXI.EXLOT5_0' or @id='EXN.EXLOT3_0' or @id='EXN.EXLOT4_0' or @id='EXN.EXLOT5_0']">
   <xsl:copy>
        <xsl:copy-of select="@*"/>
       <xsl:attribute name="style">display:none;</xsl:attribute>
       <xsl:apply-templates/>
   </xsl:copy>
</xsl:template>     
  
  <!-- Javascript treatment -->
 <xsl:template match="div[@id='Form']">
  <div id="Form">
		<xsl:apply-templates/>
			<script language="JavaScript">
        function updateUI(origin,loading,ItemGroupOID,ItemGroupRepeatKey){
        
            $("input[name='text_string_EXI@RDNUM_0']").attr("readonly",true);
            $("input[name='text_string_EXI@EXLOT1_0']").attr("readonly",true);
            $("input[name='text_string_EXI@EXLOT2_0']").attr("readonly",true);
            $("input[name='text_string_EXN@EXLOT1_0']").attr("readonly",true);        										
        		$("input[name='text_string_EXN@EXLOT2_0']").attr("readonly",true); 								

        input_origin = 'text_string_EXI@EXLOT1_0';
       	input_destination = 'DA.DADISPN';
       	
         if (origin.name==input_origin) {
          action = $("input[name='text_string_EXI@EXLOT1_0']").val();
          if(action!=''){
            $("table[id='info_disp']").hide(); 	 
            freezeFields(input_destination,'EXN','0',false,false,false);
          }
          else
          {
            $("table[id='info_disp']").show();
            freezeFields(input_destination,'EXN','0',true,false,false);
          }
         }
        										        										
        }
      </script>
  </div>
</xsl:template>   
 
</xsl:stylesheet>