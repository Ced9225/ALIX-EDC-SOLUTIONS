<?php
    /**************************************************************************\
    * ALIX EDC SOLUTIONS                                                       *
    * Copyright 2011 Business & Decision Life Sciences                         *
    * http://www.alix-edc.com                                                  *
    * ------------------------------------------------------------------------ *
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
require_once("class.bocdiscoo.inc.php");
require_once("class.instanciation.inc.php");

require_once(EGW_SERVER_ROOT . "/".$GLOBALS['egw_info']['flags']['currentapp']."/config.inc.php");

/*
@desc joue le role de controleur pour notre application. C'est ici que les sont centralisés les instanciations à la volée des classe uiXXXXX et boXXXX
*/
class ajax extends CommonFunctions
{
	var $public_functions = array(
      'addDeviation' => True,
      'addQuery' => True,
      'checkFormData' => True,
      'createFile' => True,
      'deleteFile' => True,
      'deletePostIt' => True,
      'getAuditTrail' => True,
      'getDeviation' => True,
      'getDeviationHistory' => True,
      'getDeviationsFormList' => True,
      'getDeviationsDataList' => True,
      'getDeviationsList' => True,
      'getFormDataList' => True,
      'getFileContent' => True,
      'getPostItFormList' => True,
      'getPostItList' => True,
      'getQueryHistory' => True,
      'getQueriesDataList' => True,
      'getQueriesList' => True,
      'getQuery' => True,
      'getSelectableFolderTree' => True,
      'getSubjectsDataList' => True,
      'getSubjectsList' => True,
      'removeItemGroupData' => True,
      'removeFormData' => True,
      'renameFile' => True,
      'saveItemGroupData' => True,
      'savePostIt' => True,
      'setFileContent' => True,
      'storeEditorPreferences' => True,
      'updateDeviation' => True,
      'updateQuery' => True,
	);
		
  public function __construct()
  {
    global $configEtude;

    //Controleur d'instanciation
    $this->m_ctrl = new instanciation();

    CommonFunctions::__construct($configEtude,$this->m_ctrl);

  }		

/*
*@desc place TransactionType='Remove' au niveau de l'ItemGroupData demandé
*@author : wlt
*/
public function removeItemGroupData(){
  $this->addlog(__METHOD__ ." : _POST=".$this->dumpRet($_POST),TRACE);  

  //Extraction des paramètres
  $MetaDataVersion = $_POST['MetaDataVersionOID'];
  $SubjectKey = $_POST['SubjectKey'];
  $StudyEventOID = $_POST['StudyEventOID'];
  $StudyEventRepeatKey = $_POST['StudyEventRepeatKey'];
  $FormOID = $_POST['FormOID'];
  $FormRepeatKey = $_POST['FormRepeatKey'];
  $ItemGroupOID = $_POST['ItemGroupOID'];
  $ItemGroupRepeatKey = $_POST['ItemGroupRepeatKey'];

  //Enregistrement des données 
  $this->m_ctrl->bocdiscoo()->removeItemGroupData($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey,$ItemGroupOID,$ItemGroupRepeatKey);  
}

/*
*@desc place TransactionType='Remove' au niveau de l'ItemGroupData demandé
*@author : wlt
*/
public function removeFormData(){
  $this->addlog(__METHOD__ ." : _POST=".$this->dumpRet($_POST),TRACE);  

  //Extraction des paramètres
  $MetaDataVersion = $_POST['MetaDataVersionOID'];
  $SubjectKey = $_POST['SubjectKey'];
  $StudyEventOID = $_POST['StudyEventOID'];
  $StudyEventRepeatKey = $_POST['StudyEventRepeatKey'];
  $FormOID = $_POST['FormOID'];
  $FormRepeatKey = $_POST['FormRepeatKey'];

  //Enregistrement des données 
  $this->m_ctrl->bocdiscoo()->removeFormData($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey);  
}


/*
@desc applique les checkMandatory et les checkConsistency sur le formulaire demandé
      le resultat est enregistré en base, un appel à getQueriesList permet de les retourner
@author wlt      
*/  
public function checkFormData(){
    $this->addLog(__METHOD__ ." : _POST=".$this->dumpRet($_POST),TRACE); 
    
    //Extraction des paramètres
    $SubjectKey = $_POST['SubjectKey'];
    $StudyEventOID = $_POST['StudyEventOID'];
    $StudyEventRepeatKey = $_POST['StudyEventRepeatKey'];
    $FormOID = $_POST['FormOID'];
    $FormRepeatKey = $_POST['FormRepeatKey'];
    
    $this->m_ctrl->bocdiscoo()->updateFormStatus($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey);
    
    return true;
}


