<?php
require("includes/config.inc.php");
require("includes/conn.inc.php");
// require("kategorien.php");
$idKat = 0; //kennzeichnet, dass keine idKat übergeben wurde
if (count($_GET) > 0 && isset($_GET["idKat"])) {
    $idKat = intval($_GET["idKat"]);
    // echo $idKat;
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Hardqwarekomponenten</title>
</head>

<body>
    <h1>Produktenkategorien</h1>
    <ul>
        <li><a href="startseite.php">Startseite</a></li>
        <li><a href="kategorien.php">Kategorien</a></li>
        <!-- <li><a href="...">...</a></li> -->
    </ul><br>
</body>

</html>
<?php
if ($idKat > 0) {
    echo "<ul>";
    $sql = "
            SELECT
                tbl_produkte.*,
                tbl_lieferbarkeiten.Lieferbarkeit
            FROM tbl_produkte
            INNER JOIN tbl_lieferbarkeiten ON tbl_lieferbarkeiten.IDLieferbarkeit=tbl_produkte.FIDLieferbarkeit
            WHERE(
                FIDKategorie=" . $idKat . "
            )
		";

    $resultProduct = $conn->query($sql) or die("Fehler in der Query " . $conn->error . "<br>" . $sql);
    while ($row = $resultProduct->fetch_assoc()) {
        if (is_null($row["Produktfoto"])) {
            $bild = "";
        } else {
            $bild = '<img src="' . $row["Produktfoto"] . '" alt="' . $row["Produktfoto"] . '">';
        }
        echo "<li>" . $row["Produkt"] . " (" . $row["Artikelnummer"] . ")";
        echo "<div>" . $row["Beschreibung"] . "</div>";
        echo "EUR " .  $row["Preis"] . " (" . $row["Lieferbarkeit"] . ") " . $bild;
        echo "</li>";
    }
    echo "</ul>";
}
?>