<?php

//action.php

session_start();

if(isset($_POST["action"]))
{
    if($_POST["action"] == "add")
    {
        $bookid = $_POST["bookid"];
        $bookname = $_POST["bookname"];
        $price = $_POST["price"];
        $bookimageurl = $_POST["bookimageurl"];
        for($count = 0; $count < count($bookid); $count++)
        {
            $cart_bookid = array_keys($_SESSION["shopping_cart"]);
            if(in_array($bookid[$count], $cart_bookid))
            {
                $_SESSION["shopping_cart"][$bookid[$count]]['product_quantity']++;
            }
            else
            {
                $item_array = array(
                'bookid' =>  $bookid[$count],  
                'bookname'  =>  $bookname[$count],
                'price'=> $price[$count],
                'product_quantity'=> 1
                );
                $_SESSION["shopping_cart"][$bookid[$count]] = $item_array; 
            }
        }
    }

    //Not Yet
    // if($_POST["action"] == 'remove')
    // {
    //     foreach($_SESSION["shopping_cart"] as $keys => $values)
    //     {
    //         if($values["bookid"] == $_POST["bookid"])
    //         {
    //             unset($_SESSION["shopping_cart"][$keys]);
    //         }
    //     }
    // }
    // if($_POST["action"] == 'empty')
    // {
    //     unset($_SESSION["shopping_cart"]);
    // }
}

?>