 /*
 *@desc méthode ajax, reçoit en paramètre les données d'un formulaire ItemGroup, et retourne les erreurs missing et consistency
 *@return array("errors"=>array(),"newSubjectId"=>string) $tblRet 
 *@author wlt
 */
  public function saveItemGroupData(){
    $this->addlog(__METHOD__ ." : _POST=".$this->dumpRet($_POST),TRACE);  
  
    //Tableau de retour
    $tblRet = array();
  
    //Extraction des paramètres
    $MetaDataVersion = $_POST['MetaDataVersionOID'];
    $SubjectKey = $_POST['SubjectKey'];
    $StudyEventOID = $_POST['StudyEventOID'];
    $StudyEventRepeatKey = $_POST['StudyEventRepeatKey'];
    $FormOID = $_POST['FormOID'];
    $FormRepeatKey = $_POST['FormRepeatKey'];
    $ItemGroupOID = $_POST['ItemGroupOID'];
    $ItemGroupRepeatKey = $_POST['ItemGroupRepeatKey'];
         
    //Pour l'Audit Trail
    $who = $GLOBALS['egw_info']['user']['userid'];
    $where = $GLOBALS['egw']->accounts->id2name($GLOBALS['egw_info']['user']['account_primary_group']);
    $why = "";
  
    //We need to have all items filled
    if($MetaDataVersion=="" || $SubjectKey=="" || $StudyEventOID=="" || $StudyEventRepeatKey=="" ||
       $FormOID=="" || $FormRepeatKey=="" || $ItemGroupOID=="" || $ItemGroupRepeatKey==""){
      $this->addlog(__METHOD__ ." : missing variables : $MetaDataVersion || $SubjectKey || $StudyEventOID || $StudyEventRepeatKey ||
       $FormOID || $FormRepeatKey || $ItemGroupOID || $ItemGroupRepeatKey",FATAL);       
    } 
  
    //Do we need to create a new patient ? if yes, we need to browser the entire patient collection
    if($SubjectKey=="BLANK"){
      $this->m_ctrl->socdiscoo();
    }else{
      $this->m_ctrl->socdiscoo($SubjectKey);   
    }
    
    //Première vérification : est-ce que les données de notre formulaire sont saines
    $tblRet["errors"] = $this->m_ctrl->bocdiscoo()->checkItemGroupDataSanity($SubjectKey,$MetaDataVersion,$ItemGroupOID,$ItemGroupRepeatKey,$_POST);
    
    //Si il y a des erreurs, l'enregistrement est impossible, on ne va pas plus loin, et l'on retourne les erreurs au browser
    if(count($tblRet["errors"])==0){
      //Faut-il créer un nouveau patient ?
      if($SubjectKey=="BLANK"){
          $SubjectKey = $this->m_ctrl->bocdiscoo()->enrolNewSubject();
          $tblRet["newSubjectId"] = $SubjectKey; 
      }
      
      //Enregistrement des données 
      $this->m_ctrl->bocdiscoo()->saveItemGroupData($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey,$ItemGroupOID,$ItemGroupRepeatKey,$_POST,$who,$where,$why,$fillst="");

      //HOOK => ajax_saveItemGroupData_afterSave
      $this->callHook(__FUNCTION__,"afterSave",array($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey,$ItemGroupOID,$ItemGroupRepeatKey,$this));      
        
    }
    
    echo json_encode($tblRet);   
  }

 /*
 *@desc méthode ajax, retourne une deviation d'après son identifiant
 *@return array
 *@author tpi
 */
  public function getDeviation(){
    $this->addlog(__METHOD__ ." : _POST=".$this->dumpRet($_POST),TRACE);  
  
    //Extraction des paramètres
    $DeviationId = $_POST['DeviationId'];
    
    $res = $this->m_ctrl->bodeviations()->getDeviation($DeviationId);
    
    echo json_encode($res);
  }
 
 
 /*
 *@desc méthode ajax, reçoit en paramètre l'identifiant d'un patient et retourne la liste des formulaires contenant une ou des deviations
 *@return array
 *@author tpi
 */
  public function getDeviationsFormList(){
    $this->addlog(__METHOD__ ." : _POST=".$this->dumpRet($_POST),TRACE);  
  
    //Extraction des paramètres
    $SubjectKey = $_POST['SubjectKey'];
      
    //Enregistrement des données 
    $res = $this->m_ctrl->bodeviations()->getDeviationsFormList($SubjectKey);
    
    echo json_encode($res);   
  }


 /*
 *@desc méthode ajax, retourne la listes des deviations filtrées sur les paramètres passé
 *@return array
 *@author tpi
 */
  public function getDeviationsList(){
    $this->addlog(__METHOD__ ." : _POST=".$this->dumpRet($_POST),TRACE);  
  
    //Extraction des paramètres
    $SubjectKey = "";
    $StudyEventOID = "";
    $StudyEventRepeatKey = "";
    $FormOID = "";
    $FormRepeatKey = "";
    $ItemGroupOID = "";
    $ItemGroupKey = "";
    $ItemOID = "";
    $status = "";
    $isLast = "";
    if(isset($_POST['SubjectKey'])) $SubjectKey = $_POST['SubjectKey'];
    if(isset($_POST['StudyEventOID'])) $StudyEventOID = $_POST['StudyEventOID'];
    if(isset($_POST['StudyEventRepeatKey'])) $StudyEventRepeatKey = $_POST['StudyEventRepeatKey'];
    if(isset($_POST['FormOID'])) $FormOID = $_POST['FormOID'];
    if(isset($_POST['FormRepeatKey'])) $FormRepeatKey = $_POST['FormRepeatKey'];
    if(isset($_POST['ItemGroupOID'])) $ItemGroupOID = $_POST['ItemGroupOID'];
    if(isset($_POST['ItemGroupKey'])) $ItemGroupKey = $_POST['ItemGroupKey'];
    if(isset($_POST['ItemOID'])) $ItemOID = $_POST['ItemOID'];
    if(isset($_POST['status'])) $status = $_POST['status'];
    if(isset($_POST['isLast'])) $isLast = $_POST['isLast'];
    
    $queries = $this->m_ctrl->bodeviations()->getDeviationsList($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey,$ItemGroupOID,$ItemGroupKey,$ItemOID,$status,$isLast);
    
    echo json_encode($queries);
  }

 /*
 *@desc méthode ajax, retourne la listes des deviations filtrées sur les paramètres passés, pour l'interface de gestion globale
 *@return array
 *@author tpi
 */
  public function getDeviationsDataList(){
    $this->addlog(__METHOD__ ." : _REQUEST=".$this->dumpRet($_REQUEST),TRACE);  
  
    //Extraction des paramètres       
    $MetaDataVersion = "1.0.0";
    $SubjectKey = "";
    $StudyEventOID = "";
    $StudyEventRepeatKey = "";
    $FormOID = "";
    $FormRepeatKey = "";
    $ItemGroupOID = "";
    $ItemGroupKey = "";
    $ItemOID = "";
    $deviationStatus = "";
    $deviationType = "";
    $isLast = "Y";
    $search = "";
    if(isset($_POST['MetaDataVersionOID'])) $MetaDataVersion = $_POST['MetaDataVersionOID'];
    if(isset($_POST['SubjectKey'])) $SubjectKey = $_POST['SubjectKey'];
    if(isset($_POST['StudyEventOID'])) $StudyEventOID = $_POST['StudyEventOID'];
    if(isset($_POST['StudyEventRepeatKey'])) $StudyEventRepeatKey = $_POST['StudyEventRepeatKey'];
    if(isset($_POST['FormOID'])) $FormOID = $_POST['FormOID'];
    if(isset($_POST['FormRepeatKey'])) $FormRepeatKey = $_POST['FormRepeatKey'];
    if(isset($_POST['ItemGroupOID'])) $ItemGroupOID = $_POST['ItemGroupOID'];
    if(isset($_POST['ItemGroupKey'])) $ItemGroupKey = $_POST['ItemGroupKey'];
    if(isset($_POST['ItemOID'])) $ItemOID = $_POST['ItemOID'];
    if(isset($_REQUEST['deviationStatus'])) $deviationStatus = $_REQUEST['deviationStatus'];
    if(isset($_REQUEST['deviationType'])) $deviationType = $_REQUEST['deviationType'];
    if(isset($_POST['isLast'])) $isLast = $_POST['isLast'];
    if(isset($_REQUEST['search'])) $search = $_REQUEST['search']; //global search : texte libre
    if(isset($_REQUEST['mode'])) $mode = $_REQUEST['mode']; //jqGrid ou export CSV
    
    $page = $_POST['page']; // get the requested page
    $limit = $_POST['rows']; // get how many rows we want to have into the grid
    $sidx = $_POST['sidx']; // get index row - i.e. user click to sort
    $sord = $_POST['sord']; // get the direction
    if(!$sidx) $sidx =1;
    
    //nous allons avoir besoin des libellés des visites, formulaires et itemgroups présents dans les metadatas
    $description = $this->m_ctrl->bocdiscoo()->getDescriptions($MetaDataVersion);
    
    //application du filtre de recherche
    $where = "";
    
    //filtre global
    if($search!=""){
      $fields = array("UPDATEDT","SITEID","SUBJKEY","ITEMTITLE","DESCRIPTION");
      $search = addslashes($search);
      foreach($fields as $field){
        if($where!="") $where .= " OR ";
        $where .= $field." LIKE '%". $search ."%'";
      }
      
      //recherche plus délicate dans les visites, formulaires, sections : il faut d'abord faire la recherche de clés dans le tableau de descriptions pour ensuite inclure la recherche dans le la requête sql
      $desKeys = array("StudyEventDef" => "SEOID", "FormDef" => "FRMOID", "ItemGroupDef" => "IGOID",);
      foreach($desKeys as $descKey => $descValue){
        $descSearches = array();
        foreach($description[$MetaDataVersion][$descKey] as $OID => $desc){
          if(eregi($search,$desc)){
            $descSearches[$OID] = $OID;
          }
        }
        foreach($descSearches as $OID){
          if($where!="") $where .= " OR ";
          $where .= $descValue." = '". $OID ."'";
        }
      }
      if($search!=""){
        $search = "(". $search .")";
      }
    }
    
    //filtre sur le type
    if($deviationType!=""){
      $types = explode(",",$deviationType);
      $in = "";
      foreach($types as $type){
        if($in!="") $in .= ",";
        $in .= "'$type'";
      }
      if($in!=""){
        if($where!="") $where .= " AND ";
        $where .= "DEVIATIONTYPE IN($in)";
      }
    }
    
    //filtre sur le type
    if(isset($_REQUEST['datePos']) && $_REQUEST['datePos']!="any" && isset($_REQUEST['dateRef']) && $_REQUEST['dateRef']!=""){
      if($_REQUEST['datePos']=="after"){
        $operator = ">";
        $time = "24:00:00";
      }else{
        $operator = "<";
        $time = "00:00:00";
      }
        if($where!="") $where .= " AND ";
        $where .= "UPDATEDT $operator '". $_REQUEST['dateRef'] ." $time'";
    }
    
    //filtres avancés
    if(isset($_REQUEST['_search']) && $_REQUEST['_search']=="true"){
    
      //Site
      if(isset($_REQUEST['SITEID'])){
        if($where!="") $where .= " AND ";
        $where .= "SITEID LIKE'%". $_REQUEST['SITEID'] ."%'";
      }
    
      //Subject
      if(isset($_REQUEST['SUBJKEY'])){
        if($where!="") $where .= " AND ";
        $where .= "SUBJKEY LIKE'%". $_REQUEST['SUBJKEY'] ."%'";
      }
      
      //Visits, Forms, Itemgroups
      $desKeys = array("StudyEventDef" => "SEOID", "FormDef" => "FRMOID", "ItemGroupDef" => "IGOID",);
      foreach($desKeys as $descKey => $descValue){
        if(isset($_REQUEST[$descValue])){
            $descSearches = array(0=>""); //au moins une valeur vide pour valeur vide ou non trouvable
            foreach($description[$MetaDataVersion][$descKey] as $OID => $desc){
              if(eregi($_REQUEST[$descValue],$desc)){
                $descSearches[$OID] = $OID;
              }
            }
            $wherex = "";
            foreach($descSearches as $OID){
              if($wherex!="") $wherex .= " OR ";
              $wherex .= $descValue." = '". $OID ."'";
            }
            if($where!="") $where .= " AND ";
            $where .= "($wherex)";
        }
      }
    
      //Item
      if(isset($_REQUEST['ITEMTITLE'])){
        if($where!="") $where .= " AND ";
        $where .= "ITEMTITLE LIKE'%". $_REQUEST['ITEMTITLE'] ."%'";
      }
    
      //Description
      if(isset($_REQUEST['DESCRIPTION'])){
        if($where!="") $where .= " AND ";
        $where .= "DESCRIPTION LIKE'%". $_REQUEST['LABEL'] ."%'";
      }
    }
    
    //précomptage
    $count = $this->m_ctrl->bodeviations()->getDeviationsCount($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey,$ItemGroupOID,$ItemGroupKey,$ItemOID,$deviationStatus,$isLast,$where); //deviations count
    if($count>0 && $limit>0) {
    	$total_pages = ceil($count/$limit);
    } else {
    	$total_pages = 0;
    }
    if ($page > $total_pages) $page=$total_pages;
    $start = $limit*$page - $limit; // do not put $limit*($page - 1)
    if ($start<0) $start = 0;
    
    //requêtage final
    $orderBy = $sidx." ".$sord;
    if($limit!=""){
      $limit = "$start , $limit";
    }
    $deviations = $this->m_ctrl->bodeviations()->getDeviationsList($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey,$ItemGroupOID,$ItemGroupKey,$ItemOID,$deviationStatus,$isLast,$where,$orderBy,$limit);
    
    $response->page = $page;
    $response->total = $total_pages;
    $response->records = $count;
    $i=0;
    
    if($mode=="CSV"){ //export CSV
      
      //document d'export
      $tmp = sys_get_temp_dir();
    	$uid = uniqid('deviations');
    	mkdir($tmp.'/'.$uid);
      
      //headers
      header('Content-type: text/csv');
      header('Content-Disposition: attachment; filename="deviations.csv"');
      
      //nettoyage
      ob_end_flush();
      ob_flush(); 
      flush();
      
      $filename = $tmp.'/'.$uid."/deviations.csv";
      $fp = fopen($filename, 'w');
      $line = array("Date", "Site", "Subject", "Visit", "Form", "Section", "Item", "Description", "Status", "URL");
      fputcsv($fp, $line,';');
      
      foreach($deviations as $deviation) {
          $deviationStatus = $this->m_ctrl->bodeviations()->getStatusLabel($deviation['STATUS']);
          
          
          $see = "http://". $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] ."?menuaction=". $this->getCurrentApp(false) .".uietude.subjectInterface&action=view&SubjectKey=". $deviation['SUBJKEY'] ."&StudyEventOID=". $deviation['SEOID'] ."&StudyEventRepeatKey=". $deviation['SERK'] ."&FormOID=". $deviation['FRMOID'] ."&FormRepeatKey=". $deviation['FRMRK'];
          
          $date = substr($deviation['UPDATEDT'],5,2) ."/".substr($deviation['UPDATEDT'],8,2) ."/".substr($deviation['UPDATEDT'],0,4) ." ". substr($deviation['UPDATEDT'],11,8);
          
          $line = array($date,$deviation['SITEID'],$deviation['SUBJKEY'],$description[$MetaDataVersion]['StudyEventDef'][$deviation['SEOID']],$description[$MetaDataVersion]['FormDef'][$deviation['FRMOID']],$description[$MetaDataVersion]['ItemGroupDef'][$deviation['IGOID']],$deviation['ITEMTITLE'],strip_tags($deviation['DESCRIPTION']),$deviationStatus,$see);
          
          fputcsv($fp, $line,';');
      }
      
      readfile($filename);
      
      exit(0);
      
    }else{ //jqGrid
      foreach($deviations as $deviation) {
          $deviationStatus = $this->m_ctrl->bodeviations()->getStatusLabel($deviation['STATUS']);
          
          $history = "";
          if($this->m_ctrl->bodeviations()->getDeviationsCount($deviation['SUBJKEY'],$deviation['SEOID'],$deviation['SERK'],$deviation['FRMOID'],$deviation['FRMRK'],$deviation['IGOID'],$deviation['IGRK'],$deviation['ITEMOID'],"","N") > 0){
            $history = "<div class='imageHistory imageOnly image16 pointer' onClick=\"toggleDeviationHistory('". $this->getCurrentApp(false) ."','". $deviation['DEVIATIONID'] ."'); $(this).toggleClass('imageHistory'); $(this).toggleClass('imageHistoryClose');\" altbox='History'></div>";
          }
          
          $profileId = $this->m_ctrl->boacl()->getUserProfileId("", $deviation['SITEID']);
          $edit = "";
          $imageClass = "imageZoom";
          $canEdit = false;
          if($deviation['STATUS']=="C"){ //personne ne peut modifier le statut d'une deviation CLOSED
            $canEdit = false;
          }elseif($profileId=="INV" && ($deviation['STATUS']=="O" || $deviation['STATUS']=="U")){ //les investigateurs peuvent modifier que le statut des deviations OPEN et UPDATED
            $canEdit = true;
          }
          if($canEdit){
            $imageClass = "imageEdit";
          }
          $edit = "<div class='". $imageClass ." imageOnly image16 pointer' onClick=\"toggleDeviationForm('". $this->getCurrentApp(false) ."','". $profileId ."',". $deviation['DEVIATIONID'] .")\" altbox='Edit'></div>";
          
          $see = "<div class='imageFindIn imageOnly image16 pointer' onClick=\"window.open('index.php?menuaction=". $this->getCurrentApp(false) .".uietude.subjectInterface&action=view&SubjectKey=". $deviation['SUBJKEY'] ."&StudyEventOID=". $deviation['SEOID'] ."&StudyEventRepeatKey=". $deviation['SERK'] ."&FormOID=". $deviation['FRMOID'] ."&FormRepeatKey=". $deviation['FRMRK'] ."')\" altbox='Go to item in the CRF'></div>";
          
          $date = substr($deviation['UPDATEDT'],5,2) ."/".substr($deviation['UPDATEDT'],8,2) ."/".substr($deviation['UPDATEDT'],0,4) ." ". substr($deviation['UPDATEDT'],11,8);
          
          $response->rows[$i]['id'] = "deviation_". $deviation['DEVIATIONID'];
          $response->rows[$i]['cell']=array($date,$deviation['SITEID'],$deviation['SUBJKEY'],$description[$MetaDataVersion]['StudyEventDef'][$deviation['SEOID']],$description[$MetaDataVersion]['FormDef'][$deviation['FRMOID']],$description[$MetaDataVersion]['ItemGroupDef'][$deviation['IGOID']],"<p>".$deviation['ITEMTITLE']."</p>","<p>".$deviation['DESCRIPTION']."</p>",$deviationStatus,$history,$edit,$see);
          $i++;
      }
      
      echo json_encode($response);
    }
  }

