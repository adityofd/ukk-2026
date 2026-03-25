<?php
// ============ BAGIAN LOGOUT ============
session_start();
session_destroy();
echo '<script>window.location="login.php"</script>';
?>