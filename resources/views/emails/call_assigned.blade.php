<!DOCTYPE html>
<html>

<head>
    <title>Назначен созвон</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .content {
            padding: 20px;
            background-color: #fff;
            border-radius: 0 0 5px 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }

        .footer {
            margin-top: 20px;
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>ATWINTA</h1>
            <p>Digital-агентство</p>
        </div>

        <div class="content">
            <h2>Назначен созвон!</h2>
            <table>
                <tr>
                    <th>Поле</th>
                    <th>Значение</th>
                </tr>
                <tr>
                    <td>Кандидат</td>
                    <td>{{ $candidateName }}</td>
                </tr>
                @if ($tutorName)
                    <tr>
                        <td>Тьютор</td>
                        <td>{{ $tutorName }}</td>
                    </tr>
                @endif
                <tr>
                    <td>HR-менеджер</td>
                    <td>{{ $hrManagerName }}</td>
                </tr>
                <tr>
                    <td>Дата</td>
                    <td>{{ $date }}</td>
                </tr>
                <tr>
                    <td>Время</td>
                    <td>{{ $time }}</td>
                </tr>
                <tr>
                    <td>Ссылка на созвон</td>
                    <td><a href="{{ $meetingLink }}">{{ $meetingLink }}</a></td>
                </tr>
            </table>

            <a href="{{ $meetingLink }}" class="button">Присоединиться к созвону</a>

            <div class="footer">
                <p>Пожалуйста, не забудьте присоединиться вовремя.</p>
                <p>Это письмо сгенерировано автоматически, пожалуйста, не отвечайте на него.</p>
            </div>
        </div>
    </div>
</body>

</html>
