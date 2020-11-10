<!DOCTYPE html>
<?php
include("baza/bazaPodataka.php");
session_start();
$baza = new bazaPodataka();
$baza->spajanje();

if (!empty($_SESSION)) {
    if (!empty($_GET['momcadId'])) {
        $momcadId = $_GET['momcadId'];

        $sql = "SELECT * FROM momcad WHERE momcad_id = '$momcadId'";
        $rezultat = $baza->upit($sql);
        $momcad = $rezultat->fetch_assoc();

        $sql = "SELECT * FROM liga";
        $rezultat = $baza->upit($sql);
        while ($redak = $rezultat->fetch_assoc()) {
            $lige [] = $redak;
        }

        if (empty($_POST["naziv"]) || empty($_POST["liga"])) {
            $zabranaAzuriranja = true;
        } else {
            $zabranaAzuriranja = false;
            $ligaId = $_POST["liga"];
            $naziv = $_POST["naziv"];
            $opis = $_POST["opis"];

            $sql = "UPDATE momcad SET liga_id = '$ligaId', naziv = '$naziv', opis = '$opis' WHERE momcad_id = '$momcadId';";
            $baza->upit($sql);
            header("Location: momcadi.php");
        }
    }
}
$baza->zatvaranje();
?>
<html lang="en">
<head>
    <title>Azuriranje momčadi</title>
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
            <h2>Ažuriranje momcadi</h2>
            <div class="greska"> <?php if (!empty($_POST) && $zabranaAzuriranja) echo "Niste unijeli sve potrebne elemente!"; ?></div>
            <br>
            <form method="post" id="novaLiga" action="azurirajMomcad.php?momcadId=<?php echo $momcadId ?>" name="novaMomcad">
                <label for="naziv">Naziv: </label>
                <input type="text" id="naziv" name="naziv" placeholder="naziv momčadi" value="<?php echo $momcad["naziv"] ?>">
                <label for="liga">Liga: </label>
                <select id="liga" name="liga">
                    <option value="">----</option>
                    <?php foreach ($lige as $liga) {
                        if ($liga['liga_id'] == $momcad['liga_id']) {
                            echo "<option value='$liga[liga_id]' selected>$liga[naziv]</option>";
                        } else {
                            echo "<option value='$liga[liga_id]'>$liga[naziv]</option>";
                        }
                    } ?>
                </select>
                <label for="opis">Opis momčadi:</label><br>
                <textarea rows="3" id="opisMomcadi" name="opis" placeholder="Ovdje unesite opis momcadi."><?php echo $momcad["opis"]; ?></textarea><br>
                <input type="submit" id="dodaj" value="Ažuriraj momčad"/>
            </form>
        </div>
    </div>

    <div class="footer">
        <h2></h2>
    </div>
</div>
</body>
</html>
