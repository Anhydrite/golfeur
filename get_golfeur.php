<?php
require("./Saingleuton.php");
$db = Saingleuton::getInstance();
echo json_encode($db->getGolfeur());
die();
