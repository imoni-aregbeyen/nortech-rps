<div class="container p-5">
  <?php
  $tbl = 'school_info';
  $createTableSql = "CREATE TABLE IF NOT EXISTS $tbl (
    id INT AUTO_INCREMENT PRIMARY KEY,
    school_logo VARCHAR(255) DEFAULT NULL,
    school_name VARCHAR(255) NOT NULL,
    school_address VARCHAR(255) NOT NULL,
    school_phone TEXT NOT NULL,
    school_email VARCHAR(255) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
  if ($conn->query($createTableSql) !== TRUE) {
    die('Error creating school_info table: ' . $conn->error);
  }
  $sql = "SELECT * FROM $tbl LIMIT 1";
  $result = $conn->query($sql);
  if ($result->num_rows === 0) {
    // Insert dummy/null data for the first record
    $insertSql = "INSERT INTO $tbl (school_logo, school_name, school_address, school_phone, school_email) VALUES (NULL, '', '', '', '')";
    $conn->query($insertSql);
    // Re-query to get the inserted record
    $result = $conn->query($sql);
  }
  $data = $result->num_rows > 0 ? $result->fetch_assoc() : null;

  // Handle form submission for update
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $school_name = $conn->real_escape_string($_POST['school_name'] ?? '');
    $school_address = $conn->real_escape_string($_POST['school_address'] ?? '');
    $school_phone = $conn->real_escape_string($_POST['school_phone'] ?? '');
    $school_email = $conn->real_escape_string($_POST['school_email'] ?? '');

    // Handle logo upload if a file is provided
    $logoFileName = $data['school_logo'];
    if (isset($_FILES['school_logo']) && $_FILES['school_logo']['error'] === UPLOAD_ERR_OK) {
      $uploadDir = __DIR__ . '/../uploads/';
      if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
      }
      $fileTmp = $_FILES['school_logo']['tmp_name'];
      $fileName = basename($_FILES['school_logo']['name']);
      $targetFile = $uploadDir . $fileName;
      if (move_uploaded_file($fileTmp, $targetFile)) {
        $logoFileName = 'uploads/' . $fileName;
      }
    }

    $updateSql = "UPDATE $tbl SET school_logo='$logoFileName', school_name='$school_name', school_address='$school_address', school_phone='$school_phone', school_email='$school_email' WHERE id=" . $data['id'];
    if ($conn->query($updateSql) === TRUE) {
      // Refresh $data after update
      $result = $conn->query($sql);
      $data = $result->num_rows > 0 ? $result->fetch_assoc() : null;
      echo '<div class="alert alert-success">Settings updated successfully.</div>';
    } else {
      echo '<div class="alert alert-danger">Error updating settings: ' . $conn->error . '</div>';
    }
  }
  ?>
  <div class="row">
    <div class="col-lg-6">
      <form action="" method="post" enctype="multipart/form-data" class="bg-white rounded shadow p-4">
        <h2 class="mb-4 text-primary">School Information</h2>
        <div class="row g-3 align-items-center mb-3">
          <div class="col-auto">
            <label for="schoolLogo" class="form-label fw-bold">School Logo</label>
            <input class="form-control" type="file" id="schoolLogo" name="school_logo" accept="image/*" onchange="previewLogo(event)">
          </div>
          <div class="col-auto">
            <?php
              $logoSrc = '#';
              $logoDisplay = 'none';
              if (!empty($data['school_logo'])) {
                $logoSrc = './' . $data['school_logo'];
                $logoDisplay = 'block';
              }
            ?>
            <img id="logoPreview" src="<?= htmlspecialchars($logoSrc) ?>" alt="Logo Preview" style="display:<?= $logoDisplay ?>; max-width: 100px; max-height: 100px; border:1px solid #ccc; border-radius:8px;" />
          </div>
        </div>
        <div class="mb-3">
          <label for="schoolName" class="form-label fw-bold">School Name</label>
          <input type="text" class="form-control" id="schoolName" name="school_name" value="<?= $data['school_name'] ?>" required>
        </div>
        <div class="mb-3">
          <label for="schoolAddress" class="form-label fw-bold">School Address</label>
          <input type="text" class="form-control" id="schoolAddress" name="school_address" value="<?= $data['school_address'] ?>" required>
        </div>
        <div class="mb-3">
          <label for="schoolPhone" class="form-label fw-bold">School Phone</label>
          <input type="tel" class="form-control" id="schoolPhone" name="school_phone" value="<?= $data['school_phone'] ?>" required>
        </div>
        <div class="mb-3">
          <label for="schoolEmail" class="form-label fw-bold">School Email</label>
          <input type="email" class="form-control" id="schoolEmail" name="school_email" value="<?= $data['school_email'] ?>" required>
        </div>
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary btn-lg">Save Settings</button>
        </div>
      </form>
      <script>
      function previewLogo(event) {
        const [file] = event.target.files;
        const preview = document.getElementById('logoPreview');
        if (file) {
          preview.src = URL.createObjectURL(file);
          preview.style.display = 'block';
        } else {
          preview.src = '#';
          preview.style.display = 'none';
        }
      }
      </script>
    </div>
  </div>
</div>