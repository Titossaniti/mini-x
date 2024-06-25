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
    'projection' => [
        'user' => 1,
        'message' => 1,
        'timestamp' => 1,
        'likes' => 1,
        'comments' => 1
    ]
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
    <div class='border border-3 rounded mt-4 px-2'>
        <!-- DROPDOWN to get options-->
        <div class="dropstart tweetOptions">
            <a href='' class="d-inline-block" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-ellipsis" style="font-size: 24px"></i>
            </a>
            <ul class="dropdown-menu">
                <?php if ((isset($_SESSION['user']) && $_SESSION['user'] == $tweet['user']) || $currentUserRole == 'moderator'): ?>
                    <li><a class="dropdown-item text-danger" href='delete_tweet.php?id=<?= $tweet['_id'] ?>'>Supprimer<i class="fa-regular fa-trash-can ms-2"></i></a></li>
                <?php endif; ?>
                <?php if (isset($_SESSION['user']) && $_SESSION['user'] === $tweet['user']): ?>
                    <li><a class="dropdown-item" href='' data-bs-toggle='modal' data-bs-target='#updateModal<?= $tweet['_id'] ?>'>Modifier</a></li>
                <?php endif ?>
                <li><a class="dropdown-item" href="#">Partager (not available)</a></li>
            </ul>
        </div>

        <p><strong><?= $tweet['user'] ?></strong></p>
        <p class='px-3'><?= $tweet['message'] ?></p>
        <div class='d-flex justify-content-between pb-3 px-1'>
            <form action='like_tweet.php?id=<?= $tweet['_id'] ?>' method='POST'>
                <button class='btn btn-outline-secondary'><?= $tweet['likes'] ?? 0 ?><i class="fa-regular fa-thumbs-up mx-1"></i></button>
            </form>
            <div>
                <p class='text-end fw-lighter fst-italic'><?= $tweet['timestamp']->toDateTime()->format('H:i - m/d/Y') ?></p>
            </div>
        </div>

        <?php if (isset($tweet['comments'])) : ?>
        <?php foreach ($tweet['comments'] as $index => $comment): ?>
            <div class="border border-1 rounded px-2 my-2 d-flex flex-row justify-content-between">
                <div class="col-9">
                    <p><strong style="font-size: 12px"><?= $comment['user'] ?></strong></p>
                    <p class='px-3'><?= $comment['message'] ?></p>
                </div>
                <div class="d-flex flex-row align-self-center col-3">
                    <form action='like_comment.php?id=<?= $tweet['_id'] ?>' method='POST'>
                        <input type='hidden' name='comment_index' value='<?= $index ?>'>
                        <button class='btn btn-outline-secondary' style="font-size: 13px"><?= $comment['likes'] ?? 0 ?><i class="fa-regular fa-thumbs-up mx-1"></i></button>
                    </form>
                    <p class='ms-3 text-end fw-lighter fst-italic' style="font-size: 12px"><?= $comment['timestamp']->toDateTime()->format('H:i - m/d/Y') ?></p>
                </div>
            </div>
        <?php endforeach; endif;?>

        <form  action='post_comment.php?id=<?= $tweet['_id'] ?>' method='POST' class="mb-2">
            <div class="parentSubmitComment">
                <input type='text' class='form-control' name='message' placeholder='Commenter...'>
                <button type='submit' class="btn btn-sm btn-primary rounded-circle"><i class="fa-regular fa-paper-plane"></i></button>
            </div>
        </form>
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