<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de votre mot de passe</title>
</head>
<body>
    <p>Bonjour,</p>
    <p>Vous recevez cet email car une demande de réinitialisation de mot de passe a été effectuée pour votre compte.</p>
    <p>Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :</p>
    <a href="{{ route('password.reset', ['token' => $token, 'email' => $email]) }}">Réinitialiser le mot de passe</a>
    <p>Si vous n'avez pas demandé de réinitialisation de mot de passe, veuillez ignorer cet email.</p>
    <p>Cordialement,</p>
    <p>L'équipe Adolices</p>
</body>
</html>
