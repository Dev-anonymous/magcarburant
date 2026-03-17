<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Réinitialisation de mot de passe</title>
</head>

<body>
    <h2>Bonjour,</h2>

    <p>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le lien ci-dessous pour définir un nouveau mot de passe :</p>

    <p>
        <a href="{{ route('recovery.verify', ['token' => $token]) }}"
            style="display:inline-block;padding:10px 20px;background:#3490dc;color:#fff;text-decoration:none;border-radius:5px;">
            Réinitialiser mon mot de passe
        </a>
    </p>

    <p>⚠️ Ce lien est valable pendant 15 minutes. Passé ce délai, vous devrez refaire une demande.</p>

    <p>Si vous n’êtes pas à l’origine de cette demande, vous pouvez ignorer cet email.</p>

    <p>Merci,<br>L’équipe Support</p>
</body>

</html>
