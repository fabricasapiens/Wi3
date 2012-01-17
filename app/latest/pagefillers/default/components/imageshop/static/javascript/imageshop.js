$(function() {
    var config = {imageSize: 50};
    // Clicking on an image in the left panel will show a larger version on the right
    $(".component_imageshop .thumbnail").click(function(event) {
        var currentWidth = $(this).closest(".component_imageshop").find(".rightbar").width();
        // Set larger image in the right pane
        $(this).closest(".component_imageshop").find(".rightbar .largeimage").html("<img src='" + $("img", this).attr("src").replace("/"+config.imageSize+"/", "/"+currentWidth+"/") + "'></img>");
        $(this).closest(".component_imageshop").find(".rightbar .largeimage").fadeIn("100");
    });
    $(".component_imageshop [data-buttontype=addtocart]").click(function(event) {
        // Prevent default action (show image)
        event.stopPropagation();
        event.preventDefault();
        // Ensure that cart exists
        if ($(this).closest(".component_imageshop").data("cart") == null) {
            $(this).closest(".component_imageshop").data("cart", {});
        }
        var cart = $(this).closest(".component_imageshop").data("cart");
        // Add product to cart
        var productid = $(this).data("productid");
        if (cart[productid] == null) {
            cart[productid] = { amount: 0 };
        }
        cart[productid].amount = (cart[productid].amount * 1) + 1;
        alert(cart);
        // Render cart
        $(this).closest(".component_imageshop").find(".rightbar .cart").html(renderCart(cart));
    });
    
    
    // Clicking on a + or - button will increase/decrease the amount of images that the user will order
    $(".component_imageshop [data-buttontype=increase]").click(function(event) {
        $(this).nextAll("span").data("amount", ($(this).nextAll("span").data("amount")+1));
        $(this).nextAll("span").text($(this).nextAll("span").data("amount"));
    });
    $(".component_imageshop [data-buttontype=decrease]").click(function(event) {
        $(this).nextAll("span").data("amount", ($(this).nextAll("span").data("amount")-1));
        $(this).nextAll("span").text($(this).nextAll("span").data("amount"));
    });
    
    function renderCart(cart) {
        // For every item in the cart, render its amount and details
        var r = "";
        for (item in cart) {
            var html = "<div class='cartitem'>Aantal: " + cart.amount + "</div>";
            r += html;
        }
        return r;
    }
    
    $(".component_imageshop .rightbar .largeimage").click(function(event) {
       $(this).fadeOut("100"); 
    });
});

/*
 * Links een veld met kleine icoontjes en als je er overheen gaat twee knoppen: vergroot en "winkelmandje".
 * Rechts een veld met een winkelwagen waar de linkse icoontjes inkomen als je op hun winkelwagentje klikt.
 * Elk icoontje rechts bevat alle instellingen voor dit product, als die er zijn. Dit omvat minimaal het aantal.
 * Als er meerdere instellingen zijn, kan je meerdere keren hetzelfde icoontje/product toevoegen.
 * Als er slechts één instelling (het aantal) is, dan kan je een product maar 1 keer toevoegen rechts.
 * 
 * Een product kan je ook slepen.
 */