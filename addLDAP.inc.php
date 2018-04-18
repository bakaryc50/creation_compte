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
	 $admin_system_dn = "sn=".$entree['cn'].",dc=moov,dc=bj";
	 //connexion au server en anonyme
	$ldapconn = ldap_connect($serveur,$ldapPort,$_SESSION['user']['userpassword']);
	if(!$ldapconn){ return -1;}
	 // recherche des param de l'utilisateur connecté
	if ($ldapconn){
		//configuration des options		
			ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3); // on passe le LDAP en version 3, necessaire pour travailler avec le AD
			
		 $entree_dn = "sn=".$entree['sn'].",dc=moov,dc=bj";
		 if (@ldap_add($ldapconn, $entree_dn, $entree)) {
		 	return 1;
		 } else {
		 	return -1;
		 }		
 	
	}
	ldap_close($ldapconn);
}
?>