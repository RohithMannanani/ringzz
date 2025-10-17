<?php
require_once 'config.php';
require_once 'classes/Cart.php';
$cart = new Cart();
$items = $cart->getItems();
$total = $cart->getTotal();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Cart - Ringzz</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <style>
    .item{display:flex;gap:12px;border-bottom:1px solid #eee;padding:12px;align-items:center;}
    .item img{width:80px;height:80px;object-fit:cover;}
    .btn{padding:6px 12px;background:#007bff;color:#fff;border-radius:6px;border:none;cursor:pointer;}
    .btn-danger{background:#dc3545;}
    .qty-btn{padding:4px 8px;margin:0 2px;cursor:pointer;}
    .total{font-weight:bold;margin-top:15px;}
  </style>
</head>
<body>
<h1>Your Cart</h1>
<div id="items">
  <?php foreach($items as $it): ?>
    <div class="item" data-id="<?= $it['id'] ?>">
      <img src="<?= !empty($it['image']) ? htmlspecialchars("./classes/".$it['image']) : 'uploads/placeholder.jpg' ?>" alt="<?= htmlspecialchars($it['name']) ?>">
      <div style="flex:1;">
        <strong><?= htmlspecialchars($it['name']) ?></strong><br>
        ₹ <?= number_format($it['price'],2) ?> | 
        Subtotal: ₹ <span class="subtotal"><?= number_format($it['subtotal'],2) ?></span><br>
        Qty: 
        <button class="qty-btn decrease">-</button>
        <span class="qty"><?= $it['qty'] ?></span>
        <button class="qty-btn increase">+</button>
      </div>
      <button class="btn btn-danger remove">Remove</button>
    </div>
  <?php endforeach; ?>
</div>

<h3 class="total">Total: ₹ <span id="total-rupees"><?= number_format($total,2) ?></span></h3>
<button id="checkoutBtn" class="btn">Checkout with Razorpay</button>

<script>
function updateCartCount(){
    $.get('api/cart.php?action=count', function(res){
        if(document.getElementById('cart-count')){
            $('#cart-count').text(res.count||0);
        }
    }, 'json');
}

function updateCartDisplay(data) {
    const itemsContainer = $('#items');
    itemsContainer.empty(); // Clear the current cart display

    if (data.items && data.items.length > 0) {
        data.items.forEach(item => {
            const itemHtml = `
                <div class="item" data-id="${item.id}">
                    <img src="${item.image ? './classes/' + item.image : 'uploads/placeholder.jpg'}" alt="${item.name}">
                    <div style="flex:1;">
                        <strong>${item.name}</strong><br>
                        ₹ ${parseFloat(item.price).toFixed(2)} |
                        Subtotal: ₹ <span class="subtotal">${item.subtotal.toFixed(2)}</span><br>
                        Qty:
                        <button class="qty-btn decrease">-</button>
                        <span class="qty">${item.qty}</span>
                        <button class="qty-btn increase">+</button>
                    </div>
                    <button class="btn btn-danger remove">Remove</button>
                </div>
            `;
            itemsContainer.append(itemHtml);
        });
    } else {
        itemsContainer.html('<p>Your cart is empty.</p>');
    }

    $('#total-rupees').text(data.total.toFixed(2));
    updateCartCount();
}

// increase quantity
$(document).on('click', '.increase', function(){
    let $parent = $(this).closest('.item');
    let id = $parent.data('id');
    $.post('api/cart.php?action=add', {product_id: id, qty:1}, function(res){
        if(res.success){
            $.get('api/cart.php?action=get', function(data){
                updateCartDisplay(data);
            }, 'json');
            updateCartCount();
        }
    }, 'json');
});

// decrease quantity
$(document).on('click', '.decrease', function(){
    let $parent = $(this).closest('.item');
    let id = $parent.data('id');
    $.post('api/cart.php?action=remove_one', {product_id: id}, function(res){
        if(res.success){
            $.get('api/cart.php?action=get', function(data){
                updateCartDisplay(data);
            }, 'json');
            updateCartCount();
        }
    }, 'json');
});

// remove item completely
$(document).on('click', '.remove', function(){
    let $parent = $(this).closest('.item');
    let id = $parent.data('id');
    $.post('api/cart.php?action=remove', {product_id: id}, function(res){
        if(res.success){
            $parent.remove();
            $.get('api/cart.php?action=get', function(data){
                $('#total-rupees').text(data.total.toFixed(2));
            }, 'json');
            updateCartCount();
        }
    }, 'json');
});

// Razorpay Checkout
$('#checkoutBtn').on('click', function(){
    $.post('api/create_order.php', {}, function(resp){
        if(resp.error){ alert(resp.error); return; }
        const options = {
          "key": resp.key_id,
          "amount": resp.amount,
          "currency": resp.currency,
          "name": "Ringzz",
          "description": "Order payment",
          "order_id": resp.id,
          "handler": function (paymentResponse){
              $.post('api/verify_payment.php', paymentResponse, function(verifyResp){
                  if(verifyResp.success){
                      alert('Payment successful!');
                      window.location.href = 'success.php?order_id=' + verifyResp.local_order_id;
                  } else alert('Payment verification failed');
              }, 'json');
          }
        };
        const rzp = new Razorpay(options);
        rzp.open();
    }, 'json');
});

$(document).ready(function(){
    updateCartCount();
});
</script>
</body>
</html>
