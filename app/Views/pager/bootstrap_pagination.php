<?php $pager->setSurroundCount(2) ?>

<nav aria-label="Navigasi halaman">
    <ul class="pagination pagination-sm mb-0">

        <?php if ($pager->hasPreviousPage()) : ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getPreviousPageURI() ?>" aria-label="Sebelumnya">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php else : ?>
            <li class="page-item disabled" aria-disabled="true">
                <span class="page-link">&laquo;</span>
            </li>
        <?php endif; ?>

        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>"
                <?= $link['active'] ? 'aria-current="page"' : '' ?>>
                <a class="page-link" href="<?= $link['uri'] ?>">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach; ?>

        <?php if ($pager->hasNextPage()) : ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getNextPageURI() ?>" aria-label="Berikutnya">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php else : ?>
            <li class="page-item disabled" aria-disabled="true">
                <span class="page-link">&raquo;</span>
            </li>
        <?php endif; ?>

    </ul>
</nav>