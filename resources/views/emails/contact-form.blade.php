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
        .field { margin-bottom: 20px; }
        .field-label { color: #999; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
        .field-value { color: #333333; }
        .message-box { border-left: 3px solid #c41e3a; padding: 15px 20px; margin: 20px 0; white-space: pre-wrap; color: #333333; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #333; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Mama Witch</h1>
            <p>Nouveau message de contact</p>
        </div>

        <div class="content">
            <div class="field">
                <div class="field-label">Nom</div>
                <div class="field-value">{{ $contactMessage->name }}</div>
            </div>

            <div class="field">
                <div class="field-label">Email</div>
                <div class="field-value"><a href="mailto:{{ $contactMessage->email }}" style="color: #c41e3a;">{{ $contactMessage->email }}</a></div>
            </div>

            @if ($contactMessage->subject)
            <div class="field">
                <div class="field-label">Sujet</div>
                <div class="field-value">{{ $contactMessage->subject }}</div>
            </div>
            @endif

            <div class="field">
                <div class="field-label">Message</div>
                <div class="message-box">{{ $contactMessage->message }}</div>
            </div>
        </div>

        <div class="footer">
            <p>Message reçu via le formulaire de contact de mamawitch.fr</p>
        </div>
    </div>
</body>
</html>
