<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
</head>

<body class="mx-auto p-4 container bg-primary-subtle">
    <header>
        <nav class="mb-5 d-flex justify-content-between">
            <div class="col-4">
                <a href="index.php"><img src="./media/logo.png" alt="Logo de Y" class="logo"></a>
            </div>
            <div class="col-8 text-end align-self-center">
                <a href="index.php" class="link-dark link-offset-3 link-underline link-underline-opacity-0 link-underline-opacity-75-hover fw-bold fs-6">YEETS</a>
                <span class="fw-bold">|</span>
                <a href="leaderboard.php" class="link-dark link-offset-3 link-underline link-underline-opacity-0 link-underline-opacity-75-hover fw-bold fs-6">CLASSEMENT</a>
            </div>
        </nav>
    </header>
    <main>
        <?php echo $content ?>
    </main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js" integrity="sha512-7Pi/otdlbbCR+LnW+F7PwFcSDJOuUJB3OxtEHbg4vSMvzvJjde4Po1v4BR9Gdc9aXNUNFVUY+SK51wWT8WF0Gg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="./main.js"></script>
</body>
</html>