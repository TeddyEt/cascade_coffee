<?php
require_once __DIR__ . '/../lib/auth.php';
require_admin();

$message = '';
$about = get_about();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = clean_text($_POST['action'] ?? '');

    if ($action === 'save_description') {
        $about['description'] = $_POST['description'] ?? '';
        save_about($about);
        header('Location: about.php?message=description_saved');
        exit;
    }

    if ($action === 'save_contact') {
        $about['address'] = $_POST['address'] ?? '';
        $about['phone'] = $_POST['phone'] ?? '';
        save_about($about);
        header('Location: about.php?message=contact_saved');
        exit;
    }

    if ($action === 'add_image') {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../assets/about-images/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0775, true);
            }

            $file_name = uniqid('cafe_', true) . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                $about['images'] = $about['images'] ?? [];
                $about['images'][] = [
                    'id' => uniqid('img_', true),
                    'url' => 'assets/about-images/' . $file_name,
                    'alt' => clean_text($_POST['image_alt'] ?? 'Cafe image'),
                ];
                save_about($about);
                header('Location: about.php?message=image_added');
                exit;
            }
        }
    }

    if ($action === 'delete_image') {
        $image_id = clean_text($_POST['image_id'] ?? '');
        $about['images'] = array_values(array_filter($about['images'] ?? [], static fn ($img) => ($img['id'] ?? '') !== $image_id));
        save_about($about);
        header('Location: about.php?message=image_deleted');
        exit;
    }

    if ($action === 'add_social') {
        $about['socials'] = $about['socials'] ?? [];
        $about['socials'][] = [
            'id' => uniqid('social_', true),
            'name' => clean_text($_POST['social_name'] ?? ''),
            'url' => clean_text($_POST['social_url'] ?? ''),
        ];
        save_about($about);
        header('Location: about.php?message=social_added');
        exit;
    }

    if ($action === 'update_social') {
        $social_id = clean_text($_POST['social_id'] ?? '');
        foreach ($about['socials'] ?? [] as $index => $social) {
            if (($social['id'] ?? '') === $social_id) {
                $about['socials'][$index] = [
                    'id' => $social_id,
                    'name' => clean_text($_POST['social_name'] ?? ''),
                    'url' => clean_text($_POST['social_url'] ?? ''),
                ];
                break;
            }
        }
        save_about($about);
        header('Location: about.php?message=social_updated');
        exit;
    }

    if ($action === 'delete_social') {
        $social_id = clean_text($_POST['social_id'] ?? '');
        $about['socials'] = array_values(array_filter($about['socials'] ?? [], static fn ($social) => ($social['id'] ?? '') !== $social_id));
        save_about($about);
        header('Location: about.php?message=social_deleted');
        exit;
    }
}

