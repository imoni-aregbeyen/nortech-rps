<?php
session_start();
require_once('./conn.php');

$pg = isset($_GET['page']) ? $_GET['page'] : 'home';
$brand = ucfirst($pg);

// Grab school info if table exists
$schoolInfo = null;
$checkTableSql = "SHOW TABLES LIKE 'school_info'";
$checkResult = $conn->query($checkTableSql);
if ($checkResult && $checkResult->num_rows > 0) {
  $infoResult = $conn->query("SELECT * FROM school_info LIMIT 1");
  if ($infoResult && $infoResult->num_rows > 0) {
    $schoolInfo = $infoResult->fetch_assoc();
  }
}
$title = $schoolInfo['school_name'] ?? 'Nortech RPS';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?></title>
  <link rel="stylesheet" href="./css/bootstrap.min.css">
</head>
<body>
  
<nav class="navbar bg-body-tertiary fixed-top">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="#"><?= $brand ?></a>
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasNavbarLabel"><?= $title ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
          <li class="nav-item">
            <a class="nav-link <?= $pg === 'home' ? 'active' : '' ?>" aria-current="page" href="./">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $pg === 'settings' ? 'active' : '' ?>" href="./?page=settings">Settings</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $pg === 'sections' ? 'active' : '' ?>" href="./?page=sections">Sections</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $pg === 'classes' ? 'active' : '' ?>" href="./?page=classes">Classes</a>
          </li>
        </ul>
        <form class="d-flex mt-3" role="search">
          <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search"/>
          <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
      </div>
    </div>
  </div>
</nav>

<?php
  if(file_exists("./pg/{$pg}.php")){
    include_once("./pg/{$pg}.php");
  } else {
    include_once("./pg/404.php");
  }
?>

<script src="./js/bootstrap.bundle.min.js"></script>
</body>
</html>