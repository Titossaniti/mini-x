<?php
require 'config.php';
ob_start();
?>

<h1 class="text-center">
    YEET
</h1>

<form action='log_in.php' method='POST' class="border border-dark rounded-2 px-5 py-3">
    <h2 class="mb-3">Se connecter</h2>
    <input type="text" name="user" class='form-control' placeholder='Identifiant' required>
    <input type="password" name="password" class='form-control mt-2' placeholder='Mot de passe' required>
    <button type="submit" class="btn btn-lg btn-primary mt-3">Connexion</button>
</form>

<div class="text-center my-5">OU</div>

<form action='sign_up.php' method='POST' class="border border-dark rounded-2 px-5 py-3">
    <h2 class="mb-3">Créer un compte</h2>
    <input type="text" name="user" class='form-control' placeholder='Identifiant' required>
    <input type="password" name="password" class='form-control mt-2' placeholder='Mot de passe' required>
    <button type="submit" class="btn btn-lg btn-primary mt-3">S'inscrire</button>
</form>
<div class="text-center my-5">OU</div>
<p class="text-center"><a href="index.php">Continuer sans se connecter</a></p>
<?php
$title = 'Mini X';
$content = ob_get_clean();
require 'layout.php';