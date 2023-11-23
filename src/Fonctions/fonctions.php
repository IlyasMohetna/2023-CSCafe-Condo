<?php
namespace App\Fonctions;
    function Redirect_Self_URL():void{
        unset($_REQUEST);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }

function GenereMDP($nbChar) :string{

    return "secret";
}

function CalculComplexiteMdp($mdp) :int{

    $passwordLength = strlen($mdp);
    $lowercaseCount = preg_match_all('/[a-z]/', $mdp);
    $uppercaseCount = preg_match_all('/[A-Z]/', $mdp);
    $digitCount = preg_match_all('/\d/', $mdp);
    $specialCharCount = $passwordLength - ($lowercaseCount + $uppercaseCount + $digitCount);
    $alphabetLen = max($minAlphabetLen ?? 10, $lowercaseCount + $uppercaseCount + $digitCount + min($maxSpecialChars ?? 40, $specialCharCount));
    $entropy = (int) log(pow($alphabetLen, $passwordLength), 2); // bits

    return $entropy;

}