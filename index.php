<?php

try {
    $db = new PDO(
        'mysql:host=localhost;dbname=pays;charset=utf8',
        'userPays',
        '@e/O6Yli.iV5EArw'
    );
    // echo 'Vous êtes connecté';

} catch (PDOException $e) {
    echo 'Échec lors de la connexion : ' . $e->getMessage();
}
if  (isset($_GET["region"]) && $_GET["region"]) {
    $sqlQueryContinent= 'SELECT p.id_pays as "id",p.libelle_pays AS "libelle", p.population_pays AS population ,p.taux_natalite_pays AS natalite,p.taux_mortalite_pays AS mortalite,p.esperance_vie_pays AS esperance,p.taux_mortalite_infantile_pays AS mortinfant,p.nombre_enfants_par_femme_pays AS nbrenfant,p.taux_croissance_pays AS croissance,p.population_plus_65_pays AS pop65 FROM t_regions r INNER JOIN t_pays p ON r.id_region =p.region_id  WHERE p.region_id = ' . $_GET["region"] . ' GROUP BY libelle ORDER BY libelle ASC';
    

} else {
// Condition pour : On choisit le continent et on affiche toutes les infos par région
    if (isset($_GET["continent"]) && $_GET["continent"]) {

        $sqlQueryContinent = 'SELECT r.id_region as "id",r.libelle_region AS "libelle", SUM(p.population_pays)AS population,AVG(p.taux_natalite_pays) AS natalite,AVG(p.taux_mortalite_pays) AS mortalite,AVG(p.esperance_vie_pays) AS esperance,AVG(p.taux_mortalite_infantile_pays) AS mortinfant,AVG(p.nombre_enfants_par_femme_pays) AS nbrenfant,AVG(p.taux_croissance_pays) AS croissance,SUM(p.population_plus_65_pays) AS pop65 FROM t_regions r INNER JOIN t_pays p ON r.`id_region`=p.region_id WHERE p.continent_id = ' . $_GET["continent"] . ' GROUP BY libelle ORDER BY libelle ASC';
        // p.continent_id = ' . $_GET["continent"] .  ***explication***
        // on doit selectionner dans la barre l'id du continent, au lieu de faire une requête pour chaque id, on met => ***. $_GET["continent"]*** pour demander que l'on récupère l'id automatiquement et qu'on affiche en fonction
        // id = 1 -> Afrique
        // id = 2 -> Amérique Latine et Caraïbes
        // id = 3 -> Amérique Septentrionale
        // print $sqlQuery;
    } 
    // Sinon : on choisit Monde (dans la barre, il n'y a choix selectionné **GET**) et on affiche chaque continent avec les moyennes et en plus monde avec les moyennes globales
    else {
        $sqlQueryContinent = 'SELECT c.id_continent AS "id",c.libelle_continent AS "libelle", SUM(p.population_pays)AS population,AVG(p.taux_natalite_pays) AS natalite,AVG(p.taux_mortalite_pays) AS mortalite,AVG(p.esperance_vie_pays) AS esperance,AVG(p.taux_mortalite_infantile_pays) AS mortinfant,AVG(p.nombre_enfants_par_femme_pays) AS nbrenfant,AVG(p.taux_croissance_pays) AS croissance,SUM(p.population_plus_65_pays) AS pop65 FROM t_continents c INNER JOIN t_pays p ON c.`id_continent`=p.continent_id GROUP BY libelle';
    }
}




$continentAllStatement = $db->prepare($sqlQueryContinent);
$continentAllStatement->execute();
$resultFinal = $continentAllStatement->fetchAll();


//NEW REQUETE pour afficher les continents 
$sqlQuerySelectContinent = 'SELECT id_continent,libelle_continent FROM `t_continents` ORDER BY libelle_continent';

$continentsStatement = $db->prepare($sqlQuerySelectContinent);
$continentsStatement->execute();
$resultsContinents = $continentsStatement->fetchAll();
//  On réutilise ***$resultsContinents*** dans le select pour récupérer la valeur

// requête pour que les régions s'affichent en fonction du continent choisi
if (isset($_GET["continent"]) && $_GET["continent"]){
    $sqlQuerySelectRegion ='SELECT libelle_region, id_region FROM `t_regions`INNER JOIN t_continents ON (t_continents.id_continent=t_regions.continent_id) WHERE t_regions.continent_id = '. $_GET["continent"] .' GROUP BY libelle_region';
    //si le select est sur monde alors on affiche toutes les régions
}else{$sqlQuerySelectRegion = 'SELECT * FROM `t_regions`INNER JOIN t_pays ON (t_pays.region_id=t_regions.id_region) GROUP BY libelle_region';
}
$regionStatement = $db->prepare($sqlQuerySelectRegion);
$regionStatement->execute();
$resultsRegion = $regionStatement->fetchAll();




