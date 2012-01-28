$(function() {
    
    // TODO: put this all in one $(".component_imageshop").each and simply keep 
    // with every each() one reference to the component, instead of that we need to bubble op with closest()
    
    var config = {imageSize: 50};
    // Load or create cart per imageshop
    $(".component_imageshop").each(function(index, element) {
        var fieldId = $(this).closest("div[type=field]").attr("fieldid");
        // Ensure that cart exists
        var cart;
        // Load from localStorage, if possible
        if (typeof(localStorage) != "undefined" && typeof(JSON) != "undefined") {
            cart = JSON.parse(localStorage.getItem("wi3.pagefiller.default.component.imageshop."+fieldId));
        }
        if (cart == null) {
            cart = {};
        } else {
            // Rrender cart
            $(this).closest(".component_imageshop").find(".rightbar .cart").html(renderCart(cart));
        }
        // Set cart on element
        $(this).data("cart", cart);
        // Create functions to persist the cart
        $(this).get(0).persistCart = function() {
            if (typeof(localStorage) != "undefined" && typeof(JSON) != "undefined") {
                localStorage.setItem("wi3.pagefiller.default.component.imageshop."+fieldId, JSON.stringify($(this).data("cart")));
            }
        };
    });
    // Clicking on an image in the left panel will show a larger version on the right
    $(".component_imageshop .thumbnail").click(function(event) {
        var currentWidth = $(this).closest(".component_imageshop").find(".rightbar").width();
        // Set larger image in the right pane
        $(this).closest(".component_imageshop").find(".rightbar .largeimage").html(renderRightImage(this, currentWidth));
        $(this).closest(".component_imageshop").find(".rightbar .largeimage").fadeIn("100");
    });
    // Make the add-to-cart button work
    $(".component_imageshop button[data-buttontype=addtocart]").click(function(event) {
        var fieldId = $(this).closest("[type=field]").attr("fieldid");
        // Prevent default action (show image)
        event.stopPropagation();
        event.preventDefault();
        var cart = $(this).closest(".component_imageshop").data("cart");
        // Ensure that product is added to cart
        var productId = $(this).data("productid");
        if (cart[productId] == null) {
            cart[productId] = {orderlines : [], src : $(this).closest(".thumbnail").find("img").attr("src")};
        }
        // Add an orderline to the product
        cart[productId].orderlines.push({amount: 1, size: "13x18"});
        // Re-render cart
        $(this).closest(".component_imageshop").find(".rightbar .cart").html(renderCart(cart));
        // Hide image, if it was visible
        $(this).closest(".component_imageshop").find(".rightbar .largeimage").fadeOut("100");
    });
    
    // Set live click events on the plus and minus buttons
    // Clicking on a + or - button will increase/decrease the amount of images that the user will order
    $(".component_imageshop [data-buttontype=increase]").live("click", function(event) {
        var cart = $(this).closest(".component_imageshop").data("cart");
        var productid = $(this).closest("div.orderlines").parent("div").data("productid");
        var orderlineid = $(this).closest("div").data("orderlineid");
        cart[productid].orderlines[orderlineid].amount = cart[productid].orderlines[orderlineid].amount + 1;
        $(this).prevAll("span").text(cart[productid].orderlines[orderlineid].amount);
        // Persist data, if possible
        $(this).closest(".component_imageshop").get(0).persistCart();
    });
    $(".component_imageshop [data-buttontype=decrease]").live("click", function(event) {
        var cart = $(this).closest(".component_imageshop").data("cart");
        var productid = $(this).closest("div.orderlines").parent("div").data("productid");
        var orderlineid = $(this).closest("div").data("orderlineid");
        cart[productid].orderlines[orderlineid].amount = cart[productid].orderlines[orderlineid].amount - 1;
        $(this).prevAll("span").text(cart[productid].orderlines[orderlineid].amount);
        // Persist data, if possible
        $(this).closest(".component_imageshop").get(0).persistCart();
    });
    // Live event for size. Update order in cart if another size is chosen
    $(".component_imageshop select").live("change", function(event) {
        var cart = $(this).closest(".component_imageshop").data("cart");
        var productid = $(this).closest("div.orderlines").parent("div").data("productid");
        var orderlineid = $(this).closest("div").data("orderlineid");
        cart[productid].orderlines[orderlineid].size = $(this).val();
        // Persist data, if possible
        $(this).closest(".component_imageshop").get(0).persistCart();
    });
    // Live event for a new orderline. Update and re-render cart.
    $(".component_imageshop .rightbar [data-buttontype=addorderline]").live("click", function(event) {
        var cart = $(this).closest(".component_imageshop").data("cart");
        var productId = $(this).closest("div.orderlines").parent("div").data("productid");
        // Add an orderline to the product
        cart[productId].orderlines.push({amount: 1, size: "13x18"});
        // Persist data, if possible
        $(this).closest(".component_imageshop").get(0).persistCart();
        // Re-render cart
        $(this).closest(".component_imageshop").find(".rightbar .cart").html(renderCart(cart));
    });
    // Live event for removing a orderline. Update and re-render cart.
    $(".component_imageshop .rightbar [data-buttontype=removeorderline]").live("click", function(event) {
        var imageshop = $(this).closest(".component_imageshop"); // Since we will remove the 'this' element below, we need a reference to its parent
        var cart = $(this).closest(".component_imageshop").data("cart");
        var productId = $(this).closest("div.orderlines").parent("div").data("productid");
        var orderlineid = $(this).closest("div").data("orderlineid");
        // Remove the orderline from the product
        cart[productId].orderlines.splice(orderlineid, 1); // Remove 1 element from the Array
        // Re-render cart
        imageshop.find(".rightbar .cart").html(renderCart(cart));
        // Persist data, if possible
        imageshop.get(0).persistCart();
    });
    
    // Attach event to the order button
    $(".component_imageshop .orderbutton").click(function(event){
        // Show form that asks for email-address
        wi3.popup.show(renderOrderOverview());
        // Assign confirmation-button event to order-overview
        var cart = $(this).closest(".component_imageshop").data("cart");
        $(wi3.popup.getDOM()).find("button[data-buttontype=confirmorder]").click({cart:cart}, function(event) {
            var name = $(this).parent().find("[name=name]").val();
            var emailaddress = $(this).parent().find("[name=emailaddress]").val();
            if (name.length > 0 && emailaddress.length > 0) {
                wi3.request("pagefiller_default_component_imageshop/order", { cart : event.data.cart, name:name, emailaddress:emailaddress });
            } else {
                alert('Vul naam en emailadres in.');
            }
        });
    });
    
    function renderOrderOverview() {
        var ret = "<p>Vul je naam en emailadres in om de bestelling af te ronden.</p><p><label style='display: inline-block; width: 100px;' for='name'>Naam</label><input name='name'/></p><p><label style='display: inline-block; width: 100px;' for='emailaddress'>Emailadres</label><input name='emailaddress'/></p>";
        ret += "<label style='display: inline-block; width: 100px;' for='name'></label><button data-buttontype='confirmorder'>Bestellen</button>";
        return ret;
    }
    
    function renderCart(cart) {
        // For every item in the cart, render its amount and details
        var r = "";
        for (itemId in cart) {
            var item = cart[itemId];
            var html = "<div data-productid='" + itemId + "' class='cartitem contained mediumpadding smallmargin'><img src='" + item.src + "'></img>" +
                "<div class='orderlines'>";
                for(orderlineId in item.orderlines) {
                    var orderline = item.orderlines[orderlineId];
                    html += "<div data-orderlineid='" + orderlineId + "'>";
                        html += "Aantal: <span>" + orderline.amount + "</span> <button data-buttontype='decrease'>-</button><button data-buttontype='increase'>+</button> ";
                        html += "Type: <select>" + renderSelectOptions({'digitaal':'digitaal, volledig formaat', '10x15':'foto, 10 x 15 cm', '13x18':'foto, 13 x 18 cm', '20x30':'foto, 20 x 30 cm'}, orderline.size) + "</select>";
                        html += " <a data-buttontype='removeorderline' href='javascript:void(0);' title='bestelregel verwijderen'>weg</a>";
                    html += "</div>";
                }
                html += "<div class='mediummargin nobottommargin'><a data-buttontype='addorderline' href='javascript:void(0);'>voeg nog bestelregel toe voor deze foto</a></div>";
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
    
    function renderRightImage(thumbnail, thumbnailWidth) {
        var html = "<img src='" + $("img", thumbnail).attr("src").replace("/"+config.imageSize+"/", "/"+thumbnailWidth+"/") + "'></img>";
        html += "<div class='instruction mediumpadding'>Sluiten</div>";
        return html;
    }
    
    // Show a 'close this image' hover on the right image
    $(".component_imageshop .rightbar .largeimage").click(function(event) {
       $(this).fadeOut("100"); 
    }).
    // And hide the image if user clicks on it
    click(function(event) {
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