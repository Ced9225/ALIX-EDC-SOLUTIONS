<?php
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
    
require_once("class.CommonFunctions.php");

class uisubjectlist extends CommonFunctions
{
 
  function uisubjectlist($configEtude,$ctrlRef)
  {	
    CommonFunctions::__construct($configEtude,$ctrlRef);
  } 
  
  //Affichage de la liste des patients (HTML simple)
  function getInterface()
  {

    $menu = $this->m_ctrl->etudemenu()->getMenu();

    $lstSubjectsEx = $this->getSubjectsListEx();

    $htmlRet = "
                $menu

                <div id='mainFormOnly' class='ui-dialog ui-widget ui-widget-content ui-corner-all'>
                  <div class='ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix'>
                    <span class='ui-dialog-title'>Subjects List</span>
                  </div>
                  
                  <div class='ui-dialog-content ui-widget-content'>
                    $lstSubjectsEx
                  </div>                  
                
                  $lstSubjects
                </div>
                 
                ";
      
      return $htmlRet;    
  }
  
  //Obtention d'un tableau HTML des patients jqGrid
  private function getSubjectsListEx()
  {
    $html = "";
    
    //Columns setup - we remove the Value field
    $cols = array();
    foreach($this->m_tblConfig['SUBJECT_LIST']['COLS'] as $key => $col){
      $cols[] = array('Key' => $key,
                      'Visible' => $col['Visible'],
                      'Title'=> $col['Title'],
                      'ShortTitle'=> $col['ShortTitle'],
                      'Width'=> $col['Width'],
                      'Orientation' => $col['Orientation']);
    }
    
    $html .= "<SCRIPT LANGUAGE='JavaScript' SRC='" . $GLOBALS['egw']->link('/'.$this->getCurrentApp(false).'/js/jquery-1.6.2.min.js') . "'></SCRIPT>
              <SCRIPT LANGUAGE='JavaScript' SRC='" . $GLOBALS['egw']->link('/'.$this->getCurrentApp(false).'/js/jquery-ui-1.8.16.custom.min.js') . "'></SCRIPT>
              <SCRIPT LANGUAGE='JavaScript' SRC='" . $GLOBALS['egw']->link('/'.$this->getCurrentApp(false).'/js/jqGrid/grid.locale-en.js') . "'></SCRIPT>
              <SCRIPT LANGUAGE='JavaScript' SRC='" . $GLOBALS['egw']->link('/'.$this->getCurrentApp(false).'/js/jqGrid/jquery.jqGrid.min.js') . "'></SCRIPT>
              <SCRIPT LANGUAGE='JavaScript' SRC='" . $GLOBALS['egw']->link('/'.$this->getCurrentApp(false).'/js/helpers.js') . "'></SCRIPT>
              <SCRIPT LANGUAGE='JavaScript' SRC='" . $GLOBALS['egw']->link('/'.$this->getCurrentApp(false).'/js/alixcrf.subjects.js') . "'></SCRIPT>
              
              <table id='listSubjects'></table>
              <div id='pagerSubjects'></div>
              <div id='filter' style='margin-left:30%;display:none'></div>
              
              <script>loadAlixCRFSubjectsJS('".$this->getCurrentApp(false)."','".json_encode($cols)."');</script>";
    
    
    $html .= "
      <div id='dialog-modal-check' title='Running consistency checks...'>
    	  <p>Checking data... <span id='dialog-modal-check-subject'></span> <span id='dialog-modal-check-subjects'></span></p>
    	  <div style='text-align: center;'><img src='". $this->getCurrentApp(false) ."/templates/default/images/horizontal_loader.gif' alt='Loading' /></div>
    	  <p>Progress : <span id='dialog-modal-check-progress'>0</span>% &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button id='dialog-modal-check-cancel' class='ui-state-default ui-corner-all'>Cancel</button></p>
      </div>";
    
    $html .= "<script>
                function goSubject(SubjectKey){
                  CurrentApp = '".$this->getCurrentApp(false)."';
                  StudyEventOID = '".$this->m_tblConfig['ENROL_SEOID']."';
                  StudyEventRepeatKey = '".$this->m_tblConfig['ENROL_SERK']."';
                  FormOID = '".$this->m_tblConfig['ENROL_FORMOID']."';
                  FormRepeatKey = '".$this->m_tblConfig['ENROL_FORMRK']."';
                  loadAlixCRFSubject(CurrentApp,SubjectKey,StudyEventOID,StudyEventRepeatKey,FormOID,FormRepeatKey);
                }
                
                $(document).ready(function() {
                  initSubjectsList();
                }); 
                
                function runAllConsistencyChecks(i){
                  var CurrentApp = '". $this->getCurrentApp(false) ."';
                  var Subjects = new Array($jsSubjects);
                  var len = Subjects.length;
                  //for(var i=0; i<len; i++){
                    if(i<len)
                    $('#dialog-modal-check-subjects').html('('+ (i+1) +'/'+ len +')');
                    if(i==(len-1)){
                      runConsistencyChecks(CurrentApp,Subjects[i].SiteId,Subjects[i].SubjectKey);
                    }else{ //with callback for next subject
                      runConsistencyChecks(CurrentApp,Subjects[i].SiteId,Subjects[i].SubjectKey,'runAllConsistencyChecks('+ (i+1) +')');
                    }
                  //}
                }
              </script>";
              
    return $html;
  }

/**
 * @description Retourne le statut du patient (Screened, Randomized, etc)
 * @todo à déplacer dans custom avec un hook 
 * @author tpi
 */  
  private function getSubjectStatus($subj){
    if($subj['colCONT']=="1" && $subj['colDSTERMN']==""){
      return "Completed";
    }elseif($subj['colIEYN']=="2" && $subj['colRDNUM']==""){
      return "Randomization Failure";
    }elseif($subj['colIEELIG']=="2"){
      return "Screening Failure";
    }elseif($subj['colCONT']!="1" && $subj['colDSTERMN']!=""){
      return "Withdrawal";
    }elseif($subj['colIEYN']=="1" && $subj['colRDNUM']!=""){
      return "Randomized";
    }elseif($subj['colIEELIG']=="1"){
      return "Screened";
    }
    return "";
  }

/**
 * @description Retourne le statut du CRF (FILLED / INCONSISTENT / PARTIAL / EMPTY)
 * @author tpi
 */  
  private function getCRFStatus($SubjectKey){
    $status = $this->m_ctrl->bocdiscoo()->getSubjectStatus($SubjectKey);
    return "<div class='imageStatus$status imageOnly image16 pointer' altbox='$status' >";
  }
}  
