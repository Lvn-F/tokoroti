<!-- ===================== TOPBAR ===================== -->
<header class="navbar navbar-dark sticky-top px-3 py-0"
        style="height:56px; background:#212529; margin-left:260px; z-index:1029;">

    <!-- Hamburger (mobile) -->
    <button class="btn btn-sm btn-dark d-lg-none me-2"
            id="sidebarToggle"
            type="button"
            aria-label="Toggle sidebar">
        <i class="bi bi-list fs-5"></i>
    </button>

    <!-- Page title injected by each view via $pageTitle -->
    <span class="navbar-brand mb-0 h6 fw-semibold text-white">
        <?= esc($pageTitle ?? 'Dashboard') ?>
    </span>

    <div class="ms-auto d-flex align-items-center gap-3">

        <!-- Dark mode toggle -->
        <button class="btn btn-sm btn-outline-secondary border-0 text-secondary"
                id="darkModeToggle"
                type="button"
                title="Toggle dark mode"
                aria-label="Toggle dark mode">
            <i class="bi bi-moon-stars-fill" id="darkModeIcon"></i>
        </button>

        <!-- User dropdown -->
        <div class="dropdown">
            <button class="btn btn-sm btn-dark dropdown-toggle d-flex align-items-center gap-2 py-1"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    id="userMenuButton"
                    aria-haspopup="true">
                <?php
                $foto = session()->get('foto_profil');
                if ($foto) : ?>
                    <img src="<?= base_url('uploads/profil/' . esc($foto)) ?>"
                         alt="Foto profil"
                         width="28" height="28"
                         class="rounded-circle object-fit-cover">
                <?php else : ?>
                    <span class="rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                          style="width:28px;height:28px;font-size:.75rem;">
                        <?= strtoupper(substr(session()->get('nama_user') ?? 'U', 0, 1)) ?>
                    </span>
                <?php endif; ?>
                <span class="d-none d-md-inline small">
                    <?= esc(session()->get('nama_user') ?? '') ?>
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow"
                aria-labelledby="userMenuButton">
                <li>
                    <span class="dropdown-item-text small text-secondary">
                        Role: <strong><?= esc(ucfirst(session()->get('role') ?? '')) ?></strong>
                    </span>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="<?= site_url('logout') ?>">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                </li>
            </ul>
        </div>

    </div>
</header>
<!-- =================== END TOPBAR =================== -->