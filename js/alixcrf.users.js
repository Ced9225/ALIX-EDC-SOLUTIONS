    /**************************************************************************\
    * ALIX EDC SOLUTIONS                                                       *
    * Copyright 2011 Business & Decision Life Sciences                         *
    * http://www.alix-edc.com                                                  *
    * ------------------------------------------------------------------------ *                                                                       *
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
    
/*
@desc point d'entrée - appelée pour initialiser le comportement AJAX
@author wlt
*/
function loadAlixCRFusersJS(CurrentApp)
{                   
  //Bind des buttons
  $("#tblUsers tr").click(function(){
    newUrl = "index.php?menuaction="+CurrentApp+".uietude.usersInterface&action=viewUser&userId="+$(this).attr("id");
    $(location).attr('href',newUrl);  
  }); 
  
}

function loadAlixCRFprofilesJS(CurrentApp){
  //Bind des buttons
	$('#create-profile')
			.button()
			.click(function() {
        $('#dialog-form').dialog('open');
	});

  $("#tblProfiles tr").click(function(){
    //Edition du profil
    $("select[name='siteId']").val($(this).find("td[name='siteId']").text());
    $("select[name='profileId']").val($(this).find("td[name='profileId']").text());
    $("select[name='default']").val($(this).find("td[name='default']").text());
    
    $('#dialog-form').dialog('open');
  }); 

  var profileId = $("#profileId"),
      defaultProfile = $("#default"), 
      siteId = $("#siteId"),
    	tips = $(".validateTips"),
      allFields = $([]).add(profileId).add(siteId).add(defaultProfile);
  
  function updateTips(t){
  	tips
  		.text(t)
  		.addClass('ui-state-highlight');
  	setTimeout(function() {
  		tips.removeClass('ui-state-highlight', 1500);
  	}, 500);
  }
   
  //Formulaire d'ajout d'un profile
  $("#dialog-form").dialog({
  	autoOpen: false,
  	height: 300,
  	width: 350,
  	modal: true,
  	buttons: {
  		'Save': function() {
  			var bValid = true;
  			allFields.removeClass('ui-state-error');
  
  			if(siteId.val()=="" || profileId.val()==""){
          bValid = false;
        }
  			
  			if (bValid) {
            //Soumission du formulaire
            $("form#addProfile").submit();
  			}
  		},
  		Cancel: function() {
  			$(this).dialog('close');
  		}
  	},
  	close: function() {
  		allFields.val('').removeClass('ui-state-error');
  	}
  });
}