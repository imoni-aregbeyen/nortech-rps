<?php
$tbl = 'sections';
$createTableSql = "CREATE TABLE IF NOT EXISTS $tbl (
  id INT AUTO_INCREMENT PRIMARY KEY,
  section_name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
if ($conn->query($createTableSql) !== TRUE) {
  die('Error creating school_section table: ' . $conn->error);
}

// Handle add/edit form submission
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['delete_id'])) {
    // Handle delete
    $delete_id = intval($_POST['delete_id']);
    $conn->query("DELETE FROM $tbl WHERE id=$delete_id");
    header('Location: sections.php');
    exit;
  } elseif (!empty($_POST['section_name'])) {
    $section_name = $conn->real_escape_string(trim($_POST['section_name']));
    if ($section_name !== '') {
      if ($edit_id) {
        // Update
        $updateSql = "UPDATE $tbl SET section_name='$section_name' WHERE id=$edit_id";
        if ($conn->query($updateSql) === TRUE) {
          header('Location: ./?page=sections');
          exit;
        } else {
          echo '<div class="alert alert-danger">Error updating section: ' . $conn->error . '</div>';
        }
      } else {
        // Insert
        $insertSql = "INSERT IGNORE INTO $tbl (section_name) VALUES ('$section_name')";
        if ($conn->query($insertSql) === TRUE) {
          header('Location: sections.php');
          exit;
        } else {
          echo '<div class="alert alert-danger">Error adding section: ' . $conn->error . '</div>';
        }
      }
    }
  }
}

// Fetch all sections
$sections = [];
$result = $conn->query("SELECT * FROM $tbl ORDER BY id ASC");
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $sections[] = $row;
  }
}

// If editing, fetch the section
$edit_section = null;
if ($edit_id) {
  $edit_result = $conn->query("SELECT * FROM $tbl WHERE id=$edit_id");
  if ($edit_result && $edit_result->num_rows > 0) {
    $edit_section = $edit_result->fetch_assoc();
  }
}
?>
<div class="container p-5">
  <div class="row">
    <div class="col-lg-6">
      <form action="" method="post" class="bg-white rounded shadow p-4 mb-4">
        <h2 class="mb-4 text-primary"><?= $edit_section ? 'Edit Section' : 'Add Section' ?></h2>
        <div class="mb-3">
          <label for="sectionName" class="form-label fw-bold">Section Name</label>
          <input type="text" class="form-control" id="sectionName" name="section_name" placeholder="e.g. Nursery, Primary" value="<?= $edit_section ? htmlspecialchars($edit_section['section_name']) : '' ?>" required>
        </div>
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary btn-lg"><?= $edit_section ? 'Update Section' : 'Add Section' ?></button>
          <?php if ($edit_section): ?>
            <a href="sections.php" class="btn btn-secondary btn-lg">Cancel</a>
          <?php endif; ?>
        </div>
      </form>
    </div>
    <div class="col-lg-6">
      <div class="bg-light rounded shadow p-4">
        <h4 class="mb-3">Existing Sections</h4>
        <ul class="list-group">
          <?php foreach ($sections as $section): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span><?= htmlspecialchars($section['section_name']) ?></span>
              <span>
                <a href="./?page=sections&edit=<?= $section['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <form action="" method="post" style="display:inline;" onsubmit="return confirm('Delete this section?');">
                  <input type="hidden" name="delete_id" value="<?= $section['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
              </span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</div>
