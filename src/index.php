<?php
require 'config.php';
session_start();
ob_start();

// Recherche de l'utilisateur actuel dans la collection des users pour obtenir son rôle
if (isset($_SESSION['user'])) {
    $currentUser = $_SESSION['user'];
    $userRole = $users->findOne(['user' => $currentUser]);
    $currentUserRole = $userRole['role'] ?? '';
} else {
    $currentUserRole = '';
}

$tweets = $collection->find([], [
    'sort' => ['timestamp' => -1], // Tri par date décroissante
]);

if (isset($_SESSION['user'])) {
    $userName = $_SESSION['user'];
    ?>
    <div class="my-5 text-end">
        <p>Bonjour, <span class="fw-bold fs-4"><?= $userName ?></span></p>
        <a href="log_out.php" class="btn btn-sm btn-primary mt-3">Se déconnecter</a>
    </div>
    <?php
} else {
    ?>
    <div class="my-5 text-end">
        <a href="x.php" class="btn btn-sm btn-primary mt-3">Se connecter</a>
    </div>
    <?php
}
?>

<div class='p-2 border rounded mb-5'>
    <form action='post_tweet.php' method='POST'>
        <input hidden type='text' name='user' class='form-control' placeholder='Qui es-tu ?'>
        <input type='text' name='message' class='form-control mt-2' placeholder='Quoi de neuf ?'>
        <button type='submit' class='btn btn-primary mt-2'>Publier</button>
    </form>
</div>

<?php foreach ($tweets as $tweet): ?>
    <div class='parentCrossDelete border border-1 rounded mt-4 px-2'>
        <p><strong><?= $tweet['user'] ?></strong></p>
        <p class='px-3'><?= $tweet['message'] ?></p>
        <div class='d-flex justify-content-between pb-3 px-1'>
            <form action='like_tweet.php?id=<?= $tweet['_id'] ?>' method='POST'>
                <button class='btn btn-outline-secondary'><?= $tweet['likes'] ?? 0 ?><i class="fa-regular fa-thumbs-up mx-1"></i></button>
            </form>
            <div>
                <p class='text-end fw-lighter fst-italic'><?= $tweet['timestamp']->toDateTime()->format('H:i - m/d/Y') ?></p>
                <?php if (isset($_SESSION['user']) && $_SESSION['user'] === $tweet['user']): ?>
                    <a href='' data-bs-toggle='modal' data-bs-target='#updateModal<?= $tweet['_id'] ?>'>Modifier</a>
                <?php endif ?>
            </div>
        </div>
        <?php if ((isset($_SESSION['user']) && $_SESSION['user'] == $tweet['user']) || $currentUserRole == 'moderator'): ?>
            <a class='deleteCross' href='delete_tweet.php?id=<?= $tweet['_id'] ?>'>❌</a>
        <?php endif; ?>
    </div>

    <div class='modal fade' id='updateModal<?= $tweet['_id'] ?>' tabindex='-1' aria-labelledby='updateModalLabel' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h2 class='modal-title fs-5' id='updateModalLabel'>Modifier un tweet</h2>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <div class='modal-body'>
                    <form action='update_tweet.php?id=<?= $tweet['_id'] ?>' method='POST'>
                        <div class='mb-3'>
                            <label for='updateMessage' class='form-label'>Message</label>
                            <input type='text' class='form-control' name='message' id='message' value="<?= $tweet['message'] ?>" required>
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Fermer</button>
                            <button type='submit' class='btn btn-primary'>Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach;

$title = 'Mini X';
$content = ob_get_clean();
require 'layout.php';