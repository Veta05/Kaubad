<?php
require("conf.php");

session_start();
if(!isset($_SESSION['tuvastamine'])){
    header('Location: AB_login.php');
    exit();
}

require("functions.php");
$sort = "kaubanimi";
$search_term = "";
if(isset($_REQUEST["sort"])) {
    $sort = $_REQUEST["sort"];
}
if(isset($_REQUEST["search_term"])) {
    $search_term = $_REQUEST["search_term"];
}
if(isset($_REQUEST["kaubagrupi_lisamine"])) {
    global $connection;
    $kaubagrupi_nimi=$_REQUEST["kaubagrupi_nimi"];
    $query=mysqli_query($connection, "SELECT * FROM kaubagrupid WHERE kaubagrupp='$kaubagrupi_nimi'");

    if (!empty(trim($_REQUEST["kaubagrupi_nimi"])) &&
        mysqli_num_rows($query)==0)
    {
        addProductGroup($_REQUEST["kaubagrupi_nimi"]);
        header("Location: index.php");
        exit();
    }
}
if(isset($_REQUEST["kauba_lisamine"])) {
    // ei saa lisada tühja või tühikuga kaubanimi ja hind
    if(!empty(trim($_REQUEST["kaubanimi"])) && !empty(trim($_REQUEST["hind"]))){
        addProduct($_REQUEST["kaubanimi"], $_REQUEST["hind"], $_REQUEST["kaubagrupp_id"]);
        header("Location: index.php");
        exit();
    }
}
if(isset($_REQUEST["delete"]) && isAdmin()) {
    deleteProduct($_REQUEST["delete"]);
}
if(isset($_REQUEST["save"])) {
    saveProduct($_REQUEST["changed_id"], $_REQUEST["kaubanimi"], $_REQUEST["hind"], $_REQUEST["kaubagrupp_id"]);
}
$products = countryData($sort, $search_term);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Kaubad ja kaubagrupid</title>
</head>
<body>
<header class="header">

    <div class="container2">
        <h1>Tabelid | Kaubad ja kaubagrupid</h1>
    </div>
</header>
<?=$_SESSION['kasutaja']?> on sisse logitud
<form action="logout.php" method="post">
    <input type="submit" value="Logi välja" name="logout">
</form>
<main class="main">
    <div class="container">
        <form action="index.php">
            <input type="text" name="search_term" placeholder="Otsi...">
        </form>
    </div>
    <?php if(isset($_REQUEST["edit"]) && $_SESSION['onAdmin']==1): ?>
        <?php foreach($products as $product): ?>
            <?php if($product->id == intval($_REQUEST["edit"])): ?>
                <div class="container">
                    <form action="index.php">
                        <input type="hidden" name="changed_id" value="<?=$product->id ?>"/>
                        <input type="text" name="kaubanimi" value="<?=$product->kaubanimi?>">
                        <input type="text" name="hind" value="<?=$product->hind?>">
                        <?php echo createSelect("SELECT id, kaubagrupp FROM kaubagrupid", "kaubagrupp_id"); ?>
                        <a title="Katkesta muutmine" class="cancelBtn" href="index.php" name="cancel">X</a>
                        <input type="submit" name="save" value="&#10004;">
                    </form>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <div class="container">
        <table>
            <thead>
            <tr>
                <th>Id</th>
                <th><a href="index.php?sort=kaubanimi">Kaubanimi</a></th>
                <th><a href="index.php?sort=hind">Hind</a></th>
                <th><a href="index.php?sort=kaubagrupp">Kaubagrupp</a></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($products as $product): ?>
                <tr>
                    <td><strong><?=$product->id ?></strong></td>
                    <td><?=$product->kaubanimi ?></td>
                    <td><?=$product->hind ?></td>
                    <td><?=$product->kaubagrupp_id ?></td>
                    <?php  if($_SESSION["onAdmin"]==1) { ?>
                        <td>
                            <a title="Kustuta kaup" class="deleteBtn" href="index.php?delete=<?=$product->id?>"
                               onclick="return confirm('Oled kindel, et soovid kustutada?');">X</a>
                            <a title="Muuda kaupa" class="editBtn" href="index.php?edit=<?=$product->id?>">&#9998;</a>
                        </td>
                    <?php  } ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <form action="index.php">
            <h2>Kaubagrupi lisamine:</h2>
            <dl>
                <dt>Kaubagrupi nimetus:</dt>
                <dd><input type="text" name="kaubagrupi_nimi" placeholder="Sisesta kaubagrupp..."></dd>
                <input type="submit" name="kaubagrupi_lisamine" value="Lisa kaubagrupp">
            </dl>
        </form>
        <form action="index.php">
            <h2>Kauba lisamine:</h2>
            <dl>
                <dt>Kaubanimi:</dt>
                <dd><input type="text" name="kaubanimi" placeholder="Sisesta kaubanimi..."></dd>
                <dt>Hind:</dt>
                <dd><input type="number" name="hind" placeholder="Sisesta hind..."></dd>
                <dt>Kaubagrupp</dt>
                <dd><?php
                    echo createSelect("SELECT id, kaubagrupp FROM kaubagrupid", "kaubagrupp_id");
                    ?></dd>
                <input type="submit" name="kauba_lisamine" value="Lisa kaup">
            </dl>
        </form>
    </div>
</main>
</body>
<footer class="container2">
    <strong>&copy; Jelizaveta Aia</strong>
    <?php
    echo "<strong>";
    echo date('Y');
    echo "</strong>"
    ?>
</footer>
</html>
