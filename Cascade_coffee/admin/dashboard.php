<?php
require_once __DIR__ . '/../lib/auth.php';
require_admin();

$message = '';
$editingItem = null;

if (isset($_GET['edit'])) {
    $editingItem = find_menu_item(clean_text($_GET['edit']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = clean_text($_POST['action'] ?? '');
    $items = get_menu_items();

    if ($action === 'delete') {
        $id = clean_text($_POST['id'] ?? '');
        $items = array_values(array_filter($items, static fn ($item) => ($item['id'] ?? '') !== $id));
        save_menu_items($items);
        header('Location: dashboard.php?message=deleted');
        exit;
    }

    if ($action === 'toggle_availability') {
        $id = clean_text($_POST['id'] ?? '');
        foreach ($items as $index => $item) {
            if (($item['id'] ?? '') === $id) {
                $items[$index]['available'] = !($items[$index]['available'] ?? true);
                break;
            }
        }
        save_menu_items($items);
        header('Location: dashboard.php?message=toggled');
        exit;
    }

    if ($action === 'save') {
        $id = clean_text($_POST['id'] ?? '');
        $menuItem = [
            'id' => $id !== '' ? $id : uniqid('item_', true),
            'name' => clean_text($_POST['name'] ?? ''),
            'category' => clean_text($_POST['category'] ?? 'Food'),
            'price' => clean_text($_POST['price'] ?? ''),
            'description' => clean_text($_POST['description'] ?? ''),
            'available' => isset($_POST['available']),
        ];

        if ($id !== '') {
            foreach ($items as $index => $item) {
                if (($item['id'] ?? '') === $id) {
                    $items[$index] = $menuItem;
                    break;
                }
            }
        } else {
            $items[] = $menuItem;
        }

        save_menu_items($items);
        header('Location: dashboard.php?message=saved');
        exit;
    }
}

if (isset($_GET['message'])) {
    if ($_GET['message'] === 'deleted') {
        $message = 'Menu item deleted.';
    } elseif ($_GET['message'] === 'toggled') {
        $message = 'Item availability toggled.';
    } else {
        $message = 'Menu item saved.';
    }
}

$items = get_menu_items();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard | Cascade Coffee</title>
    <link rel="stylesheet" href="../styles/main.css" />
  </head>
  <body>
    <header class="admin-header">
      <a class="mini-brand" href="../index.php">
        <img src="../assets/cascade-coffee-logo.svg" alt="Cascade Coffee" />
      </a>
      <nav>
        <a href="dashboard.php">Menu</a>
        <a href="about.php">About Us</a>
        <a href="account.php">Account</a>
        <a href="logout.php">Logout</a>
      </nav>
    </header>

    <main class="admin-shell">
      <div class="admin-title">
        <p class="eyebrow">Admin</p>
        <h1>Manage Menu Lists</h1>
        <p>Create, edit, view, and delete available foods and drinks.</p>
      </div>

      <?php if ($message !== ''): ?>
        <p class="notice success"><?= e($message) ?></p>
      <?php endif; ?>

      <section class="admin-panel" aria-labelledby="formTitle">
        <h2 id="formTitle"><?= $editingItem ? 'Edit Item' : 'Create New Item' ?></h2>
        <form class="item-form" method="post">
          <input type="hidden" name="action" value="save" />
          <input type="hidden" name="id" value="<?= e($editingItem['id'] ?? '') ?>" />

          <label>
            Name
            <input type="text" name="name" value="<?= e($editingItem['name'] ?? '') ?>" required />
          </label>

          <label>
            Category
            <select name="category" required>
              <?php $category = $editingItem['category'] ?? 'Food'; ?>
              <option value="Food" <?= $category === 'Food' ? 'selected' : '' ?>>Food</option>
              <option value="Drink" <?= $category === 'Drink' ? 'selected' : '' ?>>Drink</option>
              <option value="Specials" <?= $category === 'Specials' ? 'selected' : '' ?>>Specials</option>
            </select>
          </label>

          <label>
            Price ETB
            <input type="number" min="0" step="1" name="price" value="<?= e($editingItem['price'] ?? '') ?>" required />
          </label>

          <label class="wide">
            Description
            <textarea name="description" rows="4" required><?= e($editingItem['description'] ?? '') ?></textarea>
          </label>

          <label class="check-label">
            <input type="checkbox" name="available" <?= ($editingItem['available'] ?? true) ? 'checked' : '' ?> />
            Available to customers
          </label>

          <div class="form-actions">
            <button class="button" type="submit"><?= $editingItem ? 'Update Item' : 'Add Item' ?></button>
            <?php if ($editingItem): ?>
              <a class="text-link" href="dashboard.php">Cancel edit</a>
            <?php endif; ?>
          </div>
        </form>
      </section>

      <section class="admin-panel" aria-labelledby="listTitle">
        <h2 id="listTitle">Available List</h2>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $item): ?>
                <tr>
                  <td><?= e($item['name'] ?? '') ?></td>
                  <td><?= e($item['category'] ?? '') ?></td>
                  <td><?= e($item['price'] ?? '') ?> ETB</td>
                  <td><?= ($item['available'] ?? false) ? 'Available' : 'Hidden' ?></td>
                  <td class="table-actions">
                    <a class="small-button" href="dashboard.php?edit=<?= e($item['id'] ?? '') ?>">Edit</a>
                    <form method="post">
                      <input type="hidden" name="action" value="toggle_availability" />
                      <input type="hidden" name="id" value="<?= e($item['id'] ?? '') ?>" />
                      <button class="small-button toggle" type="submit"><?= ($item['available'] ?? false) ? 'Make Unavailable' : 'Make Available' ?></button>
                    </form>
                    <form method="post" onsubmit="return confirm('Delete this item?');">
                      <input type="hidden" name="action" value="delete" />
                      <input type="hidden" name="id" value="<?= e($item['id'] ?? '') ?>" />
                      <button class="small-button danger" type="submit">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>
    <script src="../scripts/main.js"></script>
  </body>
</html>
