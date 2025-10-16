<?php
// api/cart.php
require_once '../config.php';
require_once '../classes/Cart.php';

// Check if user is logged in for cart operations that modify the cart
$action = $_GET['action'] ?? $_POST['action'] ?? ($_GET['action'] ?? null);
$modifying_actions = ['add', 'remove', 'remove_one'];

if (in_array($action, $modifying_actions) && empty($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Please login to manage cart']);
    exit;
}

$cart = new Cart();
$action = $_GET['action'] ?? $_POST['action'] ?? ($_GET['action'] ?? null);

header('Content-Type: application/json');

switch($action) {
    case 'count':
        $items = $cart->getItems();
        $count = 0;
        foreach($items as $it) $count += $it['qty'];
        echo json_encode(['count' => $count]);
        break;

    case 'get':
        echo json_encode(['items' => $cart->getItems(), 'total' => $cart->getTotal()]);
        break;

    case 'add':
        $pid = intval($_POST['product_id']);
        $qty = intval($_POST['qty'] ?? 1);
        $cart->add($pid, $qty);
        $items = $cart->getItems();
        $count = 0;
        foreach($items as $it) $count += $it['qty'];
        echo json_encode(['success' => true, 'count' => $count]);
        break;

    case 'remove':
        $pid = intval($_POST['product_id']);
        $cart->remove($pid);
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}