 /*
 *@desc méthode ajax, retourne l'historique d'une deviation (liste de deviations)
 *@return array
 *@author tpi
 */
  public function getDeviationHistory(){
    $this->addlog(__METHOD__." : _POST=".$this->dumpRet($_POST),TRACE);
    
    //Extraction des paramètres
    $DEVIATIONID = "";
    $SubjectKey = "";
    $StudyEventOID = "";
    $StudyEventRepeatKey = "";
    $FormOID = "";
    $FormRepeatKey = "";
    $ItemGroupOID = "";
    $ItemGroupKey = "";
    $ItemOID = "";
    if(isset($_POST['DEVIATIONID'])) $DEVIATIONID = $_POST['DEVIATIONID'];
    if(isset($_POST['SubjectKey'])) $SubjectKey = $_POST['SubjectKey'];
    if(isset($_POST['StudyEventOID'])) $StudyEventOID = $_POST['StudyEventOID'];
    if(isset($_POST['StudyEventRepeatKey'])) $StudyEventRepeatKey = $_POST['StudyEventRepeatKey'];
    if(isset($_POST['FormOID'])) $FormOID = $_POST['FormOID'];
    if(isset($_POST['FormRepeatKey'])) $FormRepeatKey = $_POST['FormRepeatKey'];
    if(isset($_POST['ItemGroupOID'])) $ItemGroupOID = $_POST['ItemGroupOID'];
    if(isset($_POST['ItemGroupKey'])) $ItemGroupKey = $_POST['ItemGroupKey'];
    if(isset($_POST['ItemOID'])) $ItemOID = $_POST['ItemOID'];
    
    if($DEVIATIONID!="" && $SubjectKey=="" && $StudyEventOID=="" && $StudyEventRepeatKey=="" && $FormOID=="" && $FormRepeatKey=="" && $ItemGroupOID=="" && $ItemGroupKey=="" && $ItemOID==""){ //si on a l'identifiant de la deviation sans les autres clés on va récupérer les clés
      $deviation = $this->m_ctrl->bodeviations()->getDeviation($DEVIATIONID);
      $SubjectKey = $deviation['SUBJKEY'];
      $StudyEventOID = $deviation['SEOID'];
      $StudyEventRepeatKey = $deviation['SERK'];
      $FormOID = $deviation['FRMOID'];
      $FormRepeatKey = $deviation['FRMRK'];
      $ItemGroupOID = $deviation['IGOID'];
      $ItemGroupKey = $deviation['IGRK'];
      $ItemOID = $deviation['ITEMOID'];
    }else{
      // sinon on ne fait rien : soit on a déjà toutes les clés nécessaires, soit on ne peut même pas les récupérer
    }
    
    $res = $this->m_ctrl->bodeviations()->getDeviationsList($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey,$ItemGroupOID,$ItemGroupKey,$ItemOID,"","N","","DEVIATIONID DESC");
    
    echo json_encode($res);
  }

 /*
 *@desc ajax method, return the content of a file
 *@return string
 *@author TPI
 */
  public function getFileContent(){
    $this->addlog(__METHOD__ ." : _POST=".$this->dumpRet($_POST),TRACE);  
  
    $file = $_POST['file'];
    
    //check what is wanted : regular file or DbXml file ?
    $pathparts = explode("/", $file);
    if($pathparts[count($pathparts)-3]=="dbxml"){
      $containerName = $pathparts[count($pathparts)-2];
      $fileOID = substr($pathparts[count($pathparts)-1], 0, -4); //".xml"
      $content = $this->m_ctrl->boeditor()->getDbxmlFileContent($containerName, $fileOID);
    }else{
      $content = $this->m_ctrl->boeditor()->getFileContent($file);
    }
    
    $res = array("file" => $file, "content" => $content);
    
    echo json_encode($res);
  }

 /*
 *@desc ajax method, save the content of a file
 *@return string
 *@author TPI
 */
  public function setFileContent(){
    $this->addlog(__METHOD__ ." : _POST=".$this->dumpRet($_POST),TRACE);  
    
    $file = $_POST['file'];
    $content = $_POST['content'];
    $content = urldecode($content);
    //$content = stripslashes($content);
    $content = str_replace("\'", "'", $content); //jQuery.ajax() do not add slashes to " and \
    
    
    //check what is wanted : regular file or DbXml file ?
    $pathparts = explode("/", $file);
    if($pathparts[count($pathparts)-3]=="dbxml"){
      $containerName = $pathparts[count($pathparts)-2];
      $fileOID = substr($pathparts[count($pathparts)-1], 0, -4); //".xml"
      $res = $this->m_ctrl->boeditor()->setDbxmlFileContent($containerName, $fileOID, $content);
    }else{
      $res = $this->m_ctrl->boeditor()->setFileContent($file, $content);
    }
    
    echo json_encode($res);
  }

 /*
 *@desc méthode ajax, retourne une query d'après son identifiant
 *@return array
 *@author tpi
 */
  public function getQuery(){
    $this->addlog(__METHOD__ ." : _POST=".$this->dumpRet($_POST),TRACE);  
  
    //Extraction des paramètres
    $QueryId = $_POST['QueryId'];
    
    $query = $this->m_ctrl->boqueries()->getQuery($QueryId);
    
    echo json_encode($query);
  }
  

 /*
 *@desc méthode ajax, retourne la listes des queries filtrées sur les paramètres passé
 *@return array
 *@author tpi
 */
  public function getQueriesList(){
    $this->addlog(__METHOD__ ." : _REQUEST=".$this->dumpRet($_REQUEST),TRACE);  
  
    //Extraction des paramètres
    $SubjectKey = "";
    $StudyEventOID = "";
    $StudyEventRepeatKey = "";
    $FormOID = "";
    $FormRepeatKey = "";
    $ItemGroupOID = "";
    $ItemGroupKey = "";
    $ItemOID = "";
    $position = "";
    $queryStatus = "";
    $isLast = "";
    if(isset($_REQUEST['SubjectKey'])) $SubjectKey = $_REQUEST['SubjectKey'];
    if(isset($_REQUEST['StudyEventOID'])) $StudyEventOID = $_REQUEST['StudyEventOID'];
    if(isset($_REQUEST['StudyEventRepeatKey'])) $StudyEventRepeatKey = $_REQUEST['StudyEventRepeatKey'];
    if(isset($_REQUEST['FormOID'])) $FormOID = $_REQUEST['FormOID'];
    if(isset($_REQUEST['FormRepeatKey'])) $FormRepeatKey = $_REQUEST['FormRepeatKey'];
    if(isset($_REQUEST['ItemGroupOID'])) $ItemGroupOID = $_REQUEST['ItemGroupOID'];
    if(isset($_REQUEST['ItemGroupKey'])) $ItemGroupKey = $_REQUEST['ItemGroupKey'];
    if(isset($_REQUEST['ItemOID'])) $ItemOID = $_REQUEST['ItemOID'];
    if(isset($_REQUEST['position'])) $position = $_REQUEST['position'];
    if(isset($_REQUEST['queryStatus'])) $queryStatus = $_REQUEST['queryStatus'];
    if(isset($_REQUEST['isLast'])) $isLast = $_REQUEST['isLast'];
    
    $queries = $this->m_ctrl->boqueries()->getQueriesList($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey,$ItemGroupOID,$ItemGroupKey,$ItemOID,$position,$queryStatus,$isLast);
    
    echo json_encode($queries);
  }

