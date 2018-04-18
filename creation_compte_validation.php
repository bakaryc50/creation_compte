<?php session_start();
require_once('fonction.php');
extract($_POST);

require_once('connexion.php');
require_once('function2mail.php');
	
// fin recherche de l'adresse mail de l'expéditeur de la demande en cours de traitement

$msgM = "<html>
		<head></head>
			<body>
				<b>Bonjour, </b><br /> 
				Une demande de creation de compte est en cours <br />
				Pour voir le contenu de la demande veuillez cliquer sur le lien suivant :<br /> 
				<a href='http://ben-workflow:81/creation_compte/index.php' target='_blank'>voir la demande</a> 
				puis entrez vos parametres de connexion<br /><br />
				Ce mail est un mail automatique envoye par le systeme de demande de creation de compte.
			</body>
	   </html>";					
// cas 1 CDGAP

	if(strcmp($_SESSION['logged']['titre'], "CDGAP")==0){
		
		try { 	$sql= " UPDATE  compte_param SET en_coursDA ='non'   
						WHERE id_creationCpte = $id";
				$req = $connexion->exec($sql);
			} catch (PDOException $e) {
				$msg = "Erreur donnee";
				echo $msg;
				exit;
			}
		$mailExpediteur = $_SESSION['acteur']['CDGAP'].'@moov.bj';	
		$mailDestinataire = $_SESSION['acteur']['helpdesk'].'@moov.bj';				
		$retour = @envoieMail($mailExpediteur, $mailDestinataire, $msgM);//envoie de mail au helpdeskdsi

		if ($retour){ $res=array("retour"=>"1");} else { $res=array("retour"=>"2");}
	} 
// cas 2 helpdeskdsi
	if (strcmp($_SESSION['logged']['titre'], "helpdesk")==0){
			try { 	$sql=" UPDATE  compte_param SET en_coursHelpdesk ='non'   
							WHERE id_creationCpte = $id ";
					$req = $connexion->exec($sql);
				} catch (PDOException $e) {
					$msg = "Erreur Validation";
					echo $msg;
					exit;
				}	
		$mailExpediteur = $_SESSION['acteur']['helpdesk'].'@moov.bj';		
		$mailDestinataire = $_SESSION['acteur']['admin_system'].'@moov.bj';		
		$retour = @envoieMail($mailExpediteur, $mailDestinataire, $msgM);//envoie de mail au helpdeskdsi
		if ($retour){ $res=array("retour"=>"1");} else { $res=array("retour"=>"2");}
	} 
// cas 2 admin_system
	if (strcmp($_SESSION['logged']['titre'], "admin_system")==0) {
		// on recherche d'abord l'utilisateur qui a initié la demande de creation de compte
		// recherche de l'adresse mail de l'expéditeur de la demande en cours de traitement
			try { 	$sql0=" SELECT  mailnickname, tab1_departement, tab1_nom, tab1_prenom, tab1_fonction, tab1_contact, 
							FROM compte_param 
							WHERE id_creationCpte = $id
							";
							$req0 = $connexion->query($sql0);
						} catch (PDOException $e) {
							$msg = "Erreur donnee";
							echo $msg;
							exit;
						}
			

			$nb_lign0= $req0->rowCount();
			$result = ""; 

			if($nb_lign0>0){
				while ($donnee = $req0 -> fetch()) {
					$baseMail = $donnee['mailnickname']; // cool,base du mail récupéré
					$entree['sn']=$donnee['tab1_nom'];
					$entree['givenname']=$donnee['tab1_prenom'];
					$entree['cn']=$donnee['tab1_prenom']." ".$donnee['tab1_nom'];
					$entree['mailnickname']=$donnee['mailnickname'].'@moov.bj';
					$entree['tel']="(00229) ".$donnee['tab1_contact'];
					$entree['office']=$donnee['tab1_departement'];
					$entree['title']=$donnee['tab1_fonction'];
					$entree['userpassword']="123456789";
					$entree['mustchpwd']='yes';
				}

				 
			}
		//fin recherche de l'adresse mail de l'expéditeur de la demande en cours de traitement

		// creation du compte dans AD
			require_once('addLDAP.inc.php');
			$crea = @ajoutLDAP();
			
		// fin creation du compte dans AD

		//MAJ de l'état de la demande chez l'admin system
			try { 	$sql=" UPDATE  compte_param SET en_coursAdminSystem ='non'   
							WHERE id_creationCpte = $id ";
					$req = $connexion->exec($sql);
				} catch (PDOException $e) {
					$msg = "Erreur Validation";
					echo $msg;
					exit;
				}
		//fin MAJ de l'état de la demande chez l'admin system
		// envoie de mail de notification
		$msgM = "<html>
		<head></head>
			<body>
				<b>Bonjour, </b><br /> 
				Nous vous confirmons par cette note que votre demande de creation de compte <br />
				a ete pris en compte. Le compte a ete cree suivant les informations fournies<br />
				Ce mail est un mail automatique envoye par le systeme de demande de creation de compte.
			</body>
	   </html>";
		$mailExpediteur = $_SESSION['acteur']['admin_system'].'@moov.bj';
		$mailDestinataire  = $baseMail.'@moov.bj';
				
				
		$retour = @envoieMail($mailExpediteur, $mailDestinataire, $msgM);//envoie de mail de confirmation de creation de compte
		if ($retour){ $res=array("retour"=>"1");} else { $res=array("retour"=>"2");}
		if($crea==1){ $res=array("retour"=>"3");
			$infoCpt = "sn=".$entree['sn']." <br/> givenname=".$entree['givenname']." 
						<br/> cn=".$entree['cn']." <br/> mailnickname=".$entree['mailnickname']." <br/> tel=".$entree['tel']."  
						<br/> office=".$entree['office']." <br/> title=".$entree['title']." <br/> userpassword=".$entree['userpassword']." 
						<br/> mustchpwd=".$entree['mustchpwd']."<br/><br/> Appuyez sur la touche F5 pour rafraichir la page";
			$res["infocpt"] = $infoCpt;
		}
	} 	
echo json_encode($res);
?>