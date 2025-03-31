<!DOCTYPE html>
<html>
<head>
    <title>Новая заявка с карьерной страницы</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ATWINTA</h1>
            <h2>Новая заявка с карьерной страницы</h2>
        </div>
        
        <div class="content">
            <table>
                <tr>
                    <th>Поле</th>
                    <th>Значение</th>
                </tr>
                <tr>
                    <td>Имя</td>
                    <td>{{ $applicationData['name'] }}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>{{ $applicationData['email'] }}</td>
                </tr>
                <tr>
                    <td>Телефон</td>
                    <td>{{ $applicationData['phone'] }}</td>
                </tr>
                <tr>
                    <td>Тип работ</td>
                    <td>{{ $applicationData['position'] }}</td>
                </tr>
                <tr>
                    <td>Дата отправки</td>
                    <td>{{ now()->format('d.m.Y H:i') }}</td>
                </tr>
            </table>
            
            <p style="margin-top: 20px;">Резюме кандидата прикреплено к этому письму.</p>
        </div>
    </div>
</body>
</html>