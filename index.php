<?php
require_once("./Golfeur.php");
require_once("./Saingleuton.php");
$pedro = new Golfeur('Pedro', 'Sanchez');
$db = Saingleuton::getInstance();



$temp = file_get_contents("https://web.ffgolf.org/resultats/listcompet.php?pub=1&gk=d73c6130810543df7e7caf183b7b62d2");
$temp = preg_split('/var cpt/', $temp);
$temp = preg_split('/\$\(document\).ready/', $temp[1])[0];
$temp = substr($temp,5, strlen($temp)-10);
$temp = preg_split('/\[\"/', $temp);
array_shift($temp);
$id_golfeur = 1;
foreach($temp as $id_compet => $compet){
    if(strpos(strval($compet), "Simple") != false){
        $compet = preg_split('/,"/', $compet);
        $lien = substr($compet[0], 0, strlen($compet[0])-1);
        $competName = substr($compet[1], 0, strlen($compet[1])-1);
        $competDate = substr($compet[4], 0, strlen($compet[4])-4);
        $db->insertCompet($id_compet+1, $competName, $competDate);
        $golfeurs = $pedro->getInfos("https://web.ffgolf.org/resultats/".$lien);

        foreach($golfeurs as $golfeur){
            $db->insertGolfeur($golfeur->getNom());
            $db->insertResultat($golfeur->getNom(), $id_compet+1, $golfeur->getTN(), $golfeur->getTB(), $golfeur->getTotalN(), $golfeur->getTotalB(), $golfeur->getIdx());
            $id_golfeur++;  
        }

    }
    
}
