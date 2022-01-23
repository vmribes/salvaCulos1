<div class="container">
    <h1>Pel·lícula:</h1>
    <?php use App\Movie;

    if (!empty($movie)): ?>
        <h2>Título: <?=$movie->getTitle()?></h2>
        <p><i>Id: <?=$movie->getId()?></i></p>

        <figure>
            <img style="width: 100px"alt="<?=$movie->getTitle() ?>" src="http://<?=$request->getDomain() ?>/posters/<?=$movie->getPoster() ?>" />
        </figure>

        <p>Overview: <?=$movie->getOverview()?></p>
        <p>Rate: <?=$movie->getRating()?></p>
    <?php else: ?>
        <h3><?=array_shift($errors)?></h3>
    <?php endif; ?>

</div>

