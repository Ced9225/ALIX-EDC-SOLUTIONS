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
    
//////////////////////////////////////////////////////////////
// Annotations
function initAnnotation(ItemOID,IGRK){

  ItemOIDdashed = ItemOID.replace(".","-");

  $("#annotation_div_"+ItemOIDdashed+"_"+IGRK).dialog({
    	autoOpen: false,
    	height: 270,
    	width: 320,
    	modal: false,
    	buttons: {
    		'Close': function() {
          $(this).dialog('close');
    		}
    	},
    	close: function() {
    	}
    });
    
}


function toggleAnnotation(elementId){
  $("#"+elementId).dialog('open');
}

function setState(ItemOID,ItemGroupOID, CurrentItemGroupRepeatKey, FlagValue)
{
  //on coche le bouton radio correspondant au flag dans l'annotation à l'élément.
  var DivId = 'annotation_div_'+ItemOID+'_'+CurrentItemGroupRepeatKey;
  $("input[name='annotation_flag_"+ItemOID+'_'+CurrentItemGroupRepeatKey+"'][value='"+FlagValue+"']").attr('checked',true);
  
  //on affiche la valeur du flag à côté de l'image d'annotation, puis on disable les champs correspondants
  updateFlag(ItemOID, CurrentItemGroupRepeatKey, FlagValue);
}
function setStateEx(ItemOID,ItemGroupOID, CurrentItemGroupRepeatKey, FlagValue, Loading)
{
  if(Loading)
  {
    //on active ou désactive les éléments selon la demande
    freezeFields(ItemOID,ItemGroupOID, CurrentItemGroupRepeatKey, (FlagValue!='Ø'));
  }
  else
  {
    //on adapte l'état activé/désactivé des éléments et le Flag de leur annotation
    setState(ItemOID,ItemGroupOID, CurrentItemGroupRepeatKey, FlagValue);
  }
}

function updateFlag(ItemOID,ItemGroupOID, CurrentItemGroupRepeatKey, FlagValue, keepDisabled, freezeOnlyEmpty)
//keepDisabled est optionnel, il sert à ne pas altérer l'état d'un élément déjà disabled lors du chargement de la page
//freezeOnlyEmpty est optionnel, il sert à altérer l'état d'un élément uniquement quand sa valeur est vide => utile pour les partialDate
{
  //alert(updateFlag);
  var elementId = 'annotation_div_'+ItemOID+'_'+CurrentItemGroupRepeatKey+'_flagvalue';
  
  //Recopie des valeurs dans les champs du formulaire
  $("*[name='"+ItemGroupOID+"'] :input[name='annotation_flag_"+ItemOID+"_"+CurrentItemGroupRepeatKey+"']").val(FlagValue);
  
  updateElementContent(elementId,FlagValue+'&#160;'); //Affichage du libellé du flag saisi à gauche de l'icône d'annotation
  if(FlagValue=='' || FlagValue=='Ø')
  {
    bFreeze = false;
  }
  else                                              
  {
    bFreeze = true;
  }
  if(FlagValue=='ND' && freezeOnlyEmpty)
  {
    // on défreeze tous les champs avant d'altérer leur état. (partial date)
    freezeFields(ItemOID,ItemGroupOID,ItemGroupOID, false, false, false);
    //alert(freezeOnlyEmpty);
  }
  freezeFields(ItemOID,ItemGroupOID, CurrentItemGroupRepeatKey, bFreeze, keepDisabled, freezeOnlyEmpty); 
}

function updateElementContent(elementId,content){
  if(document.getElementById(elementId))
  {
    document.getElementById(elementId).innerHTML = content;
  }
}

function freezeFields(ItemOID,ItemGroupOID, CurrentItemGroupRepeatKey, bFreeze, keepDisabled, freezeOnlyEmpty)
{  
  filterEmpty="";
  if(freezeOnlyEmpty){ 
    filterEmpty = "[value='']";
  }

  flagValue = $("*[name='"+ItemGroupOID+"'] :input[name='annotation_flag_"+ItemOID.replace(".","-")+"_"+CurrentItemGroupRepeatKey+"']").val();

  if(flagValue!="Ø" && flagValue!="" && typeof(flagValue)!="undefined"){
    keepDisabled = true;
  }
  
  ItemOID = ItemOID.replace(".","\\.");  
  ItemOID = ItemOID.replace("-","\\.");  
  //alert("*[name='"+ItemGroupOID+"'] #"+ItemOID+"_"+CurrentItemGroupRepeatKey+" :input.inputItem"+ filterEmpty);
  $("*[name='"+ItemGroupOID+"'] #"+ItemOID+"_"+CurrentItemGroupRepeatKey+" :input.inputItem"+ filterEmpty).attr('disabled',function () {
                                                                      if(!(keepDisabled * this.disabled)) return bFreeze;
                                                                    });
}

//Mise à jour de l'image de l'annotation
function updateAnnotPict(annotation_comment_name, annotation_picure_id)
{
  var emptyPic = 'alixcrf/templates/default/images/post_note_empty.gif';
  var annotPic = 'alixcrf/templates/default/images/post_note.gif';
  
  element = document.getElementsByName(annotation_comment_name);
  if(element[0].value.length>1)
  {
    document.getElementById(annotation_picure_id).src = annotPic;
  }
  else
  {
    document.getElementById(annotation_picure_id).src = emptyPic;
  }
}
