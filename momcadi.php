<!DOCTYPE html>
<?php
include("baza/bazaPodataka.php");
session_start();
$baza = new bazaPodataka();
$baza->spajanje();
if (!empty($_SESSION)) {

    $sql = "SELECT m.*, l.naziv as nazivLige FROM momcad m, liga l where m.liga_id = l.liga_id";

    $rezutat = $baza->upit($sql);
    while ($redak = $rezutat->fetch_assoc()) {
        $momcadi[] = $redak;

    }
}
$baza->zatvaranje();
?>
<html lang="en">
<head>
    <title>Momčadi</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" media="screen" type="text/css" href="css/robpucko.css"/>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>Kladionica</h1>
        <p>Projektni zadatak iz kolegija Izradnja WEB aplikacija.</p>
    </div>

    <div class="navbar">
        <a href="index.php">Početna</a>
        <a href="lige.php">Lige</a>
        <a href="o_autoru.html">O Autoru</a>
        <?php
        if (empty($_SESSION)) {
            echo '<a href = "prijava.php" class = "right">Prijava</a>';
        } else {
            if ($_SESSION["tipKorisnika"] == '1' || $_SESSION["tipKorisnika"] == '0') {
                echo '<a href="statistikaListica.php">Statistika listića</a>';
            }
            if ($_SESSION["tipKorisnika"] == '0') {
                echo '<a href="korisnici.php">Korisnici</a>';
                echo '<a href="momcadi.php" class="active">Momčadi</a>';
            }
            echo '<a href = "odjava.php" class = "right">Odjava</a>';
        }
        ?>
    </div>


    <div class="row">
        <div class="main">
            <h2>Popis momčadi</h2>
            <a href="novaMomcad.php"><input type="button" id="dodaj" value="Kreiraj momčad"/></a><br>
            <table>
                <thead>
                <tr>
                    <th></th>
                    <th>Naziv</th>
                    <th>Opis</th>
                    <th>Liga</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($momcadi)) {
                    foreach ($momcadi as $momcad) : ?>
                        <tr>
                            <td><a href='azurirajMomcad.php?momcadId=<?php echo $momcad['momcad_id'] ?>'><img src='slike/edit.png' alt='edit' height=25 width=25/></a>
                            <td><?php echo $momcad['naziv'] ?></td>
                            <td><?php echo $momcad['opis'] ?></td>
                            <td><?php echo $momcad['nazivLige'] ?></td>
                        </tr>
                    <?php endforeach;
                } ?>
            </table>
        </div>
    </div>

    <div class="footer">
        <h2></h2>
    </div>
</div>
</body>
</html>