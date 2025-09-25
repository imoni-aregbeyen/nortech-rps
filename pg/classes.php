<?php
// Create classes table if not exists
$tbl = 'classes';
$sectionTbl = 'sections';
$createTableSql = "CREATE TABLE IF NOT EXISTS $tbl (
  id INT AUTO_INCREMENT PRIMARY KEY,
  class_name VARCHAR(100) NOT NULL,
  section_id INT NOT NULL,
  UNIQUE KEY unique_class_section (class_name, section_id),
  FOREIGN KEY (section_id) REFERENCES $sectionTbl(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
if ($conn->query($createTableSql) !== TRUE) {
  die('Error creating classes table: ' . $conn->error);
}

// Fetch all sections for dropdown
$sections = [];
$result = $conn->query("SELECT * FROM $sectionTbl ORDER BY section_name ASC");
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $sections[] = $row;
  }
}

// Handle add/edit/delete
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $conn->query("DELETE FROM $tbl WHERE id=$delete_id");
  header('Location: ./?page=classes');
    exit;
  } elseif (!empty($_POST['class_name']) && !empty($_POST['section_id'])) {
    $class_name = $conn->real_escape_string(trim($_POST['class_name']));
    $section_id = intval($_POST['section_id']);
    if ($edit_id) {
      $updateSql = "UPDATE $tbl SET class_name='$class_name', section_id=$section_id WHERE id=$edit_id";
      if ($conn->query($updateSql) === TRUE) {
  header('Location: ./?page=classes');
        exit;
      } else {
        echo '<div class="alert alert-danger">Error updating class: ' . $conn->error . '</div>';
      }
    } else {
      $insertSql = "INSERT INTO $tbl (class_name, section_id) VALUES ('$class_name', $section_id)";
      if ($conn->query($insertSql) === TRUE) {
  header('Location: ./?page=classes');
        exit;
      } else {
        echo '<div class="alert alert-danger">Error adding class: ' . $conn->error . '</div>';
      }
    }
  }
}

// Fetch all classes with section name
$classes = [];
$result = $conn->query("SELECT c.*, s.section_name FROM $tbl c JOIN $sectionTbl s ON c.section_id = s.id ORDER BY s.section_name, c.class_name");
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
  }
}

// If editing, fetch the class
$edit_class = null;
if ($edit_id) {
  $edit_result = $conn->query("SELECT * FROM $tbl WHERE id=$edit_id");
  if ($edit_result && $edit_result->num_rows > 0) {
    $edit_class = $edit_result->fetch_assoc();
  }
}
?>
<div class="container p-5">
  <div class="row">
    <div class="col-lg-6">
      <form action="" method="post" class="bg-white rounded shadow p-4 mb-4">
        <h2 class="mb-4 text-primary"><?= $edit_class ? 'Edit Class' : 'Add Class' ?></h2>
        <div class="mb-3">
          <label for="className" class="form-label fw-bold">Class Name</label>
          <input type="text" class="form-control" id="className" name="class_name" placeholder="e.g. Primary 1" value="<?= $edit_class ? htmlspecialchars($edit_class['class_name']) : '' ?>" required>
        </div>
        <div class="mb-3">
          <label for="sectionId" class="form-label fw-bold">Section</label>
          <select class="form-select" id="sectionId" name="section_id" required>
            <option value="">Select Section</option>
            <?php foreach ($sections as $section): ?>
              <option value="<?= $section['id'] ?>" <?= $edit_class && $edit_class['section_id'] == $section['id'] ? 'selected' : '' ?>><?= htmlspecialchars($section['section_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary btn-lg"><?= $edit_class ? 'Update Class' : 'Add Class' ?></button>
          <?php if ($edit_class): ?>
            <a href="classes.php" class="btn btn-secondary btn-lg">Cancel</a>
          <?php endif; ?>
        </div>
      </form>
    </div>
    <div class="col-lg-6">
      <div class="bg-light rounded shadow p-4">
        <h4 class="mb-3">Existing Classes</h4>
        <ul class="list-group">
          <?php foreach ($classes as $class): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>
                <strong><?= htmlspecialchars($class['class_name']) ?></strong>
                <small class="text-muted">(<?= htmlspecialchars($class['section_name']) ?>)</small>
              </span>
              <span>
                <a href="./?page=classes&edit=<?= $class['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <form action="" method="post" style="display:inline;" onsubmit="return confirm('Delete this class?');">
                  <input type="hidden" name="delete_id" value="<?= $class['id'] ?>">
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
