<?php
require 'config.php';
session_start();
ob_start();

$aggregation = [
    [
        '$facet' => [
            "tweets" => [
                ['$group' => [
                    '_id' => '$user',
                    'tweetCount' => ['$sum' => 1],
                    'tweetLikes' => ['$sum' => '$likes'],
                    'commentsReceived' => ['$sum' => [
                        '$size' => ['$ifNull' => ['$comments', []]]
                    ]]
                ]]
            ],
            "comments" => [
                ['$unwind' => '$comments'],
                ['$project' => [
                    'tweet_user' => '$user',
                    'comment_user' => '$comments.user',
                    'comment_likes' => '$comments.likes'
                ]],
                ['$group' => [
                    '_id' => '$comment_user',
                    'commentCount' => ['$sum' => 1],
                    'commentLikes' => ['$sum' => '$comment_likes'],
                    'selfCommentCount' => [
                        '$sum' => [
                            '$cond' => [
                                ['$eq' => ['$tweet_user', '$comment_user']], // Vérifier si le commentaire est sur son propre tweet
                                1,
                                0
                            ]
                        ]
                    ]
                ]]
            ]
        ]
    ],
    [
        '$project' => [
            'totalEngagement' => [
                '$concatArrays' => ['$tweets', '$comments']
            ]
        ]
    ],
    [
        '$unwind' => '$totalEngagement'
    ],
    [
        '$group' => [
            '_id' => '$totalEngagement._id',
            'totalTweets' => ['$sum' => '$totalEngagement.tweetCount'],
            'totalTweetLikes' => ['$sum' => '$totalEngagement.tweetLikes'],
            'totalComments' => ['$sum' => '$totalEngagement.commentCount'],
            'totalCommentLikes' => ['$sum' => '$totalEngagement.commentLikes'],
            'commentsReceived' => ['$sum' => '$totalEngagement.commentsReceived'],
            'selfCommentCount' => ['$sum' => '$totalEngagement.selfCommentCount']
        ]
    ],
    [
        '$addFields' => [
            'totalPoints' => [
                '$add' => [
                    '$totalTweets',
                    '$totalTweetLikes',
                    // Soustrait les commentaires sur ses propres tweets pour ne pas les compter double
                    ['$subtract' => ['$totalComments', '$selfCommentCount']],
                    '$totalCommentLikes',
                    '$commentsReceived'
                ]
            ]
        ]
    ],
    [
        '$sort' => ['totalPoints' => -1]
    ],
    ['$limit' => 5]
];

$results = $collection->aggregate($aggregation);
?>

<div class="mt-5">
    <h2 class="text-center display-2">Classement</h2>
    <div class="list-group">
        <?php foreach ($results as $index => $doc) : ?>
            <a href="profile.php" class="list-group-item d-flex justify-content-between align-content-center
                <?php if ($index == 0) echo 'gold-grad'; ?>
                <?php if ($index == 1) echo 'silver-grad'; ?>
                <?php if ($index == 2) echo 'bronze-grad'; ?>
                <?php if ($index > 2) echo 'btn-grad'; ?>
            ">
                <div class="text-start fs-3 col-4"><?= $index + 1 ?></div>
                <div class="fw-bold fs-5 col-4"><?= $doc['_id'] ?></div>
                <div  class="fw-bold fs-5 col-4"><?= $doc['totalPoints'] ?> points</div>
            </a>
        <?php endforeach; ?>
    </div>
    <div style="font-size: 12px">
        Les utilisateurs sont classés par points gagnés. <br/>
        Rapportent 1 point :<br/>
        Chaque Yeet et commentaire posté.<br/>
        Chaque Yikes sur ses différents Yeets et commentaires.<br/>
        Chaque commentaire posté par les autres utilisateurs sous ses Yeets.
    </div>
</div>
<?php
$title = 'Yeet - Classement';
$content = ob_get_clean();
require 'layout.php';