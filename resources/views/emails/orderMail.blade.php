<!DOCTYPE html>
<html>
<head>
    <title>Сброс пароля</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ATWINTA</h1>
            <p>Digital-агентство</p>
        </div>
        <h2>Сброс пароля</h2>
        <p>Для сброса пароля нажмите на ссылку ниже:</p>
        <a href="{{ $resetUrl }}">Сбросить пароль</a>
        <p>Если вы не запрашивали сброс пароля, проигнорируйте это письмо.</p>
        <div class="footer">
            <p>Это письмо было отправлено автоматически. Пожалуйста, не отвечайте на него.</p>
            <p>&copy; {{ date('Y') }} ATWINTA. Все права защищены.</p>
        </div>
    </div>
</body>
</html>