function changeColor(pclrID) {
    console.log("Changing color scheme to: " + pclrID);

    //unselect all items
    $(".image_gallery .list .item").attr("active", "0");
    $(".color_chooser .value .color_button").attr("active", "0");

    var available_colors = $(".color_chooser .value .color_button").length;
    console.log("Available colors: " + available_colors);

    var color_button = $(".color_chooser .value .color_button[pclrID='" + pclrID + "']");
    color_button.attr("active", "1");

    var color_name = color_button.attr("color_name");
    $(".current_color .value").html(color_name);

    //hide single color schemes
    if (available_colors == 1 && !color_name) {
        $(".item.current_color").css("display", "none");

    }

    //available pids from this color
    piID = color_button.attr("piID");

    var size_values = color_button.attr("size_values");

    var size_chooser_model = $(".sizing_colors .item.size_chooser").attr("model");
    if (size_chooser_model == "size_button") {
        renderSizeChooserAsButtons(size_values);
    } else if (size_chooser_model == "select_box") {
        renderSizeChooserAsSelect(size_values);
    }

    //hide other galleries
    $(".image_gallery .list").css("display", "none");
    var active_gallery = $(".image_gallery .list[pclrID='" + pclrID + "']");
    active_gallery.css("display", "block");

    var first_item = active_gallery.find(".item").first(); //$(".image_gallery .list[pclrID='"+pclrID+"'] .item").first();

    changeImage(first_item);
    updatePrice();

}

function renderSizeChooserAsSelect(size_values) {

    var sizes = size_values ? size_values.split("|") : Array();

    //   console.log(sizes);
    //   console.log(sizes.length);

    var size_chooser = $(".sizing_colors .item.size_chooser .value .product_size");
    //
    size_chooser.parents(".item.size_chooser").first().css("display", "none");
    size_chooser.empty();

    for (var a = 0; a < sizes.length; a++) {
        size_chooser.append("<option>" + sizes[a] + "</option>");
    }

    if (sizes.length > 0) {
        size_chooser.parents(".item.size_chooser").first().css("display", "");
    }

}

function renderSizeChooserAsButtons(size_values) {

    var sizes = size_values ? size_values.split("|") : Array();

    //   console.log(sizes);
    //   console.log(sizes.length);

    var size_chooser = $(".sizing_colors .item.size_chooser .value");
    //
    size_chooser.parents(".item.size_chooser").first().css("display", "none");
    size_chooser.empty();

    for (var a = 0; a < sizes.length; a++) {
        size_chooser.append("<span class='size_button' onClick='javascript:changeSizing(this)'>" + sizes[a] + "</span>");
    }

    if (sizes.length > 0) {
        size_chooser.parents(".item.size_chooser").first().css("display", "");
        size_chooser.children(".size_button").first().attr("active", "1");
    }

}

function changeSizing(elm) {
    var size_button = $(".sizing_colors .item.size_chooser .value .size_button");
    size_button.attr("active", "0");

    $(elm).attr("active", "1");
    updatePrice();

}

//update the main image
function changeImage(elm) {
    //deselect all gallery items
    $(".image_gallery .list .item").attr("active", "0");

    var bean = $(elm).attr("bean");
    var id = $(elm).attr("itemID");
    var href_big = $(".image_big").attr("source");
    href_big += "&class=" + bean + "&id=" + id;

    $(".image_big IMG").attr("src", href_big);

    $(elm).attr("active", "1");

    var href_popup = $(".image_big A").attr("source");
    $(".image_big A").attr("href", href_popup + "&class=" + bean + "&id=" + id);

}

function updatePrice() {
    console.log("Update Price");

    var color_chooser = $(".color_chooser .value .color_button[active='1']");
    var prices = color_chooser.attr("sell_prices");
    var sell_prices = prices.split("|");

    var stock_amounts = color_chooser.attr("stock_amounts").split("|");

    var pid_values = color_chooser.attr("pids");
    var pids = pid_values.split("|");

    console.log("Prices: " + sell_prices);
    console.log("piIDs: " + pids);

    var size_chooser_model = $(".sizing_colors .item.size_chooser").attr("model");

    var pid = -1;
    var index = -1;
    var selected_price = -1;

    if (size_chooser_model == "select_box") {
        index = $(".size_chooser .value .product_size option:selected").index();
    } else if (size_chooser_model == "size_button") {
        $(".size_chooser .value .size_button").each(function (idx) {
            if ($(this).attr("active") == "1") {
                console.log("Selected size index: " + idx);
                index = idx;
            }

        });
    }

    if (index > -1) {
        selected_price = parseFloat(sell_prices[index]).toFixed(2);

        $(".sell_price .value").html(selected_price);

        pid = pids[index];

        $(".sell_price .value").attr("pid", pid);
        $(".stock_amount .value").html(stock_amounts[index]);
    } else {

        pid = parseInt($(".sell_price .value").attr("piID"));
        selected_price = parseFloat(sell_prices).toFixed(2);

        $(".sell_price .value").html(selected_price);
        $(".stock_amount .value").html(stock_amounts);

    }
    console.log("Selected price: " + sell_prices[index]);
    console.log("Selected piID: " + pid);

    var attrib = attributes[pid];
    var html = "";
    for (var idx = 0; idx < attrib.length; idx++) {
        var obj = attrib[idx];
        //sellable inventory has class attribute value filled in
        if (obj.value) {
            html += "<div class='item'>";
            html += "<label>" + obj.name + "</label>";
            html += "<span class='value'>" + obj.value + "</span>";
            html += "</div>";
        }
    }
    $(".attributes *").remove();
    $(".attributes").append(html);


}

function addToCart() {
    let selected_piID = $(".sell_price .value").attr("pid");
    let stock_amount = parseInt($(".stock_amount .value").html());
    console.log("Stock amount: " + stock_amount);
    if (stock_amount < 1) {
        showAlert("В момента няма наличност от този артикул");
    } else {
        let url = new URL(LOCAL+"/checkout/cart.php", location.href);
        url.searchParams.set("addItem", "");
        url.searchParams.set("piID", selected_piID);

        //window.location.href = LOCAL + "checkout/cart.php?addItem&piID=" + selected_piID;
        window.location.href=url.href;

    }


}
