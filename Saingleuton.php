
<?php

class Saingleuton
{
	private $PDOInstance = null;
	private static $instance = null;

	private function __construct()
	{
		$this->PDOInstance =   new PDO("pgsql:host=localhost;port=5432;dbname=golfeur", 'postgres', 'postgres', array(
			PDO::ATTR_EMULATE_PREPARES => false,
			PDO::MYSQL_ATTR_DIRECT_QUERY => false,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		));
	}

	public static function getInstance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new Saingleuton();
		}
		return self::$instance;
	}



	public function insertGolfeur($nom)
	{
		$query = $this->PDOInstance->prepare('select nom from golfeur where nom = ?');
		$query->bindValue(1, $nom);
		$query->execute();
		if ($query->rowCount() == 0) {
			$query = $this->PDOInstance->prepare('insert into golfeur(nom) values(?)');
			$query->bindValue(1, $nom);
			$query->execute();
		}
	}

	public function insertCompet($id, $nom, $date)
	{
		try {
			$query = $this->PDOInstance->prepare('insert into compet(nom, date, id) values(?,?,?)');
			$query->bindValue(1, $nom);
			$query->bindValue(2, $date);
			$query->bindValue(3, $id);
			$query->execute();
		} catch (Exception $e) {
			echo 'Exception compet -> ';
			var_dump($e->getMessage());
		}
	}

	public function insertResultat($nom_golf, $id_compet, $tn, $tb, $totaln, $totalb, $idx)
	{
		try {
			$id_golf = $this->PDOInstance->prepare('select id from golfeur where nom = ?');
			$id_golf->bindValue(1, $nom_golf);
			$id_golf->execute();
			$id_golf = $id_golf->fetchAll()[0][0];
			$id_golf = strval($id_golf);
			$query = $this->PDOInstance->prepare('insert into resultat(id_golfeur, id_compet, t1n, t2n, t3n, t4n, t1b, t2b, t3b, t4b, totalb, totaln, idx) values(?,?,?,?,?,?,?,?,?,?,?,?,?)');
			$query->bindValue(1, $id_golf);
			$query->bindValue(2, $id_compet);
			$query->bindValue(3, $tn[0]);
			$query->bindValue(4, $tn[1]);
			$query->bindValue(5, $tn[2]);
			$query->bindValue(6, $tn[3]);
			$query->bindValue(7, $tb[0]);
			$query->bindValue(8, $tb[1]);
			$query->bindValue(9, $tb[2]);
			$query->bindValue(10, $tb[3]);
			$query->bindValue(11, $totaln);
			$query->bindValue(12, $totalb);
			$query->bindValue(13, $idx);
			$query->execute();
		} catch (Exception $e) {
			echo 'Exception resulat-> ';
			var_dump($e->getMessage());
		}
	}

	public function getData($nom)
	{
		$query = $this->PDOInstance->prepare("SELECT golfeur.nom, idx, totaln, totalb, date FROM golfeur inner join resultat on resultat.id_golfeur = golfeur.id inner join compet on compet.id = resultat.id_compet where golfeur.nom=? order by date;");
		$query->bindValue(1, $nom);
		$query->execute();
		if ($query->rowCount() > 0) {
			while ($value = $query->fetch(PDO::FETCH_OBJ)) {
				$nom = $value->nom;
				$idx = $value->idx;
				$totaln = $value->totaln;
				$totalb = $value->totalb;
				$date = $value->date;
				$result_array[] = ['nom' => $nom, 'totaln' => $totaln, 'totalb' => $totalb, 'idx' => $idx, 'date' => $date];
			}
			return $result_array;
		}
		return $result_array;
	}

	public function getGolfeur()
	{
		$query = $this->PDOInstance->prepare("SELECT distinct nom from golfeur;");
		$query->execute();
		if ($query->rowCount() > 0) {
			while ($value = $query->fetch(PDO::FETCH_OBJ)) {
				$result_array[] = ['nom' => $value->nom];
			}
		}
		return $result_array;
	}
}
