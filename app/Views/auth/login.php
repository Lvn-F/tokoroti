<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toko Roti</title>
    <link rel="stylesheet" href="../public/bootstrap/dist/css/bootstrap.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f4f1ee;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
        }
        .login-card .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        }
        .btn-brand {
            background-color: #dd4814;
            border-color: #dd4814;
        }
        .btn-brand:hover {
            background-color: #c33f10;
            border-color: #c33f10;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="card p-4 p-md-5">
        <div class="card-body p-0">
            <h1 class="h4 mb-1">Toko Roti</h1>
            <p class="text-muted small mb-4">Masuk ke akun Anda</p>

            <?php if (session()->getFlashdata('loginError')) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= esc(session()->getFlashdata('loginError')) ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('message')) : ?>
                <div class="alert alert-success" role="alert">
                    <?= esc(session()->getFlashdata('message')) ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')) : ?>
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0 ps-3">
                        <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('login') ?>" method="post" autocomplete="off">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-control"
                        value="<?= esc(old('username')) ?>"
                        required
                        autofocus
                    >
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-brand text-white w-100 fw-semibold">
                    Login
                </button>
            </form>
        </div>
    </div>
</div>

<script src="../public/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>