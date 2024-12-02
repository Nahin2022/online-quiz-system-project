<?php
session_start();
echo "Logging out..."; // Temporary debug line

session_unset();
session_destroy();
header("Location: http://localhost/2/");
exit();
?>
