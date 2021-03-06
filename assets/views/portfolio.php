<?php
require_once __DIR__ . './../functions/portfolio_functions.php';

$ua = getBrowser();
?>

<section class="portfolio section" id="section5">
  <div class="container">
    <div class="row">

      <div class="section-title text-center heading col-sm-12">
        <h2>Portfolio</h2>
      </div>

      <div class="part col-md-12">
        <div class="portfolio-nav col-sm-12" id="filter-button">
          <ul>
            <li data-filter="*" class="current">
              <a>Toutes les catégories</a>
            </li>

            <?php foreach (getCat($pdo) as $cat) : ?>

              <li data-filter=".<?= $cat['class'] ?>">
                <a><?= $cat['titre_cat'] ?></a>
              </li>

            <?php endforeach; ?>

          </ul>
        </div>

        <div class="project-gallery col-sm-12">
          <div class="portfolioContainer row">

            <?php foreach (getPost($pdo) as $post) : ?>

              <div class="grid-item col-md-4 <?= $post['class'] ?>">
                <figure>
                  <?php if ($ua['name'] == 'Safari') : ?>
                    <img src="global/uploads/<?= $post['img2'] ?>" alt="image site internet" width="357" height="240">
                  <?php else : ?>
                    <img src="global/uploads/<?= $post['img'] ?>" alt="image site internet" width="357" height="240">
                  <?php endif; ?>
                  <figcaption class="fig-caption">
                    <a href="<?= $post['url'] ?>" data-id="<?= $post['id_post'] ?>" class="post-link">
                      <i class="fa fa-search"></i>
                      <h5 class="title"><?= $post['titre'] ?></h5>
                    </a>
                    <span class="sub-title"><?= $post['titre_cat'] ?></span>
                  </figcaption>
                </figure>
              </div>

            <?php endforeach; ?>



          </div>
        </div>

      </div>


    </div>
  </div>
</section>