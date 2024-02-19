<?php

use App\Modele\Modele_Entreprise;
use App\Modele\Modele_Salarie;
use App\Modele\Modele_Utilisateur;
use App\Vue\Vue_Connexion_Formulaire_client;
use App\Vue\Vue_Mail_Confirme;
use App\Vue\Vue_Mail_ReinitMdp;
use App\Vue\Vue_Menu_Administration;
use App\Vue\Vue_Structure_BasDePage;
use App\Vue\Vue_Structure_Entete;
use App\Vue\Vue_ConsentementRGPD;
use App\Vue\Vue_Utilisateur_Reset_MDP;
use App\Utilitaire\EmailSender;
use App\Fonctions;

$Vue->setEntete(new Vue_Structure_Entete());

switch ($action) {
    case "reinitmdpconfirm":

        if(isset($_POST['email'])){
            $email = $_POST['email'];
            $utilisateur = Modele_Utilisateur::Utilisateur_Select_ParLogin($email);

            if($utilisateur){
                $logger->info('Demande de réinitiation de mot de passe', ['email'  => $email]);
                $id_utilisateur = $utilisateur['idUtilisateur'];
                $generateMdp = Fonctions\generateMdp(20);
    
                Modele_Utilisateur::Utilisateur_Reset_motDePasse($id_utilisateur, $generateMdp);
    
                $recover = EmailSender::sendEmail($email, 'Demande de réinitialisation de mot de passe',
                    'Votre nouveau mot de passe est : '.$generateMdp
                );

                if($recover){
                    $Vue->addToCorps(new Vue_Mail_Confirme());
                }
            }else{
                $logger->info('Demande de réinitiation de mot de passe par un mail introuvable', ['email'  => $email]);
                echo"erreur";
            }
        }

        break;
    case "reinitmdp":

        // $Vue->addToCorps(new Vue_Utilisateur_Reset_MDP());
        $Vue->addToCorps(new Vue_Mail_ReinitMdp());

        break;

    case "forceChangeMdp": // No old password required
        $Utilisateur = Modele_Utilisateur::Utilisateur_Select_ParId($_REQUEST["idUtilisateur"]);
        Modele_Utilisateur::Utilisateur_Modifier_motDePasse($_REQUEST["idUtilisateur"], $_POST['']);
        $Vue->setMenu(new Vue_Menu_Administration());
        $Vue->addToCorps(new Vue_Utilisateur_Reset_MDP('Votre mot de passe a été changé'));
        break;

    case "Se connecter" :

        if (isset($_REQUEST["compte"]) and isset($_REQUEST["password"])) {
            //Si tous les paramètres du formulaire sont bons

            $utilisateur = Modele_Utilisateur::Utilisateur_Select_ParLogin($_REQUEST["compte"]);

            if ($utilisateur != null) {
                if ($utilisateur["desactiver"] == 0) {
                        if ($_REQUEST["password"] == $utilisateur["motDePasse"]) {

                            $_SESSION["idUtilisateur"] = $utilisateur["idUtilisateur"];
                            $_SESSION["idCategorie_utilisateur"] = $utilisateur["idCategorie_utilisateur"];

                            $logger->info('Tentative de connexion réussi', ['id_user'  => $_SESSION["idUtilisateur"]]);

                            // ------ Vérifier si c'est un mot de passe temporaire
                            if(isset($utilisateur['mdp_reset']) && $utilisateur['mdp_reset'] == 1){
                                $Vue->addToCorps(new Vue_Utilisateur_Reset_MDP());
                            }else{
                                if(in_array($utilisateur['idCategorie_utilisateur'], [2,4]) && !$utilisateur['aAccepteRGPD']){
                                    switch ($utilisateur["idCategorie_utilisateur"]) {
                                        case 2:
                                            $_SESSION["typeConnexionBack"] = "utilisateurCafe";
                                            break;
                                        case 4:
                                            $_SESSION["typeConnexionBack"] = "salarieEntrepriseCliente";
                                            $_SESSION["idSalarie"] = $utilisateur["idUtilisateur"];
                                            $_SESSION["idEntreprise"] = Modele_Salarie::Salarie_Select_byId($_SESSION["idUtilisateur"])["idEntreprise"];
                                            break;    
                                    }
                                    $Vue->addToCorps(new Vue_ConsentementRGPD());
                                }else{
                                    switch ($utilisateur["idCategorie_utilisateur"]) {
                                        case 1:
                                            $_SESSION["typeConnexionBack"] = "administrateurLogiciel"; //Champ inutile, mais bien pour voir ce qu'il se passe avec des étudiants !
                                            $Vue->setMenu(new Vue_Menu_Administration());
                                            break;
                                        case 2:
                                            $_SESSION["typeConnexionBack"] = "utilisateurCafe";
                                            $Vue->setMenu(new Vue_Menu_Administration());
                                            break;
                                        case 3:
                                            $_SESSION["typeConnexionBack"] = "entrepriseCliente";
                                            //error_log("idUtilisateur : " . $_SESSION["idUtilisateur"]);
                                            $_SESSION["idEntreprise"] = Modele_Entreprise::Entreprise_Select_Par_IdUtilisateur($_SESSION["idUtilisateur"])["idEntreprise"];
                                            include "./Controleur/Controleur_Gerer_Entreprise.php";
                                            break;
                                        case 4:
                                            $_SESSION["typeConnexionBack"] = "salarieEntrepriseCliente";
                                            $_SESSION["idSalarie"] = $utilisateur["idUtilisateur"];
                                            $_SESSION["idEntreprise"] = Modele_Salarie::Salarie_Select_byId($_SESSION["idUtilisateur"])["idEntreprise"];
                                            include "./Controleur/Controleur_Catalogue_client.php";
                                            break;
                                    }
                                }
                            }
                            // ------
                        } else {//mot de passe pas bon
                            $logger->info('Tentative de connexion echoué', ['utilisateur'  => $_REQUEST["compte"]]);

                            $msgError = "Mot de passe erroné";
    
                            $Vue->addToCorps(new Vue_Connexion_Formulaire_client($msgError));
                        }
                } else {
                    $logger->warning('Tentative de connexion compte désactivé' .' '. $_SERVER['PHP_SELF']);
                    $msgError = "Compte désactivé";

                    $Vue->addToCorps(new Vue_Connexion_Formulaire_client($msgError));

                }
            } else {
                $logger->warning('Tentative de connexion avec un utilisateur inexistant', ['utilisateur'  => $_REQUEST["compte"]]);

                $msgError = "Identification invalide";

                $Vue->addToCorps(new Vue_Connexion_Formulaire_client($msgError));
            }
        } else {
            $logger->warning('Tentative de connexion avec des champs vide', ['utilisateur'  => $_REQUEST["compte"]]);

            $msgError = "Identification incomplete";

            $Vue->addToCorps(new Vue_Connexion_Formulaire_client($msgError));
        }
    break;
    default:

        $Vue->addToCorps(new Vue_Connexion_Formulaire_client());

        break;
}


$Vue->setBasDePage(new Vue_Structure_BasDePage());