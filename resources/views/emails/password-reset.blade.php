<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #0a0a0a; color: #e0e0e0; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 40px 20px; }
        .header { text-align: center; padding-bottom: 30px; border-bottom: 2px solid #c41e3a; margin-bottom: 30px; }
        .header h1 { color: #ffffff; font-size: 24px; margin: 0; }
        .header p { color: #c41e3a; font-size: 12px; letter-spacing: 3px; text-transform: uppercase; margin-top: 5px; }
        .content { line-height: 1.8; font-size: 15px; }
        .content p { margin: 15px 0; }
        .btn { display: inline-block; padding: 14px 32px; background: #c41e3a; color: #ffffff !important; text-decoration: none; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; font-size: 13px; border-radius: 4px; margin: 20px 0; }
        .note { font-size: 13px; color: #999; margin-top: 20px; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #333; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Mama Witch</h1>
            <p>Réinitialisation du mot de passe</p>
        </div>

        <div class="content">
            <p>Bonjour {{ $user->name }},</p>

            <p>Une réinitialisation de votre mot de passe a été demandée.</p>

            <p>Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe :</p>

            <p style="text-align: center;">
                <a href="{{ url('/account/setup/' . $user->setup_token) }}" class="btn">Réinitialiser mon mot de passe</a>
            </p>

            <p class="note">Vous pouvez également vous connecter directement avec votre compte Google.</p>
            <p class="note">Ce lien est valable 48 heures. Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet email.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Mama Witch — Hard Rock, Paris</p>
        </div>
    </div>
</body>
</html>
