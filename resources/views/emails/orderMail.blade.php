<!DOCTYPE html>
<html>
<head>
    <title>Сброс пароля</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .button { 
            display: inline-block; 
            padding: 10px 20px; 
            background-color: #3b82f6; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
        }
    </style>
</head>
<body>
    <h2>Сброс пароля</h2>
    <p>Для сброса пароля нажмите на кнопку ниже:</p>
    <a href="{{ $resetUrl }}" class="button">Сбросить пароль</a>
    <p>Если вы не запрашивали сброс пароля, проигнорируйте это письмо.</p>
</body>
</html>