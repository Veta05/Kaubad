<?php
require ('conf.php');

// tagastab isAdmin session
function isAdmin(){
    return $_SESSION['onAdmin'] ==1;
}

//sorteerimine
function countryData($sort_by = "kaubanimi", $search_term = "") {
    global $connection;
    $sort_list = array("kaubanimi", "hind", "kaubagrupp_id");
    if(!in_array($sort_by, $sort_list)) {
        return "Seda tulpa ei saa sorteerida";
    }
    $request = $connection->prepare("SELECT kaubad.id, kaubanimi, hind, kaubagrupid.kaubagrupp
    FROM kaubad, kaubagrupid 
    WHERE kaubad.kaubagrupp_id = kaubagrupid.id 
    AND (kaubanimi LIKE '%$search_term%' OR hind LIKE '%$search_term%' OR kaubagrupp LIKE '%$search_term%')
    ORDER BY $sort_by");
    $request->bind_result($id, $kaubanimi, $hind, $kaubagrupp);
    $request->execute();
    $data = array();
    while($request->fetch()) {
        $product = new stdClass();
        $product->id = $id;
        $product->kaubanimi = htmlspecialchars($kaubanimi);
        $product->hind = htmlspecialchars($hind);
        $product->kaubagrupi_nimi = $kaubagrupp;
        array_push($data, $product);
    }
    return $data;
}
// valitud rea näitamine
function createSelect($query, $name) {
    global $connection;
    $query = $connection->prepare($query);
    $query->bind_result($id, $data);
    $query->execute();
    $result = "<select name='$name'>";
    while($query->fetch()) {
        $result .= "<option value='$id'>$data</option>";
    }
    $result .= "</select>";
    return $result;
}

//kaubagrupi andmete lisamine tabelisse
function addProductGroup($productGroup_name) {
    global $connection;
    $query = $connection->prepare("INSERT INTO kaubagrupid (kaubagrupp)
    VALUES (?)");
    $query->bind_param("s", $productGroup_name);
    $query->execute();
}
// Kaubade andmete lisamine andmetabelisse
function addProduct($product, $price, $productGroup_id) {
    global $connection;
    $query = $connection->prepare("INSERT INTO kaubad (kaubanimi, hind, kaubagrupp_id)
    VALUES (?, ?, ?)");
    $query->bind_param("ssd", $product, $price, $productGroup_id);
    $query->execute();
}
//Kaubade andmete kustutamine
function deleteProduct($product_id) {
    global $connection;
    $query = $connection->prepare("DELETE FROM kaubad WHERE id=?");
    $query->bind_param("i", $product_id);
    $query->execute();
}
//Kauba andmete muutmine
function saveProduct($product_id, $product, $price, $productGroup_id) {
    global $connection;
    $query = $connection->prepare("UPDATE kaubad
    SET kaubanimi=?, hind=?, kaubagrupp_id=?
    WHERE kaubad.id=?");
    $query->bind_param("ssii", $product, $price, $productGroup_id, $product_id);
    $query->execute();
}

?>
