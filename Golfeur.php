<?php
class Golfeur{
    private $nom;
    private $idx;
    private $TB = [];
    private $TN = [];
    private $TotalB;
    private $TotalN;

    public function __construct($nom = null, $prenom = null, $idx = null, $totalPointsBruts = null){
        $this->nom = $nom;
        $this->idx = $idx;
    }

    public function getNom(){
        return $this->nom;
    }
    public function setNom($nom){
        $this->nom = $nom;
    }
    public function getIdx(){
        return $this->idx;
    }
    public function setIdx($idx){
        $this->idx = $idx;
    }
    public function getTB(){
        return $this->TB;
    }
    public function setTB($TB){
        $this->TB = $TB;
    } 
    public function getTN(){
        return $this->TB;
    }
    public function setTN($TN){
        $this->TN = $TN;
    } 
    public function getTotalN(){
        return $this->TotalN;
    }
    public function setTotalN($TotalN){
        $this->TotalN = $TotalN;
    } 
    public function getTotalB(){
        return $this->TotalB;
    }
    public function setTotalB($TotalB){
        $this->TotalB = $TotalB;
    }

    public function getInfos($url){
        $tempB = file_get_contents($url);
        $tempN = file_get_contents($url.'&res=N');
        $nb_joueur = $this->countMembers(preg_split("[<p>]", $tempB));
        $tempoB = preg_split("/<td class=mtdgris/", $tempB);
        $tempoN = preg_split("/<td class=mtdgris/", $tempN);
        $golfeurs = [];
        for($i=0, $count = 0;$i<$nb_joueur;$i++){
            $golfeur = new Golfeur();
            if($i%2 == 0){
                $names = $this->computeName($tempoB[$count+3]);
                $prenom = $names[0];
                $nom = implode($names, " ");
                $idx = $this->removeHtmlTag($tempoB[$count+4]);
                $T1B = $this->removeHtmlTag($tempoB[$count+6]);
                $T2B = $this->removeHtmlTag($tempoB[$count+7]);
                $T3B = $this->removeHtmlTag($tempoB[$count+8]);
                $T4B = $this->removeHtmlTag($tempoB[$count+9]);
                $T1N = $this->removeHtmlTag($tempoN[$count+6]);
                $T2N = $this->removeHtmlTag($tempoN[$count+7]);
                $T3N = $this->removeHtmlTag($tempoN[$count+8]);
                $T4N= $this->removeHtmlTag($tempoN[$count+9]);
                $deuxJB = preg_split("/<td class=mtdblanc/", $tempoB[$count+10]);
                $deuxJN = preg_split("/<td class=mtdblanc/", $tempoN[$count+10]);
                $TotalN = $this->computeTotal([$T1N, $T2N, $T3N, $T4N]);
                $TotalB = $this->computeTotal([$T1B, $T2B, $T3B, $T4B]);
                $a = [$T1B, $T2B, $T3B, $T4B];
                $b = [$T1N, $T2N, $T3N, $T4N];
                $golfeur->nom = $nom;
                $golfeur->idx = $idx ;
                $golfeur->TB = $a;
                $golfeur->TN = $b;
                $golfeur->TotalB = $TotalB;
                $golfeur->TotalN = $TotalN;
                $count+=10;
            }else{
                $names = $this->computeName($deuxJB[3]);
                $prenom = $names[0];
                $nom = implode($names, " ");
                $idx = $this->removeHtmlTag($deuxJB[4]);
                $T1B = $this->removeHtmlTag($deuxJB[6]);
                $T2B = $this->removeHtmlTag($deuxJB[7]);
                $T3B = $this->removeHtmlTag($deuxJB[8]);
                $T4B = $this->removeHtmlTag($deuxJB[9]);
                $T1N = $this->removeHtmlTag($deuxJN[6]);
                $T2N = $this->removeHtmlTag($deuxJN[7]);
                $T3N = $this->removeHtmlTag($deuxJN[8]);
                $T4N = $this->removeHtmlTag($deuxJN[9]);
                $deuxJB = preg_split("/<td class=mtdblanc/", $deuxJB[10]);
                $deuxJN = preg_split("/<td class=mtdblanc/", $deuxJN[10]);
                $TotalB = $this->removeHtmlTag($deuxJB[0]);
                $TotalN = $this->removeHtmlTag($deuxJN[0]);
                $a = [$T1B, $T2B, $T3B, $T4B];
                $b = [$T1N, $T2N, $T3N, $T4N];
                $golfeur->nom = $nom;
                $golfeur->idx = $idx ;
                $golfeur->TB = $a;
                $golfeur->TN = $b;
                $golfeur->TotalB = $TotalB;
                $golfeur->TotalN = $TotalN;
                
                }
            array_push($golfeurs, $golfeur);
        }
        return $golfeurs;
    }

    public function computeTotal($tab){
        $count = 0;
        foreach($tab as $value){
            if($value != '-' && $value != 'FOR'){
                $count+=intval($value);
            }
        }
        return $count;
    }
    public function removeHtmlTag($tab){
        $mdr = preg_split("['>]", $tab);
        $mdr = preg_split("[</]", $mdr[1])[0];
        return $mdr;
    }
    public function computeName($tab){
        $nom1 = preg_split("[CAPTION, ']",$tab);
        $nom1 = preg_split("[']", $nom1[1]);
        $nom1 = preg_split("[ ]", $nom1[0]);
        $prenom = $nom1[count($nom1) - 1];
        array_pop($nom1);
        $nom = implode($nom1," ");
        return [$prenom, $nom];
    }
    public function countMembers($tab){
        $tempoTab = 0;
       foreach($tab as $value){
           $tempoTab += preg_match_all("[img/flag]", $value, $matches);
       }
        return $tempoTab;
    }


}