if (isset($_GET['message'])) {
    $messages = [
        'description_saved' => 'Description updated successfully.',
        'contact_saved' => 'Contact information updated successfully.',
        'image_added' => 'Image added successfully.',
        'image_deleted' => 'Image deleted successfully.',
        'social_added' => 'Social media added successfully.',
        'social_updated' => 'Social media updated successfully.',
        'social_deleted' => 'Social media deleted successfully.',
    ];
    $message = $messages[$_GET['message']] ?? '';
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage About Us | Cascade Coffee</title>
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
        <h1>Manage About Us</h1>
        <p>Edit description, manage images, and update social media links.</p>
      </div>

      <?php if ($message !== ''): ?>
        <p class="notice success"><?= e($message) ?></p>
      <?php endif; ?>

      <!-- Description Section -->
      <section class="admin-panel" aria-labelledby="descTitle">
        <h2 id="descTitle">About Description</h2>
        <form class="item-form single" method="post">
          <input type="hidden" name="action" value="save_description" />
          <label class="wide">
            Description
            <textarea name="description" rows="5" required><?= e($about['description'] ?? '') ?></textarea>
          </label>
          <div class="form-actions">
            <button class="button" type="submit">Save Description</button>
          </div>
        </form>
      </section>

      <!-- Contact Section -->
      <section class="admin-panel" aria-labelledby="contactTitle">
        <h2 id="contactTitle">Contact Information</h2>
        <form class="item-form" method="post">
          <input type="hidden" name="action" value="save_contact" />
          <label>
            Address
            <input type="text" name="address" value="<?= e($about['address'] ?? '') ?>" required />
          </label>
          <label>
            Phone Number
            <input type="tel" name="phone" value="<?= e($about['phone'] ?? '') ?>" required />
          </label>
          <div class="form-actions" style="grid-column: 1 / -1;">
            <button class="button" type="submit">Save Contact Info</button>
          </div>
        </form>
      </section>

      <!-- Images Section -->
      <section class="admin-panel" aria-labelledby="imagesTitle">
        <h2 id="imagesTitle">Cafe Images</h2>
        
        <form class="item-form single" method="post" enctype="multipart/form-data">
          <input type="hidden" name="action" value="add_image" />
          <label>
            Upload Image
            <input type="file" name="image" accept="image/*" required />
          </label>
          <label>
            Image Description (Alt Text)
            <input type="text" name="image_alt" placeholder="e.g., Main seating area" />
          </label>
          <div class="form-actions">
            <button class="button" type="submit">Add Image</button>
          </div>
        </form>

        <?php if (!empty($about['images'] ?? [])): ?>
          <h3 style="margin-top: 24px; font-size: 18px;">Current Images</h3>
          <div class="images-list">
            <?php foreach ($about['images'] as $image): ?>
              <div class="image-item">
                <img src="<?= e($image['url'] ?? '') ?>" alt="<?= e($image['alt'] ?? '') ?>" style="max-width: 150px; height: auto; border-radius: 6px;" />
                <div style="margin-top: 8px;">
                  <p style="margin: 0 0 8px; font-size: 13px; color: var(--muted);">Alt: <?= e($image['alt'] ?? 'No description') ?></p>
                  <form method="post" style="display: inline;">
                    <input type="hidden" name="action" value="delete_image" />
                    <input type="hidden" name="image_id" value="<?= e($image['id'] ?? '') ?>" />
                    <button class="small-button danger" type="submit" onclick="return confirm('Delete this image?');">Delete</button>
                  </form>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>

      <!-- Social Media Section -->
      <section class="admin-panel" aria-labelledby="socialsTitle">
        <h2 id="socialsTitle">Social Media Links</h2>
        
        <form class="item-form" method="post">
          <input type="hidden" name="action" value="add_social" />
          <label>
            Social Media Name
            <input type="text" name="social_name" placeholder="e.g., Facebook" required />
          </label>
          <label class="wide">
            Link URL
            <input type="url" name="social_url" placeholder="https://facebook.com/your-page" required />
          </label>
          <div class="form-actions">
            <button class="button" type="submit">Add Social Media</button>
          </div>
        </form>

        <?php if (!empty($about['socials'] ?? [])): ?>
          <h3 style="margin-top: 24px; font-size: 18px;">Current Social Media</h3>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>URL</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($about['socials'] as $social): ?>
                  <tr>
                    <td><?= e($social['name'] ?? '') ?></td>
                    <td><?= e($social['url'] ?? '') ?></td>
                    <td class="table-actions">
                      <button class="small-button" type="button" onclick="editSocial('<?= e($social['id'] ?? '') ?>', '<?= e($social['name'] ?? '') ?>', '<?= e($social['url'] ?? '') ?>')">Edit</button>
                      <form method="post" style="display: inline;">
                        <input type="hidden" name="action" value="delete_social" />
                        <input type="hidden" name="social_id" value="<?= e($social['id'] ?? '') ?>" />
                        <button class="small-button danger" type="submit" onclick="return confirm('Delete this social media link?');">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </section>

      <!-- Edit Social Modal (hidden by default) -->
      <div id="editSocialModal" hidden>
        <div class="modal-overlay" onclick="closeSocialModal()"></div>
        <div class="modal-content">
          <form method="post" class="item-form">
            <input type="hidden" name="action" value="update_social" />
            <input type="hidden" name="social_id" id="editSocialId" />
            <label>
              Social Media Name
              <input type="text" name="social_name" id="editSocialName" required />
            </label>
            <label class="wide">
              Link URL
              <input type="url" name="social_url" id="editSocialUrl" required />
            </label>
            <div class="form-actions">
              <button class="button" type="submit">Update Social Media</button>
              <button class="text-link" type="button" onclick="closeSocialModal()">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </main>

    <script>
      function editSocial(id, name, url) {
        document.getElementById('editSocialId').value = id;
        document.getElementById('editSocialName').value = name;
        document.getElementById('editSocialUrl').value = url;
        document.getElementById('editSocialModal').hidden = false;
      }

      function closeSocialModal() {
        document.getElementById('editSocialModal').hidden = true;
      }

      window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          closeSocialModal();
        }
      });
    </script>
    <script src="../scripts/main.js"></script>
  </body>
</html>
