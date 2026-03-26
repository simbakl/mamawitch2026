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
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #333; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Mama Witch</h1>
            <p>Espace Professionnel</p>
        </div>

        <div class="content">
            <p>Bonjour {{ $proAccount->first_name }},</p>

            <p>Vous avez été invité(e) à accéder à l'<strong>Espace Pro</strong> de Mama Witch en tant que <strong>{{ $proAccount->proType->name }}</strong>.</p>

            <p>Cet espace vous donne accès à des contenus exclusifs réservés aux professionnels (fiche technique, photos HD, logos, bio presse, etc.).</p>

            <p>Pour activer votre accès, connectez-vous avec votre compte Google :</p>

            <p style="text-align: center;">
                <a href="{{ url('/pro/invitation/' . $proAccount->invitation_token) }}" class="btn">Accéder à l'Espace Pro</a>
            </p>

            <p style="font-size: 13px; color: #999;">La connexion se fait exclusivement via Google ({{ $proAccount->email }}).</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Mama Witch — Hard Rock, Paris</p>
        </div>
    </div>
</body>
</html>
