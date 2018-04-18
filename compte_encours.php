<?php session_start();
require_once('fonction.php');
controlSession();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<title>etisalat Benin S.A | Validation </title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/tablecss.css" />
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="css/bootstrap-dialog.min.css" />
	<link rel="stylesheet" type="text/css" href="css/modal_complement.css" />
	<link rel="stylesheet" type="text/css" href="css/checkbox_green.css" />
	<link rel="stylesheet" type="text/css" href="css/jquery-ui.structure.min.css" />
	<link rel="stylesheet" type="text/css" href="css/jquery-ui.theme.min.css" />
	<link rel="stylesheet" type="text/css" href="css/style_menu.css" />
	<link rel="stylesheet" href="css/css_loader.css" type="text/css" />
<style>	
.survol{ background-color: #76A231;  color: #76A231; font-weight:bold;}
</style>	
</head>		
<body>
	<div id="menu">
		<?php require_once('menu.inc.php');?>
		<a id="topbarcontenu" href="deconnect.php"><img  src="images/deconnexion.png" title="Déconnexion" alt="deconnexion"  /> Déconnexion</a>
	</div>
	<div id="infoConnecte">
		 <p>Connecté(é) : <span style="color:#c0ce39;"><?php echo $_SESSION['user']['cn']; ?></span></p>
	</div>
	<!---->
	<div style="margin-top: 100px;">
		<form id="formEncoursCompte" method="post" action="#">			
			<table id="EncoursCompte" class="gridcde">
				<thead>
					<tr>
						<th colspan="6" style="text-align: center;">Demande en attente de validation</th>
					</tr>
					<tr>
						<th>Demandeur</th>
						<th>Titre</th>
						<th>Date d'envoie</th>
						<th>Consulter</th>
						<th>Valider</th>
						<th>Rejeter</th>
					</tr>
				</thead>
				<tbody>
					<?php 
							
							if(isset($_SESSION['user']['mailnickname'])){
								require_once('connexion.php');

								// cas 1 CDGAP
									if(strcmp($_SESSION['user']['mailnickname'], $_SESSION['acteur']['CDGAP'])==0){
										try { 	$sql=" SELECT  id_creationCpte, tab1_datejour, cn, title, mailnickname, en_coursDA FROM compte_param WHERE en_coursDA='oui'";
												$req = $connexion->query($sql);
											} catch (PDOException $e) {
												$msg = "Erreur donnee";
												echo $msg;
												exit;
											}
									} 
								// cas 2 helpdeskdsi
									if(strcmp($_SESSION['user']['mailnickname'], $_SESSION['acteur']['helpdesk'])==0){
											try { 	$sql=" SELECT id_creationCpte, tab1_datejour, cn, title, en_coursDA, mailnickname, en_coursHelpdesk 
															FROM compte_param 
															WHERE en_coursDA='non'
															AND en_coursHelpdesk='oui'";
													$req = $connexion->query($sql);
												} catch (PDOException $e) {
													$msg = "Erreur donnee";
													echo $msg;
													exit;
												}
									}
									// cas 3 administrateur systeme
									if(strcmp($_SESSION['user']['mailnickname'], $_SESSION['acteur']['admin_system'])==0){
											try { 	$sql=" SELECT id_creationCpte, tab1_datejour, cn, title, mailnickname,  en_coursDA, en_coursHelpdesk 
															FROM compte_param 
															WHERE en_coursDA='non'
															AND en_coursHelpdesk='non'
															AND en_coursAdminSystem='oui'
															";
													$req = $connexion->query($sql);
												} catch (PDOException $e) {
													$msg = "Erreur donnee";
													echo $msg;
													exit;
												}
									}

									$nb_lign= $req->rowCount();
									$result = ""; 

									if($nb_lign>0){
										while($donnee = $req -> fetch()){
										 $tab1_datejour = decodedateformatMysql($donnee['tab1_datejour']);
										$result .= "<tr>";										
										$result .= "<td>".$donnee['cn']."</td>";									
										$result .= "<td>".$donnee['title']."</td>";								
										$result .= "<td>".$tab1_datejour."</td>";							
										$result .= "<td><a href='creation_cptePDF.php?id=".$donnee['id_creationCpte']."' target='_blank' title='Consulter la demande' ><img src='images/pdf.png' width='24px' height='24px' alt='Consulter'/></a></td>";
										$result .= "<td><a id='".$donnee['id_creationCpte']."' class='autoriser' href='#' ><img src='images/icn_add_user.png' width='24px' height='24px' title='Valider la demande' alt='Valider'/></a></td>";
										$result .= "<td><a id='".$donnee['id_creationCpte']."' data-src='".$donnee['mailnickname']."' class='rejeter' href='#' title='Rejeter la demande' ><img src='images/rejet.png' alt='Rejeter'/></a></td>";																				
										$result .= "</tr>";										
										}
									echo  $result;
									} else{
									$result .= "<tr>";	
									$result .= "<td colspan='6' style='text-align: center;'> 0 demande en cours</td>";	
									$result .= "<tr>";	
									echo  $result;
									}
							}
							
							?>
				</tbody>				
			</table>
		</form>
	</div>
	<!---->
	 <br/> <br/>
	 <pre id="zmsg" class='xdebug-var-dump'>
	 	<i></i>
	 </pre>
	<!---->
	<div id="loading-div-background">
    <div id="loading-div" class="ui-corner-all" >
      <img src="images/loader.gif" alt="Chargement..."/>
      <h4 style="color:gray;font-weight:normal;">Traitement en cours, Patientez....</h4>
     </div>
	</div>
	
<script type="text/javaScript" src="js/jquery.min.js"></script>
<script type="text/javaScript" src="js/jquery-ui.min.js"></script>
<script type="text/javaScript" src="js/bootstrap.min.js"></script>
<script type="text/javaScript" src="js/bootstrap-dialog.min.js"></script>
<script>
$(document).ready( function() {
	$("#loading-div-background").hide();
	$('#zmsg').hide();
	// hover sur les lignes
	$('#EncoursCompte tbody tr').hover( function() {
			$(this).addClass("survol");
		}, function() {
			$(this).removeClass("survol");
	});
	// traitement de l'autorisation d'une demande de creation de compte
	$('a[class=autoriser]').click( function() {
			$("#loading-div-background").css({ opacity: 0.8 });
			$("#loading-div-background").show();
	
			var id = $(this).attr('id');			
			$.post( 'creation_compte_validation.php',
					{"id": id},
				function(data){
					// console.log(data); return false;
					if(data.retour=="1"){
						$("#loading-div-background").hide();
						BootstrapDialog.show({
								title: 'Information',
								type:  BootstrapDialog.TYPE_SUCCESS, 
								message: 'Félicitation, opération réussie !',
								buttons:[ {label: 'OK',
										  action: function(dialogItself){
										  dialogItself.close();}
								}]
						});
					 setTimeout("", 5000);
					window.location = window.location.pathname;
					} 

					if(data.retour=="2") {
							$("#loading-div-background").hide();
							BootstrapDialog.show({
								title: 'Information',
								type:  BootstrapDialog.TYPE_SUCCESS, 
								message: 'opération réussie avec quelques problèmes rencontrés!',
								buttons:[ {label: 'OK',
										  action: function(dialogItself){
										  dialogItself.close();}
								}]
							});
						setTimeout("", 5000);
						window.location = window.location.pathname;
					}

					if(data.retour=="3") {
							$("#loading-div-background").hide();							
							$('#zmsg i').append(data.infocpt).show();
						
					}
				},"json"	
			);
			return false;
	});
//traitement du rejet d'une demande	
$('a[class=rejeter]').click( function() {
			//$("#loading-div-background").css({ opacity: 0.8 });
			//$("#loading-div-background").show();
	
			var id = $(this).attr('id');			
			var mailnickname = $(this).attr('data-src');
			 
			$.post( 'rejet.php',
					{"id": id, "mailnickname": mailnickname},
				function(data){
					// console.log(data); return false;
					if(data=="1"){
						$("#loading-div-background").hide();
						BootstrapDialog.show({
								title: 'Information',
								type:  BootstrapDialog.TYPE_SUCCESS, 
								message: 'Rejet effectue avec succes !',
								buttons:[ {label: 'OK',
										  action: function(dialogItself){
										  dialogItself.close();}
								}]
						});
					 setTimeout("", 5000);
					window.location = window.location.pathname;
					} else{
							$("#loading-div-background").hide();
							BootstrapDialog.show({
								title: 'Information',
								type:  BootstrapDialog.TYPE_SUCCESS, 
								message: 'Echec, Rejet non effectue!',
								buttons:[ {label: 'OK',
										  action: function(dialogItself){
										  dialogItself.close();}
								}]
							});
						setTimeout("", 5000);
						window.location = window.location.pathname;
					}
				}	
			);
			return false;
			
	});
});
</script>
</body>
</html>