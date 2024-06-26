<?php
require 'config.php';
session_start();
ob_start();
?>
<div class="text-center">
    <p class="mt-5">Oups, la page n'est pas encore disponible. Revenez plus tard ! </p>
    <a class="btn btn-lg btn-primary rounded-pill mt-5" href="index.php">Voir les Yeets</a>
</div>
<?php
$title = 'Yeet - Profil';
$content = ob_get_clean();
require 'layout.php';