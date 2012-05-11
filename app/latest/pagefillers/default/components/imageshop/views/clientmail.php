<?php echo $post["name"]; ?>, 

Dit is een bevestigingsmail van de foto's die je bestelt hebt.

Een overzicht van de bestelde foto's staat hieronder.

<?php

    foreach($post["cart"] as $id => $info) {
        echo "Foto nr. " . $id . " (" . str_replace("/50", "", $info["src"]) . ")
";
        foreach($info['orderlines'] as $orderline) {
            echo "    " . $orderline["size"] . " => " . $orderline["amount"] . "
";
        }
    }
?>