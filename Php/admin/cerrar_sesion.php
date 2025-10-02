<?php
session_start();
session_unset();
session_destroy();
header("Location: ../../Templates/admin/login_admin.html");
exit();
