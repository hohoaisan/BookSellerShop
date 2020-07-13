$(document).ready(function () {
    $('#add_to_cart').click(function () {
        var bookid = $(this).data('bookid');
        var bookname = $(this).data('bookname');
        var price = $(this).data('price');
        var bookimageurl = $(this).data('bookimageurl');
        var action = "add";
        $.ajax({
            url: "/models/home.php",
            method: "POST",
            data: {
                bookid: bookid,
                bookname: bookname,
                price: price,
                bookimageurl: bookimageurl,
                action: action
            },
            success: function (data) {
                alert("Item has been Added into Cart");
            }
        });
    });
});
