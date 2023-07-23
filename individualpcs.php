<?php
require("includes/config.inc.php");
require("includes/conn.inc.php");
// Hierbei soll es möglich 
// sein, dass nach Komponenten gesucht wird (nach (Teilen) der Produktbezeichnung 
// oder (Teilen) der Artikelnummer), wobei alsdann nur diejenigen individuell 
// zusammengestellten PCs dargestellt werden sollen, in welchen diese Komponente 
// vorkommt
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Hardqwarekomponenten</title>
</head>

<body>
    <h1>Individual PCs</h1>
    <ul>
        <li><a href="startseite.php">Startseite</a></li>
        <li><a href="kategorien.php">Kategorien</a></li>
    </ul><br>
    <form action="individualpcs.php" method="post">
        <label for=>Komponente:</label><br>
        <input type="text" name="kom"> <br>
        <label for=>Artikelnummer:</label><br>
        <input type="text" name="art"> <br>
        <label for=>Produktbezeichnung:</label><br>
        <input type="text" name="pb"> <br><br>
        <button>Suchen</button>
    </form>
</body>

</html>
<?php
if (count($_POST) > 0) {
    if (strlen($_POST["kom"]) > 0) {
        $arr[] = "tbl_produkte.Produkt = '%" . $_POST["kom"] . "%'";
    }
    if (strlen($_POST["art"]) > 0) {
        $arr[] = "tbl_produkte.Artikelnummer = '%" . $_POST["art"] . "%'";
    }
    if (strlen($_POST["pb"]) > 0) {
        $arr[] = "tbl_produkte.Beschreibung = '%" . $_POST["pb"] . "%'";
    }
    $arr_Where[] = "
			(
				SELECT COUNT(tbl_pr.IDProdukt) AS cnt FROM tbl_konfigurator
				INNER JOIN tbl_produkte AS tbl_pr ON tbl_konfigurator.FIDKomponente=tbl_pr.IDProdukt
				WHERE(
					" . implode(" AND ", $arr) . "
				)
			)>0
		";
    $sql = "
    SELECT
        tbl_produkte.*,
        tbl_lieferbarkeiten.Lieferbarkeit
    FROM tbl_produkte
    INNER JOIN tbl_lieferbarkeiten ON tbl_lieferbarkeiten.IDLieferbarkeit=tbl_produkte.FIDLieferbarkeit
    WHERE(
        " . implode(" AND ", $arr_Where) . "
    )
";
    $result = $conn->query($sql) or die("Fehler in der Query " . $conn->error . "<br>" . $sql);
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<br><li>" . $row["Produkt"] . " (" . $row["Artikelnummer"] . ")";
        echo "<div>" . $row["Beschreibung"] . "</div>";
        echo "(" . $row["Lieferbarkeit"] . ") ";

        $sum = 0;
        if ($row["FIDPC"] != 0) {
            echo ('
                <li>
                    ' . $row["Produkt"] . ':
                    <ul>
            ');
            $sql = "
                SELECT
                    tbl_produkte.*,
                    tbl_lieferbarkeiten.Lieferbarkeit
                FROM tbl_konfigurator
                INNER JOIN tbl_produkte ON tbl_produkte.IDProdukt=tbl_konfigurator.FIDKomponente
                INNER JOIN tbl_lieferbarkeiten ON tbl_lieferbarkeiten.IDLieferbarkeit=tbl_produkte.FIDLieferbarkeit
                WHERE(
                    FIDPC=" . $row["IDProdukt"] . "
                )
                ORDER BY tbl_produkte.Produkt ASC
            ";
            $resultKat = $conn->query($sql2) or die("Fehler in der Query " . $conn->error . "<br>" . $sql2);
            echo "<ul>";
            while ($row = $resultKat->fetch_assoc()) {
                $sum += $row["Preis"];
                echo "<li>" . $row["Produkt"] . " (" . $row["Artikelnummer"] . ")";
                echo "<div>" . $row["Beschreibung"] . "</div>";
                echo "EUR " .  $row["Preis"] . "</li>";
            }
            echo "<br><strong> Gesamtpreis: EUR " . $sum . "</strong>";
            echo "</li>";
            echo "</ul>";
        }
    } // ------------------------------------------------------------------------
} else {
    $sql = "
    SELECT DISTINCT
        tbl_konfigurator.FIDPC, tbl_produkte.Artikelnummer, tbl_produkte.Produkt, 
        tbl_produkte.Beschreibung, tbl_produkte.Preis,tbl_lieferbarkeiten.Lieferbarkeit
    FROM 
        tbl_konfigurator
    INNER JOIN 
        tbl_produkte
    ON 
        tbl_konfigurator.FIDPC = tbl_produkte.IDProdukt
    INNER JOIN 
        tbl_lieferbarkeiten
    ON 
        tbl_produkte.FIDLieferbarkeit = tbl_lieferbarkeiten.IDLieferbarkeit;
    ";
    $result = $conn->query($sql) or die("Fehler in der Query " . $conn->error . "<br>" . $sql);
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<br><li>" . $row["Produkt"] . " (" . $row["Artikelnummer"] . ")";
        echo "<div>" . $row["Beschreibung"] . "</div>";
        echo "(" . $row["Lieferbarkeit"] . ") ";

        $sum = 0;
        if ($row["FIDPC"] != 0) {
            $sql2 = "
            SELECT 
                tbl_konfigurator.FIDKomponente, tbl_produkte.Artikelnummer, tbl_produkte.Produkt, 
                tbl_produkte.Beschreibung, tbl_produkte.Preis
            FROM 
                tbl_konfigurator
            INNER JOIN 
                tbl_produkte
            ON 
                tbl_konfigurator.FIDKomponente = tbl_produkte.IDProdukt
            WHERE 
                tbl_konfigurator.FIDPC = " . $row["FIDPC"] . ";
        ";
            $resultKat = $conn->query($sql2) or die("Fehler in der Query " . $conn->error . "<br>" . $sql2);
            echo "<ul>";
            while ($row = $resultKat->fetch_assoc()) {
                $sum += $row["Preis"];
                echo "<li>" . $row["Produkt"] . " (" . $row["Artikelnummer"] . ")";
                echo "<div>" . $row["Beschreibung"] . "</div>";
                echo "EUR " .  $row["Preis"] . "</li>";
            }
            echo "<br><strong> Gesamtpreis: EUR " . $sum . "</strong>";
            echo "</li>";
            echo "</ul>";
        }
    }
}
echo "</ul>";
?>