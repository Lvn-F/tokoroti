<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toko Roti</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f4f1ee;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
            padding: 1rem;
        }
        .card {
            background: #fff;
            width: 100%;
            max-width: 380px;
            padding: 2rem 1.75rem;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .card h1 {
            font-size: 1.4rem;
            margin: 0 0 .25rem;
            color: #222;
        }
        .card p.subtitle {
            margin: 0 0 1.5rem;
            color: #888;
            font-size: .9rem;
        }
        label {
            display: block;
            font-size: .85rem;
            margin-bottom: .35rem;
            color: #444;
            font-weight: 600;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: .65rem .75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            margin-bottom: 1rem;
        }
        input:focus {
            outline: none;
            border-color: #dd4814;
        }
        button {
            width: 100%;
            padding: .75rem;
            background: #dd4814;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
        }
        button:hover { background: #c33f10; }
        .alert {
            padding: .65rem .85rem;
            border-radius: 8px;
            font-size: .85rem;
            margin-bottom: 1rem;
        }
        .alert-error {
            background: #fdecea;
            color: #b3261e;
            border: 1px solid #f5c2bf;
        }
        .alert-success {
            background: #e8f5e9;
            color: #1e7e34;
            border: 1px solid #c3e6cb;
        }
        .alert ul { margin: 0; padding-left: 1.1rem; }
    </style>
</head>
<body>

<div class="card">
    <h1>Toko Roti</h1>
    <p class="subtitle">Masuk ke akun Anda</p>

    <?php if (session()->getFlashdata('loginError')) : ?>
        <div class="alert alert-error"><?= esc(session()->getFlashdata('loginError')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('message')) : ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('message')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')) : ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('login') ?>" method="post" autocomplete="off">
        <?= csrf_field() ?>

        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= esc(old('username')) ?>" required autofocus>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>