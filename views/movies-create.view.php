<?php

?>

<div class="container">
<h1>New movie</h1>

    <?php use App\Registry;

    if (!empty($message)) :?>

        <h3><?=$message?></h3>
    <?php endif ?>
    <?php if (!empty($errors)): ?>
        <div>
        <ul>
            <?php foreach ($errors as $error) : ?>
                <li><?= $error ?></li>
            <?php endforeach ?>
        </ul>
        </div>
    <?php endif; ?>

<form action="<?= Registry::get(Registry::ROUTER)->generate("movie_createStore") ?>" method="post" enctype="multipart/form-data">


    <input type="hidden" name="token" value="<?= $formToken ?>"/>

    <div>
        <label for="title">Title</label>
        <input id="title" type="text" name="title" value="<?= $data["title"] ?>">
    </div>
    <div>
        <label for="release_date">Release date (YYYY-mm-dd)</label>
        <input id="title" type="text" name="release_date" value="<?= $data["release_date"] ?>">
    </div>
    <div>
        <label for="overview">Overview</label>
        <textarea id="overview" name="overview"><?= $data["overview"] ?></textarea>
    </div>
    <div>
        <p>Rating</p>
        <?php foreach ([1, 2, 3, 4, 5] as $ratingValue) : ?>
            <label for="genre<?= $ratingValue ?>">
                <input id="genre<?= $ratingValue ?>" type="radio" name="rating"
                       value="<?= $ratingValue ?>" <?= ( $data["rating"] == $ratingValue) ? "checked":"" ?> >
                <?= $ratingValue ?>
            </label>
        <?php endforeach ?>
    </div>
    <div>
        <p>Poster</p>
        <input type="file" name="poster"/>
    </div>
    <div>
        <input type="submit" value="Crear">
    </div>
</form>
</div>