 /*
 *@desc méthode ajax, retourne la listes des queries filtrées sur les paramètres passés, pour l'interface de gestion globale
 *@return array
 *@author tpi
 */
  public function getQueriesDataList(){
    $this->addlog(__METHOD__ ." : _REQUEST=".$this->dumpRet($_REQUEST),TRACE);  
  
    //Extraction des paramètres       
    $MetaDataVersion = "1.0.0";
    $SubjectKey = "";
    $StudyEventOID = "";
    $StudyEventRepeatKey = "";
    $FormOID = "";
    $FormRepeatKey = "";
    $ItemGroupOID = "";
    $ItemGroupKey = "";
    $ItemOID = "";
    $position = "";
    $queryStatus = "";
    $queryType = "";
    $isLast = "Y";
    $search = "";
    if(isset($_POST['MetaDataVersionOID'])) $MetaDataVersion = $_POST['MetaDataVersionOID'];
    if(isset($_POST['SubjectKey'])) $SubjectKey = $_POST['SubjectKey'];
    if(isset($_POST['StudyEventOID'])) $StudyEventOID = $_POST['StudyEventOID'];
    if(isset($_POST['StudyEventRepeatKey'])) $StudyEventRepeatKey = $_POST['StudyEventRepeatKey'];
    if(isset($_POST['FormOID'])) $FormOID = $_POST['FormOID'];
    if(isset($_POST['FormRepeatKey'])) $FormRepeatKey = $_POST['FormRepeatKey'];
    if(isset($_POST['ItemGroupOID'])) $ItemGroupOID = $_POST['ItemGroupOID'];
    if(isset($_POST['ItemGroupKey'])) $ItemGroupKey = $_POST['ItemGroupKey'];
    if(isset($_POST['ItemOID'])) $ItemOID = $_POST['ItemOID'];
    if(isset($_POST['position'])) $position = $_POST['position'];
    if(isset($_REQUEST['queryStatus'])) $queryStatus = $_REQUEST['queryStatus'];
    if(isset($_REQUEST['queryType'])) $queryType = $_REQUEST['queryType'];
    if(isset($_POST['isLast'])) $isLast = $_POST['isLast'];
    if(isset($_REQUEST['search'])) $search = $_REQUEST['search']; //global search : texte libre
    if(isset($_REQUEST['mode'])) $mode = $_REQUEST['mode']; //jqGrid ou export CSV
    
    $page = $_POST['page']; // get the requested page
    $limit = $_POST['rows']; // get how many rows we want to have into the grid
    $sidx = $_POST['sidx']; // get index row - i.e. user click to sort
    $sord = $_POST['sord']; // get the direction
    if(!$sidx) $sidx =1;
    
    //nous allons avoir besoin des libellés des visites, formulaires et itemgroups présents dans les metadatas
    $description = $this->m_ctrl->bocdiscoo()->getDescriptions($MetaDataVersion);
    
    //application du filtre de recherche
    $where = "";
    
    //filtre global
    if($search!=""){
      $fields = array("UPDATEDT","SITEID","SUBJKEY","ITEMTITLE","LABEL","ANSWER");
      $search = addslashes($search);
      foreach($fields as $field){
        if($where!="") $where .= " OR ";
        $where .= $field." LIKE '%". $search ."%'";
      }
      
      //recherche plus délicate dans les visites, formulaires, sections : il faut d'abord faire la recherche de clés dans le tableau de descriptions pour ensuite inclure la recherche dans le la requête sql
      $desKeys = array("StudyEventDef" => "SEOID", "FormDef" => "FRMOID", "ItemGroupDef" => "IGOID",);
      foreach($desKeys as $descKey => $descValue){
        $descSearches = array();
        foreach($description[$MetaDataVersion][$descKey] as $OID => $desc){
          if(eregi($search,$desc)){
            $descSearches[$OID] = $OID;
          }
        }
        foreach($descSearches as $OID){
          if($where!="") $where .= " OR ";
          $where .= $descValue." = '". $OID ."'";
        }
      }
      if($search!=""){
        $search = "(". $search .")";
      }
    }
    
    //filtre sur le type
    if($queryType!=""){
      $types = explode(",",$queryType);
      $in = "";
      foreach($types as $type){
        if($in!="") $in .= ",";
        $in .= "'$type'";
      }
      if($in!=""){
        if($where!="") $where .= " AND ";
        $where .= "QUERYTYPE IN($in)";
      }
    }
    
    //filtre sur le type
    if(isset($_REQUEST['datePos']) && $_REQUEST['datePos']!="any" && isset($_REQUEST['dateRef']) && $_REQUEST['dateRef']!=""){
      if($_REQUEST['datePos']=="after"){
        $operator = ">";
        $time = "24:00:00";
      }else{
        $operator = "<";
        $time = "00:00:00";
      }
        if($where!="") $where .= " AND ";
        $where .= "UPDATEDT $operator '". $_REQUEST['dateRef'] ." $time'";
    }
    
    //filtres avancés
    if(isset($_REQUEST['_search']) && $_REQUEST['_search']=="true"){
    
      //Site
      if(isset($_REQUEST['SITEID'])){
        if($where!="") $where .= " AND ";
        $where .= "SITEID LIKE'%". $_REQUEST['SITEID'] ."%'";
      }
    
      //Subject
      if(isset($_REQUEST['SUBJKEY'])){
        if($where!="") $where .= " AND ";
        $where .= "SUBJKEY LIKE'%". $_REQUEST['SUBJKEY'] ."%'";
      }
      
      //Visits, Forms, Itemgroups
      $desKeys = array("StudyEventDef" => "SEOID", "FormDef" => "FRMOID", "ItemGroupDef" => "IGOID",);
      foreach($desKeys as $descKey => $descValue){
        if(isset($_REQUEST[$descValue])){
            $descSearches = array(0=>""); //au moins une valeur vide pour valeur vide ou non trouvable
            foreach($description[$MetaDataVersion][$descKey] as $OID => $desc){
              if(eregi($_REQUEST[$descValue],$desc)){
                $descSearches[$OID] = $OID;
              }
            }
            $wherex = "";
            foreach($descSearches as $OID){
              if($wherex!="") $wherex .= " OR ";
              $wherex .= $descValue." = '". $OID ."'";
            }
            if($where!="") $where .= " AND ";
            $where .= "($wherex)";
        }
      }
    
      //Item
      if(isset($_REQUEST['ITEMTITLE'])){
        if($where!="") $where .= " AND ";
        $where .= "ITEMTITLE LIKE'%". $_REQUEST['ITEMTITLE'] ."%'";
      }
    
      //Description
      if(isset($_REQUEST['LABEL'])){
        if($where!="") $where .= " AND ";
        $where .= "LABEL LIKE'%". $_REQUEST['LABEL'] ."%'";
      }
    
      //Comments
      if(isset($_REQUEST['ANSWER'])){
        if($where!="") $where .= " AND ";
        $where .= "ANSWER LIKE'%". $_REQUEST['ANSWER'] ."%'";
      }
    }
    
    //précomptage
    $count = $this->m_ctrl->boqueries()->getQueriesCount($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey,$ItemGroupOID,$ItemGroupKey,$ItemOID,$position,$queryStatus,$isLast,$where); //queries count
    if($count>0 && $limit>0) {
    	$total_pages = ceil($count/$limit);
    } else {
    	$total_pages = 0;
    }
    if ($page > $total_pages) $page=$total_pages;
    $start = $limit*$page - $limit; // do not put $limit*($page - 1)
    if ($start<0) $start = 0;
    
    //requêtage final
    $orderBy = $sidx." ".$sord;
    if($limit!=""){
      $limit = "$start , $limit";
    }
    $queries = $this->m_ctrl->boqueries()->getQueriesList($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey,$ItemGroupOID,$ItemGroupKey,$ItemOID,$position,$queryStatus,$isLast,$where,$orderBy,$limit);
    
    $response->page = $page;
    $response->total = $total_pages;
    $response->records = $count;
    $i=0;
    
    if($mode=="CSV"){ //export CSV
      
      //document d'export
      $tmp = sys_get_temp_dir();
    	$uid = uniqid('queries');
    	mkdir($tmp.'/'.$uid);
      
      //headers
      header('Content-type: text/csv');
      header('Content-Disposition: attachment; filename="queries.csv"');
      
      //nettoyage
      ob_end_flush();
      ob_flush(); 
      flush();
      
      $filename = $tmp.'/'.$uid."/queries.csv";
      $fp = fopen($filename, 'w');
      $line = array("Date", "Site", "Subject", "Visit", "Form", "Section", "Item", "Description", "Type", "Comment", "Status", "URL");
      fputcsv($fp, $line,';');
      
      foreach($queries as $query) {
          $queryType = $this->m_ctrl->boqueries()->getTypeLabel($query['QUERYTYPE']);
          $queryStatus = $this->m_ctrl->boqueries()->getStatusLabel($query['QUERYSTATUS']);
          
          
          $see = "http://". $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] ."?menuaction=". $this->getCurrentApp(false) .".uietude.subjectInterface&action=view&SubjectKey=". $query['SUBJKEY'] ."&StudyEventOID=". $query['SEOID'] ."&StudyEventRepeatKey=". $query['SERK'] ."&FormOID=". $query['FRMOID'] ."&FormRepeatKey=". $query['FRMRK'];
          
          $date = substr($query['UPDATEDT'],5,2) ."/".substr($query['UPDATEDT'],8,2) ."/".substr($query['UPDATEDT'],0,4) ." ". substr($query['UPDATEDT'],11,8);
          
          $line = array($date,$query['SITEID'],$query['SUBJKEY'],$description[$MetaDataVersion]['StudyEventDef'][$query['SEOID']],$description[$MetaDataVersion]['FormDef'][$query['FRMOID']],$description[$MetaDataVersion]['ItemGroupDef'][$query['IGOID']],$query['ITEMTITLE'],strip_tags($query['LABEL']),$queryType,$query['ANSWER'],$queryStatus,$see);
          
          fputcsv($fp, $line,';');
      }
      
      readfile($filename);
      
      exit(0);
      
    }else{ //jqGrid
      foreach($queries as $query) {
          $queryType = "<div class='QueryType". $query['QUERYTYPE'] ." imageOnly image16' altbox='". $this->m_ctrl->boqueries()->getTypeLabel($query['QUERYTYPE']) ."'></div>";
          $queryStatus = "<div class='QueryStatus". $query['QUERYSTATUS'] ." QueryOrigin". $query['QUERYORIGIN'] ." imageOnly image16' altbox='". $this->m_ctrl->boqueries()->getStatusLabel($query['QUERYSTATUS']) ."'></div>";
          
          $history = "";
          if($this->m_ctrl->boqueries()->getQueriesCount($query['SUBJKEY'],$query['SEOID'],$query['SERK'],$query['FRMOID'],$query['FRMRK'],$query['IGOID'],$query['IGRK'],$query['ITEMOID'],$query['POSITION'],"","N") > 0){
            $history = "<div class='imageHistory imageOnly image16 pointer' onClick=\"toggleQueryHistory('". $this->getCurrentApp(false) ."','". $query['QUERYID'] ."'); $(this).toggleClass('imageHistory'); $(this).toggleClass('imageHistoryClose');\" altbox='History'></div>";
          }
          
          $profileId = $this->m_ctrl->boacl()->getUserProfileId("", $query['SITEID']);
          $edit = "";
          $imageClass = "imageZoom";
          $canEdit = false;
          if($query['QUERYSTATUS']=="C"){ //personne ne peut pas modifier le statut d'une query CLOSED
            $canEdit = false;
          }elseif($profileId=="CRA" && !($query['QUERYSTATUS']=="O" && $query['QUERYSTATUS']=="M")){ //l'ARC ne peut pas modifier le statut des queries ouvertes manuellement
            $canEdit = true;
          }elseif($profileId=="INV" && ($query['QUERYSTATUS']=="O" || $query['QUERYSTATUS']=="P")){ //les investigateurs ne peuvent modifier que le statut des queries OPEN et RESOLUTION PROPOSED, 
            $canEdit = true;
          }
          if($canEdit){
            $imageClass = "imageEdit";
          }
          $edit = "<div class='". $imageClass ." imageOnly image16 pointer' onClick=\"toggleQueryForm('". $this->getCurrentApp(false) ."','". $profileId ."',". $query['QUERYID'] .")\" altbox='Edit'></div>";
          
          $see = "<div class='imageFindIn imageOnly image16 pointer' onClick=\"window.open('index.php?menuaction=". $this->getCurrentApp(false) .".uietude.subjectInterface&action=view&SubjectKey=". $query['SUBJKEY'] ."&StudyEventOID=". $query['SEOID'] ."&StudyEventRepeatKey=". $query['SERK'] ."&FormOID=". $query['FRMOID'] ."&FormRepeatKey=". $query['FRMRK'] ."')\" altbox='Go to item in the CRF'></div>";
          
          $date = substr($query['UPDATEDT'],5,2) ."/".substr($query['UPDATEDT'],8,2) ."/".substr($query['UPDATEDT'],0,4) ." ". substr($query['UPDATEDT'],11,8);
          
          $response->rows[$i]['id'] = "query_". $query['QUERYID'];
          $response->rows[$i]['cell']=array($date,$query['SITEID'],$query['SUBJKEY'],$description[$MetaDataVersion]['StudyEventDef'][$query['SEOID']],$description[$MetaDataVersion]['FormDef'][$query['FRMOID']],$description[$MetaDataVersion]['ItemGroupDef'][$query['IGOID']],"<p>".$query['ITEMTITLE']."</p>","<p>".$query['LABEL']."</p>",$queryType,"<p>".$query['ANSWER']."</p>",$queryStatus,$history,$edit,$see);
          $i++;
      }
      
      echo json_encode($response);
    }
  }

 /*
 *@desc méthode ajax, retourne l'historique d'une query (liste de queries)
 *@return array
 *@author tpi
 */
  public function getQueryHistory(){
    $this->addlog(__METHOD__." : _POST=".$this->dumpRet($_POST),TRACE);
    
    //Extraction des paramètres
    $QUERYID = "";
    $SubjectKey = "";
    $StudyEventOID = "";
    $StudyEventRepeatKey = "";
    $FormOID = "";
    $FormRepeatKey = "";
    $ItemGroupOID = "";
    $ItemGroupKey = "";
    $ItemOID = "";
    $position = "";
    if(isset($_POST['QUERYID'])) $QUERYID = $_POST['QUERYID'];
    if(isset($_POST['SubjectKey'])) $SubjectKey = $_POST['SubjectKey'];
    if(isset($_POST['StudyEventOID'])) $StudyEventOID = $_POST['StudyEventOID'];
    if(isset($_POST['StudyEventRepeatKey'])) $StudyEventRepeatKey = $_POST['StudyEventRepeatKey'];
    if(isset($_POST['FormOID'])) $FormOID = $_POST['FormOID'];
    if(isset($_POST['FormRepeatKey'])) $FormRepeatKey = $_POST['FormRepeatKey'];
    if(isset($_POST['ItemGroupOID'])) $ItemGroupOID = $_POST['ItemGroupOID'];
    if(isset($_POST['ItemGroupKey'])) $ItemGroupKey = $_POST['ItemGroupKey'];
    if(isset($_POST['ItemOID'])) $ItemOID = $_POST['ItemOID'];
    if(isset($_POST['position'])) $position = $_POST['position'];
    
    if($QUERYID!="" && $SubjectKey=="" && $StudyEventOID=="" && $StudyEventRepeatKey=="" && $FormOID=="" && $FormRepeatKey=="" && $ItemGroupOID=="" && $ItemGroupKey=="" && $ItemOID=="" && $position==""){ //si on a l'identifiant de la query sans les autres clés on va récupérer les clés
      $query = $this->m_ctrl->boqueries()->getQuery($QUERYID);
      $SubjectKey = $query['SUBJKEY'];
      $StudyEventOID = $query['SEOID'];
      $StudyEventRepeatKey = $query['SERK'];
      $FormOID = $query['FRMOID'];
      $FormRepeatKey = $query['FRMRK'];
      $ItemGroupOID = $query['IGOID'];
      $ItemGroupKey = $query['IGRK'];
      $ItemOID = $query['ITEMOID'];
      $position = $query['POSITION'];
    }else{
      // sinon on ne fait rien : soit on a déjà toutes les clés nécessaires, soit on ne peut même pas les récupérer
    }
    
    $queries = $this->m_ctrl->boqueries()->getQueriesList($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey,$ItemGroupOID,$ItemGroupKey,$ItemOID,$position,"","N","","QUERYID DESC");
    
    echo json_encode($queries);
  }

 /*
 *@desc méthode ajax, exporte les queries dans le document retourné (xslx)
 *@return file
 *@author tpi
 */
 /*
  public function getQueriesExport(){
    $this->addlog(__METHOD__." : _POST=".$this->dumpRet($_POST),TRACE);
    
    $dbg=false;
    
    //headers
    header('Content-type: application/pdf');
    header('Content-Disposition: attachment; filename="queries.xlsx"');
    
    //Multibyte function overloading in PHP must be disabled for string functions (mais la valeur de mbstring.func_overload ne peut pas être modifiée en dehors du php.ini)
    //Multibyte function overloading check commented in Autoloader.php
    ini_set('mbstring.internal_encoding', 'ISO-8859-1');
    
    // Error reporting
    //error_reporting(E_ALL);
    
    date_default_timezone_set('Europe/London');
    
    // PHPExcel
    require_once dirname(__FILE__) .'/PHPExcel.php';
    
    
    // Create new PHPExcel object
    if($dbg)echo date('H:i:s') . " Create new PHPExcel object\n";
    $objPHPExcel = new PHPExcel();
    
    // Set properties
    if($dbg)echo date('H:i:s') . " Set properties\n";
    $objPHPExcel->getProperties()->setCreator("Business & Decision Life Sciences")
    							 ->setLastModifiedBy("Business & Decision Life Sciences")
    							 ->setTitle($configEtude["APP_NAME"])
    							 ->setSubject("Queries")
    							 ->setDescription("Queries Excel 2007 export")
    							 ->setKeywords("queries export ".$configEtude["APP_NAME"])
    							 ->setCategory("export file");
    
    
    // Add some data
    if($dbg)echo date('H:i:s') . " Add some data\n";
    $columns = array("Date", "Site", "Subject", "Visit", "Form", "Section", "Item", "Description", "Type", "Comment", "Status", "URL");
    foreach($columns as $key => $column){
      $objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($key+65).'1', $column);
    }
    
    // Miscellaneous glyphs, UTF-8
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A4', 'Miscellaneous glyphs')
                ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');
    
    // Rename sheet
    if($dbg)echo date('H:i:s') . " Rename sheet\n";
    $objPHPExcel->getActiveSheet()->setTitle('Simple');
    
    
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);
    
    
    // Save Excel 2007 file
    if($dbg)echo date('H:i:s') . " Write to Excel2007 format\n";
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save("queries.xlsx");
    
    
    // Echo memory peak usage
    if($dbg)echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB\r\n";
    
    // Echo done
    if($dbg)echo date('H:i:s') . " Done writing file.\r\n";
    
    //Output
    readfile('queries.xlsx');
    
    exit(0);
  }
*/

 /*
 *@desc méthode ajax, reçoit en paramètre les données d'une deviation à créer (création manuelle)
 *@return ''
 *@author tpi
 */
  public function addDeviation(){
    $this->addlog(__METHOD__." : _POST=".$this->dumpRet($_POST),TRACE);
    
    //Extraction des paramètres
    $Description = utf8_encode($_POST['DESCRIPTION']);
    $Title = utf8_encode($_POST['ITEMTITLE']);
    
    $SubjectKey = $_POST['SubjectKey'];
    $StudyEventOID = $_POST['StudyEventOID'];
    $StudyEventRepeatKey = $_POST['StudyEventRepeatKey'];
    $FormOID = $_POST['FormOID'];
    $FormRepeatKey = $_POST['FormRepeatKey'];
    $ItemGroupOID = $_POST['ItemGroupOID'];
    $ItemGroupRepeatKey = $_POST['ItemGroupRepeatKey'];
    $ItemOID = str_replace("-", ".", $_POST['ItemOID']);
    
    $deviation = array("ItemOID" => $ItemOID,
                       "ItemGroupOID" => $ItemGroupOID,
                       "ItemGroupRepeatKey" => $ItemGroupRepeatKey,
                       "Description" => $Description,
                       "Title" => $Title,
                      );
  
    $res = $this->m_ctrl->bodeviations()->updateDeviation($SubjectKey, $StudyEventOID, $StudyEventRepeatKey, $FormOID, $FormRepeatKey, $deviation);
    
    echo json_encode($res);
  }

 /*
 *@desc méthode ajax, reçoit en paramètre les données d'une deviation à mettre à jour et l'enregistre
 *@return ''
 *@author tpi
 */
  public function updateDeviation(){
    $this->addlog(__METHOD__." : _POST=".$this->dumpRet($_POST),TRACE);
    
    //Extraction des paramètres
    $DEVIATIONID = $_POST['DEVIATIONID'];
    $STATUS = $_POST['STATUS'];
    $DESCRIPTION = utf8_encode($_POST['DESCRIPTION']);
    
    //Mise à jour : on a besoin des infos originales de la deviation
    $sql = "SELECT *
              FROM egw_alix_deviations
              WHERE CURRENTAPP='".$this->getCurrentApp(true)."' AND
                            DEVIATIONID='$DEVIATIONID' AND
                            ISLAST='Y'";
    $this->addLog(__METHOD__." : sql = ".$sql,TRACE);
    $GLOBALS['egw']->db->query($sql);
    if($GLOBALS['egw']->db->next_record()){
      $SubjectKey = $GLOBALS['egw']->db->f('SUBJKEY');
      $StudyEventOID = $GLOBALS['egw']->db->f('SEOID');
      $StudyEventRepeatKey = $GLOBALS['egw']->db->f('SERK');
      $FormOID = $GLOBALS['egw']->db->f('FRMOID');
      $FormRepeatKey = $GLOBALS['egw']->db->f('FRMRK');
      $deviation = array("ItemOID" => $GLOBALS['egw']->db->f('ITEMOID'),
                         "ItemGroupOID" => $GLOBALS['egw']->db->f('IGOID'),
                         "ItemGroupRepeatKey" => $GLOBALS['egw']->db->f('IGRK'),
                         "Description" => $DESCRIPTION,
                         "Title" => $GLOBALS['egw']->db->f('ITEMTITLE')
                        );
      $status = $STATUS;
      
      $res = $this->m_ctrl->bodeviations()->updateDeviation($SubjectKey, $StudyEventOID, $StudyEventRepeatKey, $FormOID, $FormRepeatKey, $deviation, $status);
      
      echo json_encode($res);
    }else{
      die("Error : unknown or already closed deviation '$DEVIATIONID'.");
    }
  }

 /*
 *@desc méthode ajax, reçoit en paramètre les données d'une querie à mettre à jour et l'enregistre
 *@history : WLT 29/07/2011 => on ne fait plus tourner les controles à chaque mise à jour de query, uniquement sur la dernière du formulaire 
 *@author tpi,wlt
 */
  public function updateQuery(){
    $this->addlog(__METHOD__." : _POST=".$this->dumpRet($_POST),TRACE);
    
    //Extraction des paramètres
    $QUERYID = $_POST['QUERYID'];
    $QUERYSTATUS = $_POST['QUERYSTATUS'];
    $ANSWER = utf8_encode($_POST['ANSWER']);
    
    //Mise à jour : on a besoin des infos originales de la query
    $sql = "SELECT *
              FROM egw_alix_queries
              WHERE CURRENTAPP='".$this->getCurrentApp(true)."' AND
                            QUERYID='$QUERYID' AND
                            ISLAST='Y'";
    $this->addLog(__METHOD__." : sql = ".$sql,TRACE);
    $GLOBALS['egw']->db->query($sql);
    if($GLOBALS['egw']->db->next_record()){
      $SubjectKey = $GLOBALS['egw']->db->f('SUBJKEY');
      $StudyEventOID = $GLOBALS['egw']->db->f('SEOID');
      $StudyEventRepeatKey = $GLOBALS['egw']->db->f('SERK');
      $FormOID = $GLOBALS['egw']->db->f('FRMOID');
      $FormRepeatKey = $GLOBALS['egw']->db->f('FRMRK');
      $isManual = true;
      $answer = $ANSWER;
      $query = array("ItemOID" => $GLOBALS['egw']->db->f('ITEMOID'),
                     "ItemGroupOID" => $GLOBALS['egw']->db->f('IGOID'),
                     "ItemGroupRepeatKey" => $GLOBALS['egw']->db->f('IGRK'),
                     "Description" => $GLOBALS['egw']->db->f('LABEL'),
                     "Title" => $GLOBALS['egw']->db->f('ITEMTITLE'),
                     'Position' => $GLOBALS['egw']->db->f('POSITION'),
                     'Type' => $GLOBALS['egw']->db->f('QUERYTYPE'),
                     'ContextKey' => $GLOBALS['egw']->db->f('CONTEXTKEY'),
                     'Value' => $GLOBALS['egw']->db->f('VALUE'),
                     'Decode' => $GLOBALS['egw']->db->f('DECODE')
                    );
      $queryStatus = $QUERYSTATUS;
    
      $res = $this->m_ctrl->boqueries()->updateQuery($SubjectKey, $StudyEventOID, $StudyEventRepeatKey, $FormOID, $FormRepeatKey, $isManual, $answer, $query, $queryStatus);
      
      //We run checks only if there is no pending queries for the current form
      if($this->m_ctrl->boqueries()->getQueriesCount($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey,"","","","","","Y","QUERYSTATUS<>'C'")==0){
        $this->m_ctrl->bocdiscoo()->updateFormStatus($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey);
        $res['ForceReload'] = true;
      }else{
        $res['ForceReload'] = false;
      }
      echo json_encode($res);
    }else{
      die("Error : unknown or already closed querie '$QUERYID'.");
    }
  }

 /*
 *@desc méthode ajax, reçoit en paramètre les données d'une querie à créer (création manuelle)
 *@return ''
 *@author tpi
 */
  public function addQuery(){
    $this->addlog(__METHOD__." : _POST=".$this->dumpRet($_POST),TRACE);
    
    //Extraction des paramètres
    $Description = utf8_encode($_POST['LABEL']);
    $answer = utf8_encode($_POST['ANSWER']);
    $Type = $_POST['QUERYTYPE'];
    $Title = utf8_encode($_POST['ITEMTITLE']);
    
    $SubjectKey = $_POST['SubjectKey'];
    $StudyEventOID = $_POST['StudyEventOID'];
    $StudyEventRepeatKey = $_POST['StudyEventRepeatKey'];
    $FormOID = $_POST['FormOID'];
    $FormRepeatKey = $_POST['FormRepeatKey'];
    $ItemGroupOID = $_POST['ItemGroupOID'];
    $ItemGroupRepeatKey = $_POST['ItemGroupRepeatKey'];
    $ItemOID = str_replace("-", ".", $_POST['ItemOID']);
    $isManual = true;
    $value = $this->m_ctrl->bocdiscoo()->getValue($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey,$ItemGroupOID,$ItemGroupRepeatKey,$ItemOID);
    $decodedValue = $this->m_ctrl->bocdiscoo()->getDecodedValue($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey,$ItemGroupOID,$ItemGroupRepeatKey,$ItemOID);
    if($decodedValue==""){
      $decodedValue = $value;
    }
    
    //il s'agit d'une query manuelle, nous avons besoin d'un identifiatn de position => on utilise un identifiant négatif pour distinguer cette querie manuelle de celle issue des rangeCheck
    $sql = "SELECT MIN(POSITION) as MINPOSITION
              FROM egw_alix_queries
              WHERE CURRENTAPP='".$this->getCurrentApp(true)."' AND
                            SUBJKEY='$SubjectKey' AND
                            SEOID='$StudyEventOID' AND
                            SERK='$StudyEventRepeatKey' AND
                            FRMOID='$FormOID' AND
                            FRMRK='$FormRepeatKey' AND
                            IGOID='$ItemGroupOID' AND
                            IGRK='$ItemGroupRepeatKey' AND
                            ITEMOID='$ItemOID'";
    $this->addLog(__METHOD__." : sql = ".$sql,TRACE);
    $GLOBALS['egw']->db->query($sql);
    if($GLOBALS['egw']->db->next_record()){
      if($GLOBALS['egw']->db->f('MINPOSITION') == "1" || $GLOBALS['egw']->db->f('MINPOSITION') == ""){
        $Position = "-1";
      }else{
        $Position = $GLOBALS['egw']->db->f('MINPOSITION') - 1;
      }
    }else{
      $Position = "-1";
    }
    
    $query = array("ItemOID" => $ItemOID,
                   "ItemGroupOID" => $ItemGroupOID,
                   "ItemGroupRepeatKey" => $ItemGroupRepeatKey,
                   "Description" => $Description,
                   "Title" => $Title,
                   'Position' => $Position,
                   'Type' => $Type,
                   'Value' => $value,
                   'Decode' => $decodedValue
                  );
    $queryStatus = $QUERYSTATUS;
  
    $res = $this->m_ctrl->boqueries()->updateQuery($SubjectKey, $StudyEventOID, $StudyEventRepeatKey, $FormOID, $FormRepeatKey, $isManual, $answer, $query);
    
    $this->m_ctrl->bocdiscoo()->updateFormStatus($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey);
    
    echo json_encode($res);
  }

  public function getAuditTrail(){
    try{
      $this->addlog(__METHOD__." : _POST=".$this->dumpRet($_POST),TRACE);  
    
      //Extraction des paramètres
      $SubjectKey = $_POST['SubjectKey'];
      $StudyEventOID = $_POST['StudyEventOID'];
      $StudyEventRepeatKey = $_POST['StudyEventRepeatKey'];
      $FormOID = $_POST['FormOID'];
      $FormRepeatKey = $_POST['FormRepeatKey'];
      $ItemGroupOID = $_POST['ItemGroupOID'];
      $ItemGroupRepeatKey = $_POST['ItemGroupRepeatKey'];
      $ItemOID = $_POST['ItemOID'];
      
      //On récupère  l'audit trail (changement des valeurs)
      $itemAT = $this->m_ctrl->bocdiscoo()->getAuditTrail($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey,$ItemGroupOID,$ItemGroupRepeatKey,$ItemOID,true);
      
      $history = array();
      $audit = array();
      
      $ItemGroupDataAT = $itemAT->firstChild;
      foreach($ItemGroupDataAT->childNodes as $ItemDataAT){
        $item = array();
        $item['type'] = "Value";
        $item['itemoid'] = $ItemDataAT->getAttribute("ItemOID");
        $item['value'] = $ItemDataAT->getAttribute("Value");
        $item['transaction'] = $ItemDataAT->getAttribute("TransactionType");
        foreach($ItemDataAT->childNodes as $childNode){
          if($childNode->nodeName == "AuditRecord"){
            $item['user'] = $childNode->getAttribute("User");
            $item['date'] = $childNode->getAttribute("Date");
            //on reformate la date au format "Y-m-d H:i:s" pour être en adéquatin avec le format de date des queries
            $item['date'] = date("Y-m-d H:i:s", mktime(substr($item['date'], 11, 2) , substr($item['date'], 14, 2), substr($item['date'], 17, 2), substr($item['date'], 5, 2), substr($item['date'], 8, 2), substr($item['date'], 0, 4)) );
          }
          if($childNode->nodeName == "Annotation"){
            $item['flagvalue'] = $childNode->getAttribute("FlagValue");
            $item['flagcomment'] = $childNode->getAttribute("Comment");
          }
        }
        $audit[] = $item;
      }
      
      //On récupère l'audit des queries (historiques des queries)
      $queriesAT = $this->m_ctrl->boqueries()->getQueriesList($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey,$ItemGroupOID,$ItemGroupRepeatKey,$ItemOID);
      foreach($queriesAT as $query){
        $item = array();
        $item['type'] = "Query";
        $item['itemoid'] = $query['ITEMOID'];
        $item['value'] = $query['LABEL'];
        $item['transaction'] = $query['QUERYSTATUS'];
        $item['user'] = $query['BYWHO'];
        $item['date'] = $query['UPDATEDT'];
        $item['flagvalue'] = "";
        $item['flagcomment'] = "";
        
        $audit[] = $item;
      }
      
      //On récupère l'audit des deviations (historiques des deviations)
      $deviationsAT = $this->m_ctrl->bodeviations()->getDeviationsList($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey,$ItemGroupOID,$ItemGroupRepeatKey,$ItemOID);
      foreach($deviationsAT as $deviation){
        $item = array();
        $item['type'] = "Deviation";
        $item['itemoid'] = $deviation['ITEMOID'];
        $item['value'] = $deviation['DESCRIPTION'];
        $item['transaction'] = $deviation['STATUS'];
        $item['user'] = $deviation['BYWHO'];
        $item['date'] = $deviation['UPDATEDT'];
        $item['flagvalue'] = "";
        $item['flagcomment'] = "";
        
        $audit[] = $item;
      }
      
      //On ordonne le contenu par date décroissante (du plus récent au plus ancien)
      usort($audit, array("ajax", "sortAuditTrailByDate"));
      
      $res = $audit;
      
    }catch(Exception $e){
      $res = $e->getMessage();
    }
    
    echo json_encode($res);
  }
  
  /*
  *@desc méthode privée, tri du tableau d'audit (par date DESC) retourné par la fonction public getAuditTrail
  *@return integer -1 0 1
  *@author tpi
  */
  private function sortAuditTrailByDate($a, $b){
    return ($a['date'] < $b['date']) ? 1 : (($a['date'] > $b['date']) ? -1 : ($a['type']=='Query'?-1:+1));
  }

 /*
 *@desc méthode ajax, retourne la liste des patients
 *@return json liste des patients
 *@author wlt
 */
 public function getSubjectsList(){
  $page = $_POST['page'];   
  // get how many rows we want to have into the grid - rowNum parameter in the grid 
  $limit = $_POST['rows'];   
  // get index row - i.e. user click to sort. At first time sortname parameter -
  // after that the index from colModel 
  $sidx = $_POST['sidx'];   
  // sorting order - at first time sortorder 
  $sord = $_POST['sord'];   
  // if we not pass at first time index use the first column for the index or what you want
  if(!$sidx) $sidx =1; 

  $totalrows = isset($_POST['totalrows']) ? $_POST['totalrows']: false; 
  if($totalrows){ 
    $limit = $totalrows; 
  }
  
  $tblSubj = $this->m_ctrl->bocdiscoo()->getSubjectList("");     
  
  $tblRet->total = 1; 
  $tblRet->page = 1; 
  $tblRet->records = count($tblSubj[0]);
  
  $tblRet->rows = array(); 
  $i = 0;
  //$user = $this->m_ctrl->boacl()->getCurrentUserInfo();
  foreach($tblSubj[0] as $subj)
  {        
    //Est-ce l'utilisateur connecté a le droit d'accéder à ce patient ?
    //$userProfile = $this->m_ctrl->boacl()->getUserSiteProfile($user['login'],$subj['colSITEID']);
    $userProfileId = $this->m_ctrl->boacl()->getUserProfileId("",$subj['colSITEID']);
    
    if($userProfileId!="" && $subj['fileOID']!="BLANK"){    
      $tblRet->rows[$i]['id']=(string)($subj['fileOID']);
      foreach($this->m_tblConfig['SUBJECT_LIST']['COLS'] as $key=>$col){
        $tblRet->rows[$i]['cell'][] = (string)($subj["col$key"]); 
      }
    }
    $i++;
  }
  
  echo json_encode($tblRet);  
 
 }

 /*
 *@desc méthode ajax, retourne la listes des patients filtrées sur les paramètres passés
 *@return array
 *@author tpi
 */
 /*
  public function getSubjectsDataList(){
    $this->addlog(__METHOD__ ." : _REQUEST=".$this->dumpRet($_REQUEST),TRACE);  

    //Extraction des paramètres       
    $MetaDataVersion = "1.0.0";
    $SubjectKey = "";
    $search = "";
    if(isset($_POST['MetaDataVersionOID'])) $MetaDataVersion = $_POST['MetaDataVersionOID'];
    if(isset($_POST['SubjectKey'])) $SubjectKey = $_POST['SubjectKey'];
    if(isset($_REQUEST['search'])) $search = $_REQUEST['search']; //global search : texte libre
    if(isset($_REQUEST['mode'])) $mode = $_REQUEST['mode']; //jqGrid ou export CSV
    
    $page = $_POST['page']; // get the requested page
    $limit = $_POST['rows']; // get how many rows we want to have into the grid
    $sidx = $_POST['sidx']; // get index row - i.e. user click to sort
    $sord = $_POST['sord']; // get the direction
    if(!$sidx) $sidx =1;
    
    //application du filtre de recherche
    $where = "";

    //We need to open all dbxml
    $this->m_ctrl->socdiscoo("");
    
    //Récupération de la liste complète des patients
    //Do we have a cached copy ?
    $cacheFile = $this->m_tblConfig["CDISCOO_PATH"] . "/cache/subjectList";   
    if($this->m_tblConfig["CACHE_ENABLED"] && file_exists($cacheFile)){
      $tblSubj = array();
      $tblSubj[0] = simplexml_load_file($cacheFile);
    }else{       
      $tblSubj = $this->m_ctrl->bocdiscoo()->getSubjectList("");
      $tblSubj[0]->asXML($cacheFile);     
    }
    
    //Profil par défaut de l'utilisateur
    $defaultProfilId = $this->m_ctrl->boacl()->getUserProfileId();
    
    //filtrage et comptage
    $subjs = $tblSubj[0]->children();
    $count = count($subjs);

    for($i=0; $i<$count; $i++){
      //Est-ce l'utilisateur connecté a le droit d'accéder à ce patient ?
      $profileId = $this->m_ctrl->boacl()->getUserProfileId("",$subjs[$i]['colSITEID']);
      
      if(isset($profileId) && $profileId!="" && $subjs[$i]['fileOID']!="BLANK" || $defaultProfilId=="SPO"){       
        //Ajout de l'information de staut du patient
        $subjs[$i]['SUBJECTSTATUS'] = $this->m_ctrl->bosubjects()->getSubjectStatus($subjs[$i]);
        
        //filtres avancés
        if(isset($_REQUEST['_search']) && $_REQUEST['_search']=="true"){
        
          foreach($this->m_tblConfig['SUBJECT_LIST']['COLS'] as $key=>$col){
            if(isset($_REQUEST['col'.$key])){
              if(stripos($subjs[$i]['col'.$key], $_REQUEST['col'.$key]) === false){
                //delete subject
                unset($subjs[$i]);
                $i--;
                $count--;
                break 1;
              }
            }
          }
          
          //autres filtres avancées
          if(isset($subjs[$i])){
            if(isset($_REQUEST['SUBJECTSTATUS'])){
              if(stripos($subjs[$i]['SUBJECTSTATUS'], $_REQUEST['SUBJECTSTATUS']) === false){
                unset($subjs[$i]);
                $i--;
                $count--;
              }
            }
          }
        }
      }else{
        //delete subject
        unset($subjs[$i]);
        $i--;
        $count--;
      }
    }
    
    //pagination
    if($count>0 && $limit>0) {
    	$total_pages = ceil($count/$limit);
    } else {
    	$total_pages = 0;
    }
    if ($page > $total_pages) $page=$total_pages;
    $start = $limit*$page - $limit; // do not put $limit*($page - 1)
    if ($start<0) $start = 0;
    
    $response->page = $page;
    $response->total = $total_pages;
    $response->records = $count;
    $i=0; //current item in all set
    $j=0; //current (number) item being added
    
    foreach($subjs as $subj) {
      if($i>=$start){
        $see = "<div class='imageFindIn imageOnly image16 pointer' onClick=\"location.href='index.php?menuaction=". $this->getCurrentApp(false) .".uietude.subjectInterface&action=view&SubjectKey=". $subj['fileOID'] ."&StudyEventOID=". $this->m_tblConfig['ENROL_SEOID'] ."&StudyEventRepeatKey=". $this->m_tblConfig['ENROL_SERK'] ."&FormOID=". $this->m_tblConfig['ENROL_FORMOID'] ."&FormRepeatKey=". $this->m_tblConfig['ENROL_FORMRK'] ."'\" altbox='Go to CRF'></div>";
        $status = $this->m_ctrl->bocdiscoo()->getSubjectStatus($subj['fileOID']);
        $statusCRF = "<div class='imageStatus$status imageOnly image16' altbox='$status' ></div>";
        if($this->m_ctrl->boacl()->existUserProfileId("DM","",$subj['colSITEID'])){
          $check = "<button class='ui-state-default ui-corner-all' onClick=\"if(window.event){ var e = window.event; e.cancelBubble = true; if(e && e.stopPropagation){ e.stopPropagation();};}else{event.stopPropagation();}; runConsistencyChecks('". $GLOBALS['egw_info']['flags']['currentapp'] ."', '". $subj['colSITEID'] ."', '". $subj['fileOID'] ."');\">Run consistency checks</button>";
        }else{
          $check = "";
        }
        
        $response->rows[$j]['id'] = "subject_". $subj['fileOID'];
        $response->rows[$j]['cell']=array();
        $response->rows[$j]['cell'][] = $see;
        foreach($this->m_tblConfig['SUBJECT_LIST']['COLS'] as $key=>$col){
          if($col['Visible']){
            $response->rows[$j]['cell'][] = (string)$subj['col'.$key];
          }
        }
        $response->rows[$j]['cell'][] = (string)$subj['SUBJECTSTATUS'];
        $response->rows[$j]['cell'][] = $statusCRF;
        $response->rows[$j]['cell'][] = $check;
        $j++;
      }
      $i++;
      if($j>=$limit){
        break;
      }
    }
  
    //We save the generated response into the cache
    echo json_encode($response);
  }
 */

 /*
 *@desc méthode ajax, retourne la listes des patients filtrées sur les paramètres passés
 *@return array
 *@author tpi
 */
  public function getSubjectsDataList(){
    $this->addlog(__METHOD__ ." : _REQUEST=".$this->dumpRet($_REQUEST),TRACE);  
  
    //Extraction des paramètres       
    $MetaDataVersion = "1.0.0";
    $SubjectKey = "";
    $search = "";
    if(isset($_POST['MetaDataVersionOID'])) $MetaDataVersion = $_POST['MetaDataVersionOID'];
    if(isset($_POST['SubjectKey'])) $SubjectKey = $_POST['SubjectKey'];
    if(isset($_REQUEST['search'])) $search = $_REQUEST['search']; //global search : texte libre
    
    if(isset($_GET['excelExport']) && $_GET['excelExport']=='true'){
      $mode = "csv";
    }else{
      $mode = "json";
    }
    
    $page = $_POST['page']; // get the requested page
    $limit = $_POST['rows']; // get how many rows we want to have into the grid
    $sidx = $_POST['sidx']; // get index row - i.e. user click to sort
    $sord = $_POST['sord']; // get the direction
    if(!$sidx) $sidx =1;
    
    //application du filtre de recherche
    $where = "";
    
    //retrieving subjects list
    $tblSubj = $this->m_ctrl->bosubjects()->getSubjectsList("");

    //Profil par défaut de l'utilisateur
    $defaultProfilId = $this->m_ctrl->boacl()->getUserProfileId();
    
    //filtrage et comptage
    $tblSubjs = $tblSubj[0]->children();
    foreach($tblSubjs as $subj){ //ordering => using an array !
      $subjs[] = $subj;
    }
    usort($subjs, create_function('$a,$b', 'return ((integer)$a->SubjectKey<(integer)$b->SubjectKey ? -1 : 1);'));
    $count = count($subjs);
    for($i=0; $i<$count; $i++){
      //Est-ce l'utilisateur connecté a le droit d'accéder à ce patient ?
      $profileId = $this->m_ctrl->boacl()->getUserProfileId("",(string)$subjs[$i]->colSITEID);
      
      if(isset($profileId) && $profileId!="" && $subjs[$i]->fileOID!="BLANK" || $defaultProfilId=="SPO"){
        
        //filtres avancés
        if(isset($_REQUEST['_search']) && $_REQUEST['_search']=="true"){
          foreach($this->m_tblConfig['SUBJECT_LIST']['COLS'] as $key=>$col){
            if(isset($_REQUEST['col'.$key])){
              eval("\$colValue = \$subjs[\$i]->col$key;");
              if(stripos($colValue, $_REQUEST['col'.$key]) === false){
                $subjs[$i] = false;
                break 1;
              }
            }
          }
          
          //autres filtres avancées
          if(isset($subjs[$i])){
            if(isset($_REQUEST['SUBJECTSTATUS'])){
              if(stripos($subjs[$i]->SUBJECTSTATUS, $_REQUEST['SUBJECTSTATUS']) === false){
                $subjs[$i] = false;
              }
            }
          }
        }
      }else{
        $subjs[$i] = false;
      }
    }
    
    for($i=0; $i<$count; $i++){
      if(!$subjs[$i]) unset($subjs[$i]);
    }
    $count = count($subjs);
    
    //pagination
    if($count>0 && $limit>0) {
    	$total_pages = ceil($count/$limit);
    } else {
    	$total_pages = 0;
    }
    if ($page > $total_pages) $page=$total_pages;
    $start = $limit*$page - $limit; // do not put $limit*($page - 1)
    if ($start<0) $start = 0;
    
    $response->page = $page;
    $response->total = $total_pages;
    $response->records = $count;
    $i=0; //current item in all set
    $j=0; //current (number) item being added
    
    foreach($subjs as $subj) {
      if($i>=$start){
        $see = "<div class='imageFindIn imageOnly image16 pointer' onClick=\"location.href='index.php?menuaction=". $this->getCurrentApp(false) .".uietude.subjectInterface&action=view&SubjectKey=". $subj->fileOID ."&StudyEventOID=". $this->m_tblConfig['ENROL_SEOID'] ."&StudyEventRepeatKey=". $this->m_tblConfig['ENROL_SERK'] ."&FormOID=". $this->m_tblConfig['ENROL_FORMOID'] ."&FormRepeatKey=". $this->m_tblConfig['ENROL_FORMRK'] ."'\" altbox='Go to CRF'></div>";
        $status = $subj->CRFSTATUS;
        if($mode=="json"){
          $statusCRF = "<div class='imageStatus$status imageOnly image16' altbox='$status' ></div>";
        }else{
          $statusCRF = $statut;
        }
        if($this->m_ctrl->boacl()->existUserProfileId("DM","",$subj->colSITEID)){
          $check = "<button class='ui-state-default ui-corner-all' onClick=\"if(window.event){ var e = window.event; e.cancelBubble = true; if(e && e.stopPropagation){ e.stopPropagation();};}else{event.stopPropagation();}; runConsistencyChecks('". $GLOBALS['egw_info']['flags']['currentapp'] ."', '". $subj->colSITEID ."', '". $subj->fileOID ."');\">Run consistency checks</button>";
        }else{
          $check = "";
        }
        
        //$this->m_ctrl->bosubjects()->updateSubjectsList($subj->fileOID);
        
        $response->rows[$j]['id'] = "subject_". $subj->fileOID;
        $response->rows[$j]['cell']=array();
        if($mode=="json"){
          $response->rows[$j]['cell'][] = $see;
        }
        foreach($this->m_tblConfig['SUBJECT_LIST']['COLS'] as $key=>$col){
          if($col['Visible']){
            if($col['Type']=="VISITSTATUS"){
              $query = "./formList/SubjectData/StudyEventData[@StudyEventOID='{$col['Value']['SEOID']}' and @StudyEventRepeatKey='{$col['Value']['SERK']}']";
              $StudyEvent = $subj->xpath($query);
              $status = (string)$StudyEvent[0]['Status'];
              if($mode=="json"){
                $colValue = "<div class='imageStatus$status imageOnly image16' altbox='$status' ></div>";  
              }else{
                $colValue = $status;
              }
            }else{
              eval("\$colValue = (string)\$subj->col".$key.";");
            }            
            $response->rows[$j]['cell'][] = $colValue;
          }
        }
        $response->rows[$j]['cell'][] = (string)$subj->SUBJECTSTATUS;
        $response->rows[$j]['cell'][] = $this->m_ctrl->bopostit()->getPostItCount($subj->fileOID);
        $response->rows[$j]['cell'][] = $this->m_ctrl->boqueries()->getQueriesCount($subj->fileOID,"","","","","","","","","","Y","QUERYSTATUS<>'C'");
        $response->rows[$j]['cell'][] = $statusCRF;
        $response->rows[$j]['cell'][] = $check;
        $j++;
      }
      $i++;
      if($limit>0 && $j>=$limit){
        break;
      }
    }
    
    if(isset($_GET['excelExport']) && $_GET['excelExport']=='true'){
      header("Content-Disposition: attachment; filename=subjectsList.csv");
      header("Content-Type: application/csv");
      header("Content-Description: File Transfer");
      header("Pragma: no-cache");
      header("Expires: 0");
      
      $header = array();
      foreach($this->m_tblConfig['SUBJECT_LIST']['COLS'] as $key=>$col){
        if($col['Visible']){
          $header[] = utf8_decode($col['Title']);
        }
      }
      $header[] = "Subject Status";
      $header[] = "Post-It number";
      $header[] = "Opened queries number";
      $header[] = "CRF Status";
      $buffer = fopen('php://output', 'w');
      fputcsv($buffer, $header,";");
      foreach($response->rows as $row){
        //print_r($row['cell']);
        fputcsv($buffer, $row['cell'],";");
      }

      fclose($buffer);
      
      echo $csv;            
    }else{
      echo json_encode($response);
    }
  }
 
 /*
 *@desc méthode ajax, reçoit en paramètre les données d'un post-it sur son ItemOID et les enregistre
 *@return true on success
 *@author tpi
 */
  public function savePostIt(){
    $this->addlog(__METHOD__ ." : _POST=".$this->dumpRet($_POST),TRACE);  
  
    //Tableau de retour
    $tblRet = array();
  
    //Extraction des paramètres
    $SubjectKey = $_POST['SubjectKey'];
    $StudyEventOID = $_POST['StudyEventOID'];
    $StudyEventRepeatKey = $_POST['StudyEventRepeatKey'];
    $FormOID = $_POST['FormOID'];
    $FormRepeatKey = $_POST['FormRepeatKey'];
    $ItemGroupOID = $_POST['ItemGroupOID'];
    $ItemGroupRepeatKey = $_POST['ItemGroupRepeatKey'];
    $ItemOID = $_POST['ItemOID'];
    $txt = $_POST['txt'];
    $txt = utf8_encode($txt);
      
    //Enregistrement des données 
    $res = $this->m_ctrl->bopostit()->savePostIt($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey,$ItemGroupOID,$ItemGroupRepeatKey,$ItemOID,$txt);
    
    echo json_encode($res);   
  }
 
 
 /*
 *@desc méthode ajax, reçoit en paramètre l'identifiant d'un patient et retourne la liste edes formulaires contenant un ou des post-its
 *@return array
 *@author tpi
 */
  public function getPostItFormList(){
    $this->addlog(__METHOD__ ." : _POST=".$this->dumpRet($_POST),TRACE);  
  
    //Extraction des paramètres
    $SubjectKey = $_POST['SubjectKey'];
      
    //Enregistrement des données 
    $res = $this->m_ctrl->bopostit()->getPostItFormList($SubjectKey);
    
    echo json_encode($res);   
  }
 
 /*
 *@desc méthode ajax, reçoit en paramètre l'identification d'un post-it sur son ItemOID et le supprime
 *@return true on success
 *@author tpi
 */
  public function deletePostIt(){
    $this->addlog(__METHOD__ ." : _POST=".$this->dumpRet($_POST),TRACE);  
  
    //Tableau de retour
    $tblRet = array();
  
    //Extraction des paramètres
    $SubjectKey = $_POST['SubjectKey'];
    $StudyEventOID = $_POST['StudyEventOID'];
    $StudyEventRepeatKey = $_POST['StudyEventRepeatKey'];
    $FormOID = $_POST['FormOID'];
    $FormRepeatKey = $_POST['FormRepeatKey'];
    $ItemGroupOID = $_POST['ItemGroupOID'];
    $ItemGroupRepeatKey = $_POST['ItemGroupRepeatKey'];
    $ItemOID = $_POST['ItemOID'];
      
    //Suppression du post-it
    $res = $this->m_ctrl->bopostit()->deletePostIt($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey,$ItemGroupOID,$ItemGroupRepeatKey,$ItemOID);
    
    echo json_encode($res);   
  }
 
 /*
 *@desc méthode ajax, reçoit en paramètre l'identification d'un formulaire et en retourne un tableau de post-it
 *@return array
 *@author tpi
 */
  public function getPostItList(){
    $this->addlog(__METHOD__ ." : _POST=".$this->dumpRet($_POST),TRACE);  
  
    //Tableau de retour
    $tblRet = array();
  
    //Extraction des paramètres
    $SubjectKey = $_POST['SubjectKey'];
    $StudyEventOID = $_POST['StudyEventOID'];
    $StudyEventRepeatKey = $_POST['StudyEventRepeatKey'];
    $FormOID = $_POST['FormOID'];
    $FormRepeatKey = $_POST['FormRepeatKey'];
      
    //Récupération d'un tableau de post-it
    $res = $this->m_ctrl->bopostit()->getPostItList($SubjectKey,$StudyEventOID,$StudyEventRepeatKey,$FormOID,$FormRepeatKey);
    
    echo json_encode($res);   
  }
 
 /*
 *@desc méthode ajax, retourne la liste des formulaires existant dans le CRF d'un patient
 *@return array
 *@author tpi
 */
  public function getFormDataList(){
    $this->addlog(__METHOD__ ." : _POST=".$this->dumpRet($_POST),TRACE);  
  
    //Tableau de retour
    $tblRet = array();
  
    //Extraction des paramètres
    $SubjectKey = $_POST['SubjectKey'];
      
    //Récupération d'un tableau de post-it
    $forms = $this->m_ctrl->bocdiscoo()->getFormDatas($SubjectKey,"","",true);
    $res = array();
    foreach($forms as $form){
      $res[] = array(
                     "StudyEventOID" => (string)$form['StudyEventOID'],
                     "StudyEventRepeatKey" => (string)$form['StudyEventRepeatKey'],
                     "FormOID" => (string)$form['FormOID'],
                     "FormRepeatKey" => (string)$form['FormRepeatKey']
               );
    }
    
    echo json_encode($res);
  }

 /*
 *@desc ajax, save editor preferences (session)
 *@return array preferences
 *@author TPI
 */
  public function storeEditorPreferences(){
    
    $theme = $_POST['theme'];
    $fontsize = $_POST['fontsize'];
    
    $preferences = $this->m_ctrl->boeditor()->storePreferences($theme,$fontsize);
    
    echo json_encode($preferences);
  }

 /*
 *@desc ajax, create a file
 *@return filename on success
 *@author TPI
 */
  public function createFile(){
    
    $root = $_POST['root'];
    $folder = $_POST['folder'];
    $filename = $_POST['filename'];
    
    //check what is wanted : regular file or DbXml file ?
    $pathparts = explode("/", $folder);
    if($pathparts[0]=="dbxml"){
      $containerName = $pathparts[1];
      if(substr($filename,-4) == ".xml"){
        $fileOID = substr($filename,0,-4);
      }else{
        $fileOID = $filename;
      }
      $created_filename = $this->m_ctrl->boeditor()->createDbxmlFile($containerName, $fileOID);
    }else{
      $created_filename = $this->m_ctrl->boeditor()->createFile($root,$folder,$filename);
    }
    
    echo json_encode($created_filename);
  }

 /*
 *@desc ajax, delete a file
 *@return filename on success
 *@author TPI
 */
  public function deleteFile(){
    
    $file = $_POST['file'];
    
    //check what is wanted : regular file or DbXml file ?
    $pathparts = explode("/", $file);
    if($pathparts[count($pathparts)-3]=="dbxml"){
      $containerName = $pathparts[count($pathparts)-2];
      $fileOID = substr($pathparts[count($pathparts)-1], 0, -4); //".xml"
      $deleted_filename = $this->m_ctrl->boeditor()->deleteDbxmlFile($containerName, $fileOID);
    }else{
      $deleted_filename = $this->m_ctrl->boeditor()->deleteFile($file);
    }
    
    echo json_encode($deleted_filename);
  }

 /*
 *@desc ajax, rename a file
 *@return new filename
 *@author TPI
 */
  public function renameFile(){
    
    $file = $_POST['file'];
    $newName = $_POST['newName'];
    
    //check what is wanted : regular file or DbXml file ?
    $pathparts = explode("/", $file);
    if($pathparts[count($pathparts)-3]=="dbxml"){
      $containerName = $pathparts[count($pathparts)-2];
      $fileOID = substr($pathparts[count($pathparts)-1], 0, -4); //".xml"
      if(substr($newName,-4) == ".xml"){
        $newFileOID = substr($newName,0,-4);
      }else{
        $newFileOID = $newName;
      }
      if($fileOID != $newFileOID){
        $newName = $this->m_ctrl->boeditor()->renameDbxmlFile($containerName, $fileOID, $newFileOID);
      }else{
        $newName = $newFileOID .".xml";
      }
    }else{
      $newName = $this->m_ctrl->boeditor()->renameFile($file, $newName);
    }
    
    echo json_encode($newName);
  }

 /*
 *@desc ajax, return an html tree to select a folder
 *@return new filename
 *@author TPI
 */
  public function getSelectableFolderTree(){
    
    $root = $_POST['root'];
    
    $html = $this->m_ctrl->boeditor()->getSelectableFolderTree($root);
    
    echo json_encode($html);
  }
}
