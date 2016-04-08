<?php
require_once "./src/Product.php";
require_once "./src/DbConnection.php";

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $productName = $_POST['productName'];
    $productDescription = $_POST['productDescription'];
    $productPrice = $_POST['productPrice'];
    $productQuantity = $_POST['productQuantity'];
    $sendProduct = $_POST['sendProduct'];

    if(isset($productName) && isset($productDescription) &&
       isset($productPrice) && isset($productQuantity)){
        $newProductObject = new Product();
        $newProductObject->setName($productName);
        $newProductObject->setDescription($productDescription);
        $newProductObject->setPrice($productPrice);
        $newProductObject->setQuantity($productQuantity);
        $newProductObject->setIsDeleted(0);

        if($newProductObject->addProduct($conn) === true){
            echo "added $productName to DB";
        }

        var_dump($newProductObject);
    } else {
        echo "Error with adding product to DB".$conn->error;
    }

    var_dump($_POST);
}
?>

<html>
<head>

</head>
<body>

<form action="#" method="post">
    <lable>
        Product name:
        <input type="text" name="productName">
    </lable>
    <lable>
        Product description:
        <textarea name="productDescription" rows="4" cols="40"></textarea>
    </lable>
    <label>
        Product price:
        <input type="number" name="productPrice">
    </label>
    <label>
        Product quantity:
        <input type="number" name="productQuantity">
    </label>
    <input type="submit" name="sendProduct" class="btn btn-primary btn-lg">
</form>

</body>
</html>
