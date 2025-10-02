<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['id_admin'])) {
  header('Location: ../../Templates/admin/index.html');
  exit();
}
