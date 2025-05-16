<!DOCTYPE html>
<html>
<head>
    <title>Статус вашего задания изменен</title>
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
            background-color: #c7a1c1;
            border-left: 4px solid #d02db5;
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

            @if($status === 'revision')
                <p>Ваше задание было отправлено на доработку.</p>
                @if($comment)
                <div class="reason-box">
                    <p><strong>Комментарий:</strong></p>
                    <p>{{ $comment }}</p>
                </div>
                @endif
                <p>Пожалуйста, внесите необходимые изменения и отправьте задание снова.</p>
               
            @elseif($status === 'adopted')
                <p>Поздравляем! Вы успешно прошли тестовое задание и финальное собеседование.</p>
                <p>В ближайшее время с вами свяжется наш hr-менеджер для согласования деталей вашего трудоустройства.</p>
                <p>Благодарим вас за проделанную работу.</p>

            @elseif($status === 'deny')
                <p>Благодарим вас за участие в нашем отборе и за время, которое вы уделили выполнению тестового задания и собеседованию.</p>
                <p>К сожалению, мы не можем предложить вам позицию в нашей компании на данный момент.</p>
                <p>Это решение не уменьшает ваши профессиональные качества и навыки, и мы уверены, что вы найдете подходящую возможность в будущем.</p>
                <p>Мы желаем вам удачи в дальнейших поисках и надеемся, что вы не потеряете интерес к нашей компании в будущем.</p>

            @elseif($status === 'approved')
                <p>Поздравляем! Ваше задание было одобрено.</p>
                <p>В ближайшее время с вами свяжется наш менеджер для согласования деталей финального созвона.</p>
                <p>Благодарим вас за проделанную работу.</p>
                
            @elseif($status === 'failed')
                <p>К сожалению, ваше задание не было принято.</p>
                @if($comment)
                <div class="reason-box">
                    <p><strong>Комментарий:</strong></p>
                    <p>{{ $comment }}</p>
                </div>
                @endif
                <p>Это решение не является оценкой ваших профессиональных качеств и связано с текущими требованиями.</p>
            @endif

            @if($filePath)
                <p>К этому письму прикреплен файл с дополнительной информацией.</p>
            @endif

            <div class="contact-info">
                <p>Если у вас есть вопросы, вы можете связаться с нами:</p>
                <p>Email: <a href="mailto:hr@atwinta.com">hr@atwinta.com</a></p>
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