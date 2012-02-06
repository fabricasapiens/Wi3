Bestelling geplaatst door <?php echo $post["name"] . " (" . $post["emailaddress"] . ")."; ?>

Bestelde foto's :

<?php

    foreach($post["cart"] as $id => $info) {
        echo "Foto nr. " . $id . " (" . $info["src"] . ")
";
        foreach($info['orderlines'] as $orderline) {
            echo "    " . $orderline["size"] . " => " . $orderline["amount"] . "
";
        }
    }
?>