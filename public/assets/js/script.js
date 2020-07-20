function showMessage(message) {
  $('#messages').html(`
  <div class="alert alert-success" role="alert">
  ${message}
</div>
  `);
  msg = setTimeout(() => {
    $('#messages').html('');
  }, 2000);
}

function showError(error) {
  $('#messages').html(`
  <div class="alert alert-warning" role="alert">
  ${error}
</div>
  `);
  msg = setTimeout(() => {
    $('#messages').html('');
  }, 2000);
}

async function addItemToCart(event) {
  let bookid = $(this).data('bookid');
  let result = await axios({
      url: '/cart/add',
      method: 'post',
      data: {
        bookid: bookid,
      },
      withCredentials: true,
    })
    .then((res) => {
      return res.data;
    })
    .catch((err) => {
      showError('Có lỗi xảy ra trong quá trình thêm vào giỏ hàng');
    });
  if (result.status) {
    // showMessage('Đã thêm vào giỏ hàng');
    $('#cartPopup').focus();
  } else {
    showError('Không thể thêm vào giỏ hàng');
  }
  updateCartIdentity();
}
async function editItemQuantity(event) {
  let bookid = $(this).data('bookid');
  bookid = $(this).closest('.cartEditInputGroup').data('bookid');
  quantity = $(this).closest('.cartEditInputGroup').find('input[name="quantity"]').val();
  let result = await axios({
      url: '/cart/edit',
      method: 'post',
      data: {
        bookid: bookid,
        quantity: quantity,
      },
      withCredentials: true,
    })
    .then((res) => {
      return res.data;
    })
    .catch((err) => {
      showError('Có lỗi xảy ra trong quá trình chỉnh sửa giỏ hàng');
    });
  if (result.status) {
    showMessage('Đã sửa sách trong giỏ hàng');
    $('#totalMoney').text(result.totalMoney);
  } else {
    showError(res.message);
  }
  updateCartIdentity();
}

async function removeItemFromCart(event) {
  let bookid = $(this).data('bookid');
  let result = await axios({
      url: '/cart/remove',
      method: 'post',
      data: {
        bookid: bookid,
      },
      withCredentials: true,
    })
    .then((res) => {
      return res.data;
    })
    .catch((err) => {
      showError('Có lỗi xảy ra trong quá trình xoá khỏi giỏ hàng');
    });
  if (result.status) {
    $(this).closest('.card').remove();
    $('#totalMoney').text(result.totalMoney);
    if (result.totalMoney == 0) {
      $('<div class="alert alert-warning">Bạn không có sản phẩm nào trong giỏ hàng</div>').insertBefore(
        '.shopping-cart',
      );
      $('.shopping-cart').remove();
    } else {
      showMessage('Đã xoá khỏi giỏ hàng');
    }
  } else {
    showError(res.message);
  }
  updateCartIdentity();
}

async function removeAllItemFromCart() {
  let result = await axios({
      url: '/cart/removeAll',
      method: 'post',
      withCredentials: true,
    })
    .then((res) => {
      return res.data;
    })
    .catch((err) => {
      showError('Có lỗi xảy ra trong quá trình xoá giỏ hàng');
    });
  if (result.status) {
    $('<div class="alert alert-warning">Bạn không có sản phẩm nào trong giỏ hàng</div>').insertBefore('.shopping-cart');
    $('.shopping-cart').remove();
  }
  updateCartIdentity();
}

function updateCartIdentity() {
  //Sửa số trên biểu tượng giỏ hàng
  axios({
    method: 'get',
    url: '/cart/getJSON',
  }).then((res) => {
    $('#cartIdentity').text(res.data.total);
  });
}
$(document).ready(function () {
  //Thêm, sửa, xoá
  $('.add-to-cart').on('click', addItemToCart);
  $('.cartEditQuantity').on('click', editItemQuantity);
  $('.cartRemoveItem').on('click', removeItemFromCart);
  $('.cartRemoveAllItem').on('click', removeAllItemFromCart);
  //Cập nhật số trên biểu tượng giỏ hàng
  updateCartIdentity();
  $('#cartPopup').popover({
    trigger: 'focus',
  });
});

