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
        // Ensure that product is added to cart
        var productid = $(this).data("productid");
        if (cart[productid] == null) {
            cart[productid] = { orderlines : [], src : $(this).closest(".thumbnail").find("img").attr("src") };
        }
        // Add an orderline to the product
        cart[productid].orderlines.push({ amount: 1, size: "13x18"});
        // Render cart
        $(this).closest(".component_imageshop").find(".rightbar .cart").html(renderCart(cart));
        // Hide image, if it was visible
        $(this).closest(".component_imageshop").find(".rightbar .largeimage").fadeOut("100");
    });
    
    // Set live click events on the plus and minus buttons
        // Clicking on a + or - button will increase/decrease the amount of images that the user will order
    $(".component_imageshop [data-buttontype=increase]").live("click", function(event) {
        $(this).prevAll("span").data("amount", ($(this).prevAll("span").data("amount")+1));
        $(this).prevAll("span").text($(this).prevAll("span").data("amount"));
    });
    $(".component_imageshop [data-buttontype=decrease]").live("click", function(event) {
        $(this).prevAll("span").data("amount", ($(this).prevAll("span").data("amount")-1));
        $(this).prevAll("span").text($(this).prevAll("span").data("amount"));
    });
    
    function renderCart(cart) {
        // For every item in the cart, render its amount and details
        var r = "";
        for (itemId in cart) {
            var item = cart[itemId];
            var html = "<div class='cartitem contained mediumpadding smallmargin'><img src='" + item.src + "'></img>" +
                "<div class='orderlines'>";
                for(orderlineId in item.orderlines) {
                    var orderline = item.orderlines[orderlineId];
                    html += "<div>";
                        html += "Aantal: <span data-amount='" + orderline.amount + "'>" + orderline.amount + "</span> <button data-buttontype='decrease'>-</button><button data-buttontype='increase'>+</button> ";
                        html += "Formaat: <select>" + renderSelectOptions({'10x15':'10 x 15', '13x18':'13 x 18', '20x30':'20 x 30'}, orderline.size) + "</select>";
                    html += "</div>";
                }
                html += "</div>";
            html += "</div>";
            r += html;
        }
        return r;
    }
    
    function renderSelectOptions(options, selectedKey) {
        var html = "";
        for (key in options) {
            html += "<option value='" + key + "' ";
            if (key == selectedKey) {
                html += "selected='selected'";
            }
            html += ">" + options[key] + "</option>";
        }
        return html;
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