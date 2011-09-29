<?php
    /**************************************************************************\
    * ALIX EDC SOLUTIONS                                                       *
    * Copyright 2011 Business & Decision Life Sciences                         *
    * http://www.alix-edc.com                                                  *
    * ------------------------------------------------------------------------ *                                                                       *
    * This file is part of ALIX.                                               *
    *                                                                          *
    * ALIX is free software: you can redistribute it and/or modify             *
    * it under the terms of the
    *      GNU General Public License as published by     *
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
/**
* @desc Classe de base abstraite contenant les m�thodes communes � toutes les classes
* Features :
*   => Affichage de variable sous la forme print_r version html ou version texte pour insertion dans log par ex
*   => addLog : Fonction de log...
*   => gestion des hooks 
* @author WLT
**/ 

define("TRACE",1);
define("INFO",2);
define("WARN",3);
define("ERROR",4);
define("FATAL",5);

class CommonFunctions{

/****************************************************/
//Variables membres (priv�es)
/****************************************************/

  //Array des param�tres de config
  public $m_tblConfig;

  //R�f�rence vers le controleur (class uietude)
  //Unique point d'acc�s aux instances des classes boXXXXX et uiXXXXX
  public $m_ctrl;
  
/****************************************************/
//M�thodes Publiques
/****************************************************/

  //Constructeur
  function __construct($tblConfig=array(),$ctrlRef)
  {
    $this->m_tblConfig = $tblConfig;
    if(isset($GLOBALS['egw_info']['user']['userid'])){
      $userId = $GLOBALS['egw_info']['user']['userid'];
    }else{
      $userId = "CLI";
    }
    $this->m_user = $userId;
    $this->m_ctrl = $ctrlRef;
    
    if (defined('EGW_INCLUDE_ROOT')) {
      require_once(dirname(__FILE__). "/../custom/".$this->m_tblConfig['METADATAVERSION']."/inc/hookFunctions.php"); 
    }
  }

  //Destructeur
  function __destruct()
  {
    //RAF   
  }
 
/****************************************************/
//M�thodes Priv�s
/****************************************************/
  
  protected function egwId2studyId($id)
  {
    $egwSiteNumber = abs($id);
    return sprintf("%04s",$egwSiteNumber);  
  }

  protected function studyId2egwId($id)
  {
    $egwId = "-".($id);
    return $egwId;   
  }
  
  public function addLog($message,$level){
    if($this->m_tblConfig['LOG_FILE']==""){
      $this->dumpPre(debug_backtrace());
    }
    if($level>=$this->m_tblConfig['LOG_LEVEL']){
      $timeOffset = microtime(true) - $_SERVER['REQUEST_TIME']; 
      $dt = date('c') . " " . substr($timeOffset,0,7);
      error_log("$dt " . $message . "\n",3,$this->m_tblConfig['LOG_FILE']);
      if($level>=ERROR){
        mail($this->m_tblConfig['EMAIL_ERROR'],"ETUDE (".$_SERVER['SERVER_NAME'].") ERROR/FATAL : {$this->m_user}@$dt",$message);
        if($level==FATAL){
          die("<pre>$message</pre>");
        }
      }
      if($message=="socdiscoo::destruct()" && $this->m_tblConfig['LOG_LONG_EXECUTION']){
        if($timeOffset>$this->m_tblConfig['LONG_EXECUTION_VALUE']){
          mail($this->m_tblConfig['EMAIL_ERROR'],"ETUDE (".$this->m_tblConfig['APP_NAME'].") LONG EXECUTION : {$this->m_user}@$dt","execution time = ".substr($timeOffset,0,7) . "s @{$this->m_user}@$dt");
        }
      }
    }
  }

  /**
  *@desc Retourne la string currentapp utilis� dans la base de donn�es egroupware pour diff�rencier les diff�rentes instances du module de CRF
  *      Si le mode test est activ�, un suffixe peut �tre ajout� en fonction du param�tre $bIncludeTestModeSuffix
  *@param boolean $bIncludeTestModeSuffix ajouter le suffixe d'indication du mode de test si le mode de test est actif
  *@return string             
  **/  
  public function getCurrentApp($bIncludeTestModeSuffix){
    
    $currentApp = $GLOBALS['egw_info']['flags']['currentapp'];

    if($bIncludeTestModeSuffix){
      if($_SESSION[$currentApp]['testmode']){
        $currentApp .= "_test";
      }
    }

    return $currentApp;
  }

  /**
  * @desc Tente d'appeler le hook demand�, si celui ci a �t� d�clar�
  * @param string $methodName nom de la methode appelante
  * @param string $hookName nom du hook
  * @param array tableau de param�tre pass� au hook     
  * @return valeur de retour du hook
  * @author WLT
  **/   
  protected function callHook($methodName,$hookName,$tblParam){
    $functionName = get_class($this)."_".$methodName."_".$hookName;
    if(function_exists($functionName)){
      $this->addLog("CommonFunctions->callHook() : functionName=$functionName",INFO);
      return call_user_func_array($functionName,$tblParam);       
    }else{
      return false;
    }
 
  }
  
  public function dumpPre($mixed = null)
  {
    echo '<pre>';
    var_dump($mixed);
    echo '</pre>';
    return null;
  }
  
  public function dumpRet($mixed = null)
  {
    ob_start();
    var_dump($mixed);
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }
}

?>
