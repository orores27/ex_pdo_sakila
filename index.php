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
if (isset($_GET["continent"])&&$_GET["continent"]) {
    $sqlQuery='SELECT r.id_region as "id",r.libelle_region AS "libelle", SUM(p.population_pays),AVG(p.taux_natalite_pays),AVG(p.taux_mortalite_pays),AVG(p.esperance_vie_pays),AVG(p.taux_mortalite_infantile_pays),AVG(p.nombre_enfants_par_femme_pays),AVG(p.taux_croissance_pays),SUM(p.population_plus_65_pays) FROM t_regions r INNER JOIN t_pays p ON r.`id_region`=p.region_id WHERE p.continent_id = '.$_GET["continent"].' GROUP BY libelle ORDER BY libelle ASC';
} else {
    $sqlQuery = 'SELECT c.id_continent AS "id",c.libelle_continent AS "libelle", SUM(p.population_pays),AVG(p.taux_natalite_pays),AVG(p.taux_mortalite_pays),AVG(p.esperance_vie_pays),AVG(p.taux_mortalite_infantile_pays),AVG(p.nombre_enfants_par_femme_pays),AVG(p.taux_croissance_pays),SUM(p.population_plus_65_pays) FROM t_continents c INNER JOIN t_pays p ON c.`id_continent`=p.continent_id GROUP BY libelle';
}

$continentAllStatement = $db->prepare($sqlQuery);
$continentAllStatement->execute();
$results2 = $continentAllStatement->fetchAll();

// Requête pour recherche tous les continents avec les infos


//NEW REQUETE
$sqlQuery1 = 'SELECT id_continent,libelle_continent FROM `t_continents` ORDER BY libelle_continent';
$continentsStatement = $db->prepare($sqlQuery1);
$continentsStatement->execute();
$resultsContinents = $continentsStatement->fetchAll();


// requête pour trouver  que les régions
$sqlQuery1 = 'SELECT * FROM `t_regions`INNER JOIN t_pays ON (t_pays.region_id=t_regions.id_region) GROUP BY libelle_region';

$regionStatement = $db->prepare($sqlQuery1);
$regionStatement->execute();
$results1 = $regionStatement->fetchAll();

//  var_dump($results);
if (isset($_GET["continent"])&&$_GET["continent"]) {
    $sqlQuery3 = 'SELECT libelle_continent AS "libelle", SUM(p.population_pays),AVG(p.taux_natalite_pays),AVG(p.taux_mortalite_pays),AVG(p.esperance_vie_pays),AVG(p.taux_mortalite_infantile_pays),AVG(p.nombre_enfants_par_femme_pays),AVG(p.taux_croissance_pays),SUM(p.population_plus_65_pays) FROM t_pays p INNER JOIN t_continents ON (p.continent_id=t_continents.id_continent) WHERE id_continent='.$_GET["continent"];
} else {
    $sqlQuery3 = 'SELECT \'MONDE\' AS "libelle", SUM(p.population_pays),AVG(p.taux_natalite_pays),AVG(p.taux_mortalite_pays),AVG(p.esperance_vie_pays),AVG(p.taux_mortalite_infantile_pays),AVG(p.nombre_enfants_par_femme_pays),AVG(p.taux_croissance_pays),SUM(p.population_plus_65_pays) FROM t_pays p';
}


$mondeStatement = $db->prepare($sqlQuery3);
$mondeStatement->execute();
$results3 = $mondeStatement->fetchAll();


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
        <select name="continent" id="pays-select" onchange="this.form.submit()">
            <option value="0">Monde</option>
            <?php foreach ($resultsContinents as $result) : ?>
                <option value="<?= $result['id_continent'] ?>" <?= isset($_GET["continent"]) && $_GET["continent"] === $result['id_continent'] ? "selected" : "" ?>><?= $result['libelle_continent'] ?></option>
            <?php endforeach ?>
        </select>
        <select name="region" id="region-select" onchange="this.form.submit()">
            <option value="0">Région</option>
            <?php foreach ($results1 as $result1) : ?>
                <option value="<?= $result1['id_region'] ?>" <?= isset($_GET["region"]) && $_GET["region"] === $result1["id_region"] ? "selected" : "" ?>><?= $result1['libelle_region'] ?></option>
            <?php endforeach ?>
        </select>
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
            <?php foreach ($results2 as $result) : ?>
                <tr>
                    <td><?= $result['libelle'] ?></td>
                    <td><?= $result['SUM(p.population_pays)'] ?></td>
                    <td><?= round($result['AVG(p.taux_natalite_pays)'], 1) ?></td>
                    <td><?= round($result['AVG(p.taux_mortalite_pays)'], 1) ?></td>
                    <td><?= round($result['AVG(p.esperance_vie_pays)'], 1) ?></td>
                    <td><?= round($result['AVG(p.taux_mortalite_infantile_pays)'], 1) ?></td>
                    <td><?= round($result['AVG(p.nombre_enfants_par_femme_pays)'], 1) ?></td>
                    <td><?= round($result['AVG(p.taux_croissance_pays)'], 1) ?></td>
                    <td><?= $result['SUM(p.population_plus_65_pays)'] ?></td>
                </tr>
            <?php endforeach ?>

        </tbody>
        <!-- recuperer la valeur du select et le mettre avec une somme -->
        <tfoot>
            <tr>
                
                <?php foreach ($results3 as $result) : ?>
                    <th scope="row"><?= $result['libelle'] ?></th>
                <td><?= $result['SUM(p.population_pays)'] ?></td>
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