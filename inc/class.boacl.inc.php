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

define("ODM_NAMESPACE","http://www.cdisc.org/ns/odm/v1.3");

/*
@desc classe de gestion des droits utilisateurs
@author wlt
*/
class boacl extends CommonFunctions
{
  //Variables mise en cache

  //Constructeur
  function boacl($tblConfig,$ctrlRef)
  {
      CommonFunctions::__construct($tblConfig,$ctrlRef);
  }

/**************************************************** Accesseurs User/Profile ****************************************************/

/*
@desc teste si l'utilisateur prossède un profile du profile spécifié (INV, CRA, DM, etc) (ou des profiles spécifiés (tableau) : retourne true au premier profile rencontré)
@params le profileId à tester (ou un tableau de profileId, ou une liste de profiles séparés par une virgule), userId et siteId optionnels
@return boolean
@author tpi
*/
  public function existUserProfileId($profileIds, $userId="", $siteId=""){
    $userProfiles = $this->getUserProfiles($userId,$siteId);
    if(!is_array($profileIds)){
      //$profileIds = array($profileIds);
      $profileIds = explode(",",$profileIds);
    }
    foreach($profileIds as $profileId){
      foreach($userProfiles as $userProfile){
        if($userProfile['profileId'] == $profileId){
          return true;
        }
      }
    }
    return false;
  }
  
/*
@desc retourne le login, identifiant de connexion, de l'utilisateur connecté
@return string login
@author tpi
*/
  public function getUserId(){
    return $GLOBALS['egw']->accounts->data['account_lid'];   
  }

/*
@desc retourne les infos de l'utilisateur connecté
@return array(login,fullname,lastlogin)
@author wlt
*/
  public function getUserInfo(){
    $tblRet = array();
    $tblRet['login'] = $this->getUserId();
    $tblRet['fullname'] = $GLOBALS['egw']->accounts->data['account_fullname'];
    $tblRet['lastlogin'] = $GLOBALS['egw']->accounts->data['account_lastlogin'];
    return $tblRet;   
  }

/*
@desc retourne le profileId par défaut de l'utlisateur spécifié (utilisateur connecté si non spécifié) dans le site spécifié (profile par défaut si le siteId n'est pas précisé)
@return string profileId (ARC, INV, DM)
@author tpi
*/ 
  public function getUserProfileId($userId="", $siteId=""){
    $userProfile = $this->getUserProfile($userId,$siteId);
    return $userProfile['profileId'];
  }
  
/*
@desc retourne le profile de l'utlisateur spécifié (utilisateur connecté si non spécifié) dans le site spécifié (profile par défaut si le siteId n'est pas précisé)
@return array(siteId,sitename,siteCountry,profileId,defaultProfile)
@author tpi
*/ 
  public function getUserProfile($userId="", $siteId=""){
    $bDefault = false;
    if($siteId=="") $bDefault = true;
    $userProfiles = $this->getUserProfiles($userId,$siteId,$bDefault);
    return $userProfiles[0]; //normalement une seule ligne dans le tableau ! soit c'est le profil par défaut (un seul autorisé en base), soit c'est le profile sur le siteId spécifié (un seul autorisé en base)
  }
  
/*
@desc retourne la liste des profiles de l'utlisateur spécifié (utilisateur connecté si non spécifié)
@return array(array(siteId,sitename,siteCountry,profileId,defaultProfile))
@author wlt
*/ 
  public function getUserProfiles($userId="",$siteId="",$bDefault=false){
    $tblRet = array();
    
    if($userId==""){
      $userId = $this->getUserId();
    }
    
    //Recuperation de la liste des centres de l'utilisateur
    $sql = "SELECT egw_alix_acl.SITEID,PROFILEID,SITENAME,COUNTRY,CHECKONSAVE,DEFAULTPROFILE
            FROM egw_alix_acl,egw_alix_sites
            WHERE USERID='".$userId."' AND 
                  egw_alix_acl.SITEID=egw_alix_sites.SITEID AND
                  egw_alix_acl.CURRENTAPP=egw_alix_sites.CURRENTAPP AND
                  egw_alix_acl.CURRENTAPP='".$this->getCurrentApp(false)."'";
    
    if($siteId!=""){
      $sql .= " AND egw_alix_acl.SITEID='".$siteId."'";
    }
    
    if($bDefault){
      $sql .= " AND DEFAULTPROFILE='Y'";
    }

    $GLOBALS['egw']->db->query($sql); 
    while($GLOBALS['egw']->db->next_record()){
      $tblRet[] = array('siteId'=>$GLOBALS['egw']->db->f('SITEID'),
                        'siteName'=>$GLOBALS['egw']->db->f('SITENAME'),
                        'siteCountry'=>$GLOBALS['egw']->db->f('COUNTRY'),
                        'checkOnSave'=>$GLOBALS['egw']->db->f('CHECKONSAVE'),
                        'profileId'=>$GLOBALS['egw']->db->f('PROFILEID'),
                        'defaultProfile'=>$GLOBALS['egw']->db->f('DEFAULTPROFILE'),
                        );
    } 
    
    return $tblRet;
  }
  
  
/**************************************************** Accesseurs Site ****************************************************/

/*
@desc retourne la liste de tous les centres (de l'application en cours)
@return array(siteId,sitename)
@author wlt
*/ 
  public function getSites(){
    $tblRet = array();
    //Recuperation de la liste des centres
    $sql = "SELECT SITEID,SITENAME,COUNTRY,CHECKONSAVE
            FROM egw_alix_sites
            WHERE CURRENTAPP='".$this->getCurrentApp(false)."'
            ORDER BY SITEID";
    
    $GLOBALS['egw']->db->query($sql); 
    while($GLOBALS['egw']->db->next_record()){
      $siteId = (string)$GLOBALS['egw']->db->f('SITEID');
      $tblRet["site$siteId"] = array('siteId'=>$siteId,
                        'siteName'=>$GLOBALS['egw']->db->f('SITENAME'),
                        'siteCountry'=>$GLOBALS['egw']->db->f('COUNTRY'),
                        'checkOnSave'=>$GLOBALS['egw']->db->f('CHECKONSAVE'));
    }
    return $tblRet;
  }


/**************************************************** Modificateurs Profile ****************************************************/

/*
@desc ajoute un profile utilisateur à la base ACL
@author wlt
*/
  public function addProfile($userId,$siteId,$profileId,$isDefault=false){
    $default = "";
    if($isDefault){
      $default = "Y";
    }
    $sql = "REPLACE INTO egw_alix_acl(CURRENTAPP,SITEID,USERID,PROFILEID,DEFAULTPROFILE) 
          VALUES('".$this->getCurrentApp(false)."','$siteId','$userId','$profileId','$default');";
    $GLOBALS['egw']->db->query($sql); 
  }
  

/**************************************************** Modificateurs Site ****************************************************/
  
  public function addSite($siteId,$siteName,$siteCountry,$checkOnSave){
    $sql = "INSERT INTO egw_alix_sites(CURRENTAPP,SITEID,SITENAME,COUNTRY,CHECKONSAVE) 
          VALUES('".$this->getCurrentApp(false)."','$siteId','$siteName','$siteCountry','$checkOnSave');";
    $GLOBALS['egw']->db->query($sql); 
  }

}
