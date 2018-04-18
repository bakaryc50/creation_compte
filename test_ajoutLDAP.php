<?php
/**
**@description cette fonction fait une ajout a AD 
**@return boolean 
**/
function ajoutLDAP(){ 
	 $s=array();
	 global $entree;
	 $serveur='10.16.17.70';
	 $ldapPort = 389;
	 $admin_system_dn = "sn='José AHOUANDJINOU',dc=moov,dc=bj";
	 //connexion au server en anonyme
	$ldapconn = ldap_connect($serveur,$ldapPort,'mot de passe');
	if(!$ldapconn){ return -1;}
	 // recherche des param de l'utilisateur connecté
	if ($ldapconn){
		//configuration des options		
			ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3); // on passe le LDAP en version 3, necessaire pour travailler avec le AD
			
		 $entree_dn = "sn='toto',dc=moov,dc=bj";
		 if (@ldap_add($ldapconn, $entree_dn, $entree)) {
		 	return 1;
		 } else {
		 	return -1;
		 }
			
			
 	
	}
	ldap_close($ldapconn);
return 1;
}
$entree = array();
$entree['sn']="toto";
$entree['givenname']="lolo";
$entree['cn']="toto lolo";
$entree['mail']='toto@moov.bj';
$entree['tel']="(00229) XXXXXXXXX";
$entree['office']="DSI";
$entree['title']="test";
$entree['userpassword']="12345";
$entree['mustchpwd']='yes';
var_dump($entree); exit;
$res= @ajoutLDAP();
if($res==1){ echo "Utilisateur cree ds AD avec succes avec les parametre suivant : <br/><br/>".var_dump($entree);}
else {echo "utilisateur non crée ";}

?>