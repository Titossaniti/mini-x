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

// Pipeline d'agrégation pour ajouter le total des likes (tweet + commentaires) à chaque tweet + avoir tous les tweets
$pipeline = [
    [
        '$sort' => ['timestamp' => -1]  // Tri par date décroissante
    ],
    [
        '$addFields' => [
            'totalLikes' => [
                '$add' => [
                    '$likes',
                    ['$sum' => '$comments.likes']
                ]
            ]
        ]
    ],
    [
        '$project' => [
            'user' => 1,
            'message' => 1,
            'timestamp' => 1,
            'likes' => 1,
            'comments' => 1,
            'totalLikes' => 1  // Inclut le nouveau champ calculé
        ]
    ]
];

// Récupération des tweets avec les likes calculés
$tweets = $collection->aggregate($pipeline);?>

<div class="dropstart text-end">
    <a href='' class="d-inline-block" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa-solid fa-bars fs-2 text-dark"></i></i>
    </a>
    <ul class="dropdown-menu">
        <?php
        if (isset($_SESSION['user'])) {$userName = $_SESSION['user']; ?>
            <li>
                <a href="profile.php" class="dropdown-item fw-bold"><?= $userName ?></a>
            </li>
            <li>
                <a href="log_out.php" class="dropdown-item">Se déconnecter <i class="ms-1 fa-solid fa-right-from-bracket"></i></a>
            </li>
            <?php
        } else {
            ?>
            <li>
                <a href="x.php" class=" dropdown-item">Se connecter <i class="ms-1 fa-solid fa-right-to-bracket"></i></a>
            </li>
            <?php
        }
        ?>
    </ul>
</div>
<?php
    if (isset($_SESSION['user'])) :
    $currentUser = $_SESSION['user'];
    ?>
<div class='p-2 border rounded my-5'>
    <p>Bonjour <span class="fw-bolder"><?= $userName ?></span>,</p>
    <form action='post_tweet.php' method='POST'>
        <input hidden type='text' name='user' class='form-control' placeholder='Qui es-tu ?'>
        <input type='text' name='message' class='form-control mt-2' placeholder="Yeet ce qu'il te passe par la tête !">
        <div class="d-flex justify-content-end">
            <button type='submit' class='btn btn-primary mt-2 fw-bold'>Yeet!</button>
        </div>
    </form>
</div>
<?php endif; foreach ($tweets as $tweet): ?>
    <div class='border border-3 rounded mt-4 px-2'>
        <!-- DROPDOWN pour avoir les options dispo pour un tweet-->
        <div class="dropstart tweetOptions">
            <a href='' class="d-inline-block" id="dropdownTweetOption" data-bs-toggle="dropdown" aria-expanded="false">
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

        <p><a href="profile.php" class="fw-bold link-body-emphasis link-underline link-underline-opacity-0 link-underline-opacity-75-hover"><?= $tweet['user'] ?></a></p>
        <p class='px-3'><?= $tweet['message'] ?></p>
        <div class='d-flex justify-content-between pb-3 px-1'>
            <div class="d-flex">
                <form action='like_tweet.php?id=<?= $tweet['_id'] ?>' method='POST'>
                    <button class='btn btn-outline-secondary btnSizeCustom'><?= $tweet['likes'] ?? 0 ?><i class="fa-regular fa-thumbs-up mx-1"></i></button>
                </form>
                <div class="ms-2">
                    <button class='btn btn-outline-secondary btnSizeCustom' disabled><?= $tweet['totalLikes'] ?><i class="fa-solid fa-chart-simple mx-1"></i></button>
                </div>
            </div>
            <div>
                <p class='text-end fw-lighter fst-italic'><?= $tweet['timestamp']->toDateTime()->format('H:i - m/d/Y') ?></p>
            </div>
        </div>

        <?php if (isset($tweet['comments'])) : ?>
        <?php foreach ($tweet['comments'] as $index => $comment): ?>
            <div class="border border-1 rounded px-2 my-2 d-flex flex-row justify-content-between">
                <div class="col-9">
                    <p><a href="profile.php" class="fw-bold link-body-emphasis link-underline link-underline-opacity-0 link-underline-opacity-75-hover" style="font-size: 12px"><?= $comment['user'] ?></a></p>
                    <p class='px-3'><?= $comment['message'] ?></p>
                </div>
                <div class="d-flex flex-row align-self-center col-3">
                    <form action='like_comment.php?id=<?= $tweet['_id'] ?>' method='POST'>
                        <input type='hidden' name='comment_index' value='<?= $index ?>'>
                        <button class='btn btn-outline-secondary' style="font-size: 13px; width:62px;height:34px"><?= $comment['likes'] ?? 0 ?><i class="fa-regular fa-thumbs-up mx-1"></i></button>
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
                    <h2 class='modal-title fs-5' id='updateModalLabel'>Modifier un Yeet</h2>
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

$title = 'Yeet - Fil de Yeets';
$content = ob_get_clean();
require 'layout.php';