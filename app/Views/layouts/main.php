<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'Toko Roti') ?> — Toko Roti</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url('bootstrap/dist/css/bootstrap.min.css') ?>">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* ── Layout shell ───────────────────────────────────── */
        body {
            background-color: var(--bs-body-bg);
            overflow-x: hidden;
        }

        /* Active sidebar link highlight */
        #sidebar .nav-link.active {
            background-color: #dd4814;
            color: #fff !important;
            border-radius: 8px;
        }
        #sidebar .nav-link:not(.active):hover {
            background-color: rgba(255,255,255,.07);
            border-radius: 8px;
        }

        /* Main content pushed right of sidebar */
        #main-content {
            margin-left: 260px;
            margin-top: 56px;   /* topbar height */
            min-height: calc(100vh - 56px);
            padding: 1.5rem;
            transition: margin-left .3s ease;
        }

        /* Mobile: hide sidebar off-canvas */
        @media (max-width: 991.98px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #main-content { margin-left: 0 !important; }
            header.navbar { margin-left: 0 !important; }
        }

        /* ── Brand colour overrides ─────────────────────────── */
        .btn-brand {
            background-color: #dd4814;
            border-color: #dd4814;
            color: #fff;
        }
        .btn-brand:hover, .btn-brand:focus {
            background-color: #c33f10;
            border-color: #c33f10;
            color: #fff;
        }
        .text-brand { color: #dd4814 !important; }
        .badge-brand { background-color: #dd4814; }

        /* ── Table tweaks ───────────────────────────────────── */
        .table th { white-space: nowrap; }

        /* ── Card subtle border ─────────────────────────────── */
        .card { border-color: rgba(255,255,255,.08); }
    </style>

    <?= $this->renderSection('styles') ?>
</head>
<body>

    <?= view('layouts/partials/_sidebar') ?>

    <?= view('layouts/partials/_topbar') ?>

    <main id="main-content">

        <!-- Flash messages -->
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <ul class="mb-0 ps-3">
                    <?php foreach ((array) session()->getFlashdata('errors') as $err) : ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <!-- Page content -->
        <?= $this->renderSection('content') ?>

    </main>

    <!-- Bootstrap JS -->
    <script src="<?= base_url('bootstrap/dist/js/bootstrap.bundle.min.js') ?>"></script>

    <script>
    // ── Sidebar toggle (mobile) ──────────────────────────────
    const sidebarToggleBtn = document.getElementById('sidebarToggle');
    const sidebar          = document.getElementById('sidebar');

    if (sidebarToggleBtn && sidebar) {
        sidebarToggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 992 &&
                !sidebar.contains(e.target) &&
                !sidebarToggleBtn.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    }

    // ── Dark / Light mode toggle ─────────────────────────────
    const htmlEl      = document.documentElement;
    const toggleBtn   = document.getElementById('darkModeToggle');
    const toggleIcon  = document.getElementById('darkModeIcon');
    const STORAGE_KEY = 'roti_theme';

    function applyTheme(theme) {
        htmlEl.setAttribute('data-bs-theme', theme);
        if (toggleIcon) {
            toggleIcon.className = theme === 'dark'
                ? 'bi bi-moon-stars-fill'
                : 'bi bi-sun-fill';
        }
        localStorage.setItem(STORAGE_KEY, theme);
    }

    // Load saved preference (default: dark)
    applyTheme(localStorage.getItem(STORAGE_KEY) || 'dark');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            applyTheme(htmlEl.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark');
        });
    }
    </script>

    <?= $this->renderSection('scripts') ?>

</body>
</html>