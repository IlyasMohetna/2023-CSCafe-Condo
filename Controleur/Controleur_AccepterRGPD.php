<?php

use App\Modele\Modele_Utilisateur;
use App\Vue\Vue_ConsentementRGPD;
use App\Vue\Vue_Structure_Entete;
use App\Vue\Vue_Menu_Administration;
use App\Vue\Vue_Structure_BasDePage;

$Vue->setEntete(new Vue_Structure_Entete());

switch ($action) {
    case "AfficherRGPD":
        $Vue->addToCorps(new Vue_ConsentementRGPD());
        break;

    case "AccepterRGPD":
        $logger->info('Acceptation des termes RGPD', ["source" => $_SESSION['idUtilisateur']]);
        switch ($_SESSION['typeConnexionBack']) {
            case 'utilisateurCafe':
                Modele_Utilisateur::Utilisateur_Activer_RGPD($_SESSION["idUtilisateur"]);
                $Vue->setMenu(new Vue_Menu_Administration());
                break;
            case 'salarieEntrepriseCliente':
                include "./Controleur/Controleur_Catalogue_client.php";
                break;    
        }
        break;    
}

function AfficherRGPD(){
    $Vue->addToCorps(new Vue_ConsentementRGPD());
}


$Vue->setBasDePage(new Vue_Structure_BasDePage());