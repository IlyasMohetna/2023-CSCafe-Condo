<?php


use App\Modele\Modele_Utilisateur;
use App\Vue\Vue_Compte_Administration_Gerer;
use App\Vue\Vue_Connexion_Formulaire_client;
use App\Vue\Vue_Menu_Administration;
use App\Vue\Vue_Structure_BasDePage;
use App\Vue\Vue_Structure_Entete;
use App\Vue\Vue_Utilisateur_Changement_MDP;
use App\Fonctions;


switch ($action) {
    case "changerMDP":
        //Il a cliqué sur changer Mot de passe. Cas pas fini
        $Vue->setEntete(new Vue_Structure_Entete());
        $Vue->setMenu(new Vue_Menu_Administration());
        $Vue->addToCorps(new Vue_Utilisateur_Changement_MDP("", "Gerer_monCompte"));
        break;
    case "submitModifMDP":
        //il faut récuperer le mdp en BDD et vérifier qu'ils sont identiques
        $utilisateur = Modele_Utilisateur::Utilisateur_Select_ParId($_SESSION["idUtilisateur"]);
        if ($_REQUEST["AncienPassword"] == $utilisateur["motDePasse"])
        {
            //on vérifie si le mot de passe de la BDD est le même que celui rentré
            if ($_REQUEST["NouveauPassword"] == $_REQUEST["ConfirmPassword"]) {
                $Vue->setEntete(new Vue_Structure_Entete());
                $Vue->setMenu(new Vue_Menu_Administration());
                if(Fonctions\CalculComplexiteMdp($_REQUEST["ConfirmPassword"]) <= 90){
                    $Vue->addToCorps(new Vue_Utilisateur_Changement_MDP("<center><b>Votre mot de passe ne respecte pas la politique du mot de passe</b></center>", "Gerer_monCompte"));
                }else{
                    Modele_Utilisateur::Utilisateur_Modifier_motDePasse($_SESSION["idUtilisateur"], $_REQUEST["NouveauPassword"]);
                    $Vue->addToCorps(new Vue_Compte_Administration_Gerer("<center><b>Votre mot de passe a bien été modifié</b></center>"));
                    // Dans ce cas les mots de passe sont bons, il est donc modifier
                }
            } else {
                $Vue->setEntete(new Vue_Structure_Entete());
                $Vue->setMenu(new Vue_Menu_Administration());
                $Vue->addToCorps(new Vue_Utilisateur_Changement_MDP("<center><b>Les nouveaux mots de passe ne sont pas identiques</b></center>", "Gerer_monCompte"));
            }
        } else {
            $Vue->setEntete(new Vue_Structure_Entete());
            $Vue->setMenu(new Vue_Menu_Administration());
            $Vue->addToCorps(new Vue_Utilisateur_Changement_MDP("<center><b>Vous n'avez pas saisi le bon mot de passe</b></center>", "Gerer_monCompte"));
        }
        break;
    case  "SeDeconnecter":
        //L'utilisateur a cliqué sur "se déconnecter"
        session_destroy();
        unset($_SESSION);
        $Vue->setEntete(new Vue_Structure_Entete());
        $Vue->addToCorps(new Vue_Connexion_Formulaire_client());
        break;
    default:
        //Cas par défaut: affichage du menu des actions.
        $Vue->setEntete(new Vue_Structure_Entete());
        $Vue->setMenu(new Vue_Menu_Administration());
        $Vue->addToCorps(new Vue_Compte_Administration_Gerer());
        break;
}

$Vue->setBasDePage(new Vue_Structure_BasDePage());
