<?php
require("./Saingleuton.php");
$db = Saingleuton::getInstance();
echo json_encode($db->getData($_GET['nom']));
die();
