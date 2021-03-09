<?php
include('../include/phpframe.php');

unset($_SESSION['LoginUserId']);
unset($_SESSION['LoginUserType']);

echo "<script>location.href = '../index.php'</script>";
?>