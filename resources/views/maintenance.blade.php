<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    @include('partials.fixed-viewport')
    <title>King Rangement Benin | Maintenance</title>
    <style>
        :root {
            color-scheme: dark;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: radial-gradient(circle at 20% 10%, #1f2f52 0%, #0b1018 45%, #080b11 100%);
            color: #f7f7f7;
        }

        .card {
            width: min(92vw, 560px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(10, 14, 23, 0.86);
            box-shadow: 0 28px 70px rgba(0, 0, 0, 0.45);
            padding: 34px 30px;
        }

        h1 {
            margin: 0 0 12px;
            font-size: 2rem;
            color: #e4bf71;
            letter-spacing: 0.02em;
        }

        p {
            margin: 0;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.83);
        }

        .hint {
            margin-top: 18px;
            font-size: 0.92rem;
            color: rgba(255, 255, 255, 0.6);
        }
    </style>
</head>
<body>
<section class="card" role="status" aria-live="polite">
        <h1>King Rangement Benin</h1>
    <p>
        Une mise a jour est en cours pour ameliorer votre experience.
        Le site revient tres vite.
    </p>
    <p class="hint">Merci pour votre patience.</p>
</section>
</body>
</html>
