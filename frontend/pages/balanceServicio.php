<?php
require_once '../../backend/includes/auth.php';
verificarPermiso(['Administrador', 'Secretaria']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Lubricentro Cusqueña</title>
  <link href="../css/bootstrap.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
  <!-- Navbar Superior (fijo) -->
  <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark fixed-top">
    <?php if ($_SESSION['rol'] === 'Administrador'): ?>
    <a class="navbar-brand ps-3" href="base.php">La Cusqueña</a>
    <?php endif; ?>

    <?php if ($_SESSION['rol'] === 'Secretaria'): ?>
    <a class="navbar-brand ps-3" href="base2.php">La Cusqueña</a>
    <?php endif; ?>
    <button class="btn btn-link btn-sm me-4" id="sidebarToggle">
      <i class="fas fa-bars"></i>
    </button>
    <ul class="navbar-nav ms-auto me-3">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
          <i class="fas fa-user fa-fw"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="../../index.html">Cerrar Sesión</a></li>
        </ul>
      </li>
    </ul>
  </nav>

  <div id="layoutSidenav">
    <div id="layoutSidenav_nav">
      <?php if ($_SESSION['rol'] === 'Administrador'): ?>
      <script>
        fetch('sidebear_Admin.php')
          .then(r => r.text())
          .then(html => document.getElementById('layoutSidenav_nav').innerHTML = html)
          .catch(e => console.error('Error cargando sidebar:', e));
      </script>
      <?php endif; ?>
      <?php if ($_SESSION['rol'] === 'Secretaria'): ?>
      <script>
        fetch('sidebear_secre.php')
          .then(r => r.text())
          .then(html => document.getElementById('layoutSidenav_nav').innerHTML = html)
          .catch(e => console.error('Error cargando sidebar:', e));
      </script>
      <?php endif; ?>
    </div>

    <div id="layoutSidenav_content">
      <main class="container-xl my-2 col-12 mx-auto">
        <div class="container-fluid px-4 ">
          <h1 class="mt-4 text-center mb-4 ">Balance de Servicios</h1>
          <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap">
              <div class="d-flex align-items-center flex-wrap">
                <button class="btn btn-primary mb-2 me-2">Buscar</button>
                <label for="inicio" class="form-label fw-bold me-2">Fecha Inicio:</label>
                <input type="date" class="form-control me-2 mb-2" style="width: 130px" id="inicio">
                <label for="fin" class="form-label fw-bold me-2">Fecha Fin:</label>
                <input type="date" class="form-control me-2 mb-2" style="width: 130px" id="fin">
              </div>
              <div class="d-flex align-items-center flex-wrap">
                <button onclick="window.print()" class="btn btn-primary me-2 mb-2">Imprimir</button>
                <button id="exportarPdf" class="btn btn-primary mb-2">Exportar PDF</button>
              </div>
            </div>

            <div class="table-responsive my-4">
              <table class="table table-bordered table-hover text-center">
                <thead>
                  <tr class="table-dark">
                    <th>ID</th>
                    <th>Descripcion</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                    <th>Fecha</th>
                  </tr>
                </thead>
                <tbody class="align-middle">
                  <tr>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="total-container d-flex align-items-center mt-3">
              <label for="total" class="form-label fw-bold me-2 ">TOTAL:</label>
              <input type="text" id="total" class="form-control form-control-sm me-2 mb-2" style="width: 120px; font-size: 0.8rem;" disabled>
            </div>

            <nav aria-label="Page navigation example" class="d-flex justify-content-end ">
              <ul class="pagination">
                <li class="page-item">
                  <a class="page-link" href="#" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                  </a>
                </li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                  <a class="page-link" href="#" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                  </a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script src="../js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="../js/functions/balanceServicios.js"></script>

</body>

</html>
