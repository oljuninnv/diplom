<!DOCTYPE html>
<html>

<head>
    <title>Ваша заявка отклонена</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 0;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            overflow: hidden;
        }

        .header {
            background-color: #f8f9fa;
            padding: 25px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
        }

        .content {
            padding: 25px;
        }

        .footer {
            margin-top: 20px;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
            background-color: #f8f9fa;
            border-top: 1px solid #e0e0e0;
        }

        h1 {
            color: #333;
            margin: 0;
            font-size: 24px;
        }

        h2 {
            margin-top: 0;
            font-size: 20px;
        }

        p {
            margin-bottom: 15px;
        }

        .logo {
            font-weight: bold;
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .reason-box {
            background-color: #f9f2f2;
            border-left: 4px solid #d32f2f;
            padding: 15px;
            margin: 20px 0;
        }

        .contact-info {
            margin-top: 25px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">ATWINTA</div>
            <p>Digital-агентство</p>
        </div>

        <div class="content">
            <h2>Уважаемый(ая) {{ $user->name }},</h2>

            <p>Благодарим вас за проявленный интерес к нашему агентству и время, потраченное на подачу заявки.</p>

            <p>После тщательного рассмотрения вашей заявки мы, к сожалению, вынуждены сообщить вам о решении отклонить
                вашу кандидатуру на данном этапе.</p>

            <p>Это решение не является оценкой ваших профессиональных качеств и связано с текущими потребностями нашей
                компании.</p>

            <p>Мы сохраним ваши данные в нашей базе и обязательно рассмотрим вашу кандидатуру при появлении подходящих
                возможностей в будущем.</p>

            <div class="contact-info">
                <p>Если у вас есть вопросы относительно этого решения, вы можете связаться с нами:</p>
                <p>Email: <a href="mailto:hr@atwinta.com">hr@atwinta.com</a><br>
            </div>

            <p>С уважением,<br>Команда ATWINTA</p>
        </div>

        <div class="footer">
            <p>Это письмо было отправлено автоматически. Пожалуйста, не отвечайте на него.</p>
            <p>&copy; {{ date('Y') }} ATWINTA. Все права защищены.</p>
        </div>
    </div>
</body>

</html>
