<div class="container">
<?php use App\Registry;

if (array_key_exists("userId", $_SESSION)){
    ?><h1>Pel·lícules</h1><?php
    if (!empty($message)) :?>
        <div><?= $message ?></div>
    <?php endif; ?>
    <p><a href="<?= Registry::get(Registry::ROUTER)->generate("movie_create") ?>">Nova pel·lícula</a></p>
    <ul>
        <?php foreach ($movies as $movie): ?>
            <li><a href="<?= Registry::get(Registry::ROUTER)->generate("movie_view", ["id" => $movie->getId()])?>"><?= $movie->getTitle() ?></a>
                <ul>
                    <li>
                        <a href="<?= Registry::get(\App\Registry::ROUTER)->generate("movie_edit", ["id" => $movie->getId()]) ?>">Editar</a>
                    <li><a href="<?= Registry::get(\App\Registry::ROUTER)->generate("movie_delete", ["id" => $movie->getId()]) ?>">Borrar</a>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>


    </div>

    <?php
}else{
    ?>
    <h1>Has de iniciar sesión antes de nada</h1>
<?php
    }