<div class="container">
<h1>Esborrar pel·lícula</h1>
<?php use App\Registry;

if (!isPost() && empty($errors)) : ?>
    <p>Segur que vols esborrar la pel·lícula <?= $movie->getTitle() ?>?
    <form action="<?=Registry::get(\App\Registry::ROUTER)->generate("movie_delete", ["id" => $movie->getId()])  ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $movie->getId() ?>">
        <div>
            <input type="submit" name="response" value="Sí"/>
            <input type="submit" name="response" value="No"/>
        </div>
    </form>
<?php else: ?>
    <?php if (!empty($errors)): ?>
        <h2><?= array_shift($errors) ?></h2>
    <?php else: ?>
        <h2><?= $message ?></h2>
    <?php endif; ?>
<?php endif; ?>
</div>