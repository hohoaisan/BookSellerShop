
$(document).ready(function () {
    $(".add-to-cart").on('click', async function(event){
        
        let bookid = $(this).data('bookid');
        let result = await axios({
            url: '/cart/add',
            method: 'post',
            data: {
                bookid: bookid
            },
            withCredentials: true,
        }).then((res) => {
            return res.data;
        }).catch((err) => {
            alert("Có lỗi xảy ra trong quá trình thêm vào giỏ hàng");
        });
        if (result.status) {
            alert("Đã thêm vào giỏ hàng");
        }
        else {
            alert("Không thể thêm vào giỏ hàng");
        }
        
    });

    $(".cartEditQuantity").on('click', function(event) {
        let input = event.target.parentElement;
        console.log(input);
        alert("edit");
    });
    $(".cartRemoveItem").on('click', function(event) {
        let input = event.target.parentElement;
        console.log(input);
        alert("remove");
    });

   
});
