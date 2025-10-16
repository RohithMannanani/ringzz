<?php
require_once 'config.php';
require_once 'classes/Product.php';
$productModel = new Product();
$products = $productModel->getAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Ringzz - Rings</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    /* minimal styles */
    .grid { display:flex; flex-wrap:wrap; gap:16px; }
    .card{border:1px solid #ddd;padding:12px;width:220px;border-radius:8px;}
    .card img{width:100%;height:160px;object-fit:cover}
    .btn{display:inline-block;padding:8px 12px;border-radius:5px;background:#007bff;color:#fff;text-decoration:none;cursor:pointer}
    .cart-link{position:fixed;right:20px;top:72px;z-index:1100}
  </style>
</head>
<body>
  <?php include 'header.php'; ?>
  <a class="btn cart-link" href="cart.php">Cart (<span id="cart-count">0</span>)</a>
  <h1>Ringzz — Rings</h1>
  <div class="grid">
    <?php foreach($products as $p): ?>
      <div class="card">
        <img src="<?= htmlspecialchars("./classes/".$p['image']?:'placeholder.jpg') ?>" alt="">
        <h3><?= htmlspecialchars($p['name']) ?></h3>
        <p><?= htmlspecialchars(substr($p['description'],0,80)) ?>...</p>
        <p>₹ <?= number_format($p['price'],2) ?></p>
        <button class="btn add-to-cart" data-id="<?= $p['id'] ?>">Add to cart</button>
      </div>
    <?php endforeach; ?>
  </div>

<script>
function updateCartCount(){
  $.get('api/cart.php?action=count', function(res){
    $('#cart-count').text(res.count||0);
  }, 'json');
}

$(document).ready(function(){
  updateCartCount();
  $('.cart-link').click(function(e){
  <?php if (empty($_SESSION['user_id'])): ?>
    e.preventDefault(); // ⛔ stop link from opening cart.php
    alert('Please login');
    window.location.href = '/ringzz/classes/login.php';
    return;
  <?php endif; ?>
});
  $('.add-to-cart').click(function(){
    <?php if (empty($_SESSION['user_id'])): ?>
      alert('Please login to add items to cart');
      window.location.href = 'classes/login.php';
      return;
    <?php endif; ?>
    

    const id = $(this).data('id');
    $.post('api/cart.php?action=add', { product_id: id, qty: 1 }, null, 'json')
      .done(function(res) {
        console.log('Success response:', res); // Log success response
        if (res.success) {
          $('#cart-count').text(res.count || 0);
          // alert('Added to cart');
        } else {
          alert(res.error || 'An unknown error occurred.');
        }
      })
      .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX Error:', textStatus, errorThrown); // Log any AJAX error
        console.error('Server Response Text:', jqXHR.responseText);
        alert('Error communicating with the server. Please check the console for details.');
      });
  });

});

</script>
</body>
</html>