//  var_dump($results);
// NEW REQUETE = calculer les moyennes et sommes dans le tableau
// On utilise le ***. $_GET["continent"]*** pour ne pas à avoir à spécifier l'id du continent, cela se fait automatiquement
if (isset($_GET["continent"]) && $_GET["continent"]) {
    $sqlQueryCalcul = 'SELECT libelle_continent AS "libelle", SUM(p.population_pays)AS population,AVG(p.taux_natalite_pays),AVG(p.taux_mortalite_pays),AVG(p.esperance_vie_pays),AVG(p.taux_mortalite_infantile_pays),AVG(p.nombre_enfants_par_femme_pays),AVG(p.taux_croissance_pays),SUM(p.population_plus_65_pays) FROM t_pays p INNER JOIN t_continents ON (p.continent_id=t_continents.id_continent) WHERE id_continent=' . $_GET["continent"];
    

} else {
    // Si dans la barre il n'y a pas de continent selectionné, alors on affiche le calcul du monde
    $sqlQueryCalcul = 'SELECT \'MONDE\' AS "libelle", SUM(p.population_pays) AS population,AVG(p.taux_natalite_pays),AVG(p.taux_mortalite_pays),AVG(p.esperance_vie_pays),AVG(p.taux_mortalite_infantile_pays),AVG(p.nombre_enfants_par_femme_pays),AVG(p.taux_croissance_pays),SUM(p.population_plus_65_pays)  FROM t_pays p';
}

$mondeStatement = $db->prepare($sqlQueryCalcul);
$mondeStatement->execute();
$resultsCalculs = $mondeStatement->fetchAll();


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <title>Document</title>
</head>

<body>
    <label for="pays-select">Par continent</label>

    <form method="GET">
        <!-- name="continent" est appelé plus haut dans la condition => (isset($_GET["continent"]) && $_GET["continent"]) -->
        <select name="continent" id="pays-select" onchange="this.form.submit()">
        <!-- on attribut une valeur de ZERO à monde puisque les id continent commencent par 1 -->
            <option value="0">Monde</option>
            <?php foreach ($resultsContinents as $result) : ?>
                <option value="<?= $result['id_continent'] ?>" <?= isset($_GET["continent"]) && $_GET["continent"] === $result['id_continent'] ? "selected" : "" ?>><?= $result['libelle_continent'] ?></option>
            <?php endforeach ?>
        </select>
        <!-- si le select continent est différent de ZERO alors on affiche le select region -->
        <?php if (isset($_GET["continent"]) && $_GET["continent"] != 0) : ?>
        <select name="region" id="region-select" onchange="this.form.submit()">
            <option value="0">Région</option>
            <?php foreach ($resultsRegion as $resultRegion) : ?>
                <option value="<?= $resultRegion['id_region'] ?>" <?= isset($_GET["region"]) && $_GET["region"] === $resultRegion["id_region"] ? "selected" : "" ?>><?= $resultRegion['libelle_region'] ?></option>
            <?php endforeach ?>
        </select>
        <?php endif ?>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">Pays</th>
                <th scope="col">Population totale (en milliers)</th>
                <th scope="col">Taux de natalité</th>
                <th scope="col">Taux de mortalité</th>
                <th scope="col">Espérance de vie</th>
                <th scope="col">Taux de mortalité infantile</th>
                <th scope="col">Nombre d'enfant(s) par femme</th>
                <th scope="col">Taux de croissance</th>
                <th scope="col">Population de 65 ans et plus (en milliers)</th>

            </tr>
        </thead>
        <tbody>
            <!-- calcul pour chaque continent  -->
            <?php foreach ($resultFinal as $result) : ?>
                <tr>
                    <td><?= $result['libelle'] ?></td>
                    <td><?= $result['population'] ?></td>
                    <td><?= round($result['natalite'], 1) ?></td>
                    <td><?= round($result['mortalite'], 1) ?></td>
                    <td><?= round($result['esperance'], 1) ?></td>
                    <td><?= round($result['mortinfant'], 1) ?></td>
                    <td><?= round($result['nbrenfant'], 1) ?></td>
                    <td><?= round($result['croissance'], 1) ?></td>
                    <td><?= $result['pop65'] ?></td>
                </tr>
            <?php endforeach ?>

        </tbody>
        
        <tfoot>
            <tr>
<!-- Récupérer la valeur de la dernière ligne -->
                <?php foreach ($resultsCalculs as $result) : ?>
                    <th scope="row"><?= $result['libelle'] ?></th>
                    <td><?= $result['population'] ?></td>
                    <td><?= round($result['AVG(p.taux_natalite_pays)'], 1) ?></td>
                    <td><?= round($result['AVG(p.taux_mortalite_pays)'], 1) ?></td>
                    <td><?= round($result['AVG(p.esperance_vie_pays)'], 1) ?></td>
                    <td><?= round($result['AVG(p.taux_mortalite_infantile_pays)'], 1) ?></td>
                    <td><?= round($result['AVG(p.nombre_enfants_par_femme_pays)'], 1) ?></td>
                    <td><?= round($result['AVG(p.taux_croissance_pays)'], 1) ?></td>
                    <td><?= $result['SUM(p.population_plus_65_pays)'] ?></td>
                <?php endforeach ?>
            </tr>
        </tfoot>
    </table>
</body>

</html>