<?php
require_once '../../config.php';
require_once '../../classes/Product.php';

$product = new Product();
$msg = "";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    $imagePath = null;
    if(!empty($_FILES['image']['name'])){
        $targetDir = "../uploads/";
        if(!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $filename = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $filename;
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];

        if(in_array($ext, $allowed)){
            if(move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)){
                $imagePath = "uploads/" . $filename;
            } else {
                $msg = "❌ Failed to upload image.";
            }
        } else {
            $msg = "❌ Invalid image type.";
        }
    }

    if(!$msg){
        $ok = $product->add($name, $description, $price, $stock, $imagePath);
        if($ok){
            $msg = "✅ Ring added successfully!";
        } else {
            $msg = "❌ Database error while adding ring.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

<title>Add New Ring - Admin | Ringzz</title>
<style>
body { font-family: Arial; background:#f5f5f5; margin:0; padding:20px; }
form { background:#fff; padding:20px; border-radius:10px; width:400px; margin:auto; box-shadow:0 0 10px rgba(0,0,0,0.1); }
h2 { text-align:center; color:#333; }
label { display:block; margin-top:10px; }
input, textarea { width:100%; padding:8px; margin-top:5px; }
button { margin-top:15px; padding:10px; background:#007bff; color:#fff; border:none; border-radius:5px; cursor:pointer; width:100%; }
button:hover { background:#0056b3; }
.message { text-align:center; padding:10px; margin-bottom:15px; }
.success { color:green; }
.error { color:red; }
</style>
</head>
<body>
<?php include '../../header.php'; ?>
  <h2>Add New Ring</h2>
  <?php if($msg): ?>
    <div class="message <?= strpos($msg,'✅')!==false ? 'success' : 'error' ?>"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>
  <form method="POST" enctype="multipart/form-data">
    <label>Ring Name</label>
    <input type="text" name="name" required>

    <label>Description</label>
    <textarea name="description" rows="3" required></textarea>

    <label>Price (₹)</label>
    <input type="number" name="price" step="0.01" required>

    <label>Stock</label>
    <input type="number" name="stock" required value="10">

    <label>Image</label>
    <input type="file" name="image" accept="image/*" required>

    <button type="submit">Add Ring</button>
  </form>
</body>
</html>
