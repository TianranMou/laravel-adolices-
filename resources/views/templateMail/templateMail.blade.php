<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        :root{
            --white: #FFFFFF;
            --darkBlue: #0C2340;
            --lightBlue: #00B8DE;
            --lightYellow: #FF9900;
            --darkYellow: #FFBB00;
            --lightGray: #EDF3F4;
            --black: #000000;
            --red: #ff0000;
            --gray: #777777;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: var(--lightGray);
            color: var(--black);
        }

        .email-container {
            max-width: 90%;
            margin: 20px auto;
            background-color: var(--white);
            border: 1px solid var(--darkBlue);
            border-radius: 10px;
            overflow: hidden;
        }

        .email-header {
            text-align: center;
            background-color: var(--darkBlue);
            color: var(--white);
            padding: 20px;
            background-image: url('{{ asset('images/background_image.avif') }}');
            background-size: cover;
            background-position: center;
        }

        .email-header h1 {
            margin: 0;
            color: var(--darkBlue)
        }

        .email-header img {
            width: 100px;
            height: auto;
            margin-top: 10px;
            border-radius: 10px;
        }

        .email-body {
            padding: 20px;
            border-radius: 10px;
        }

        .email-footer {
            text-align: center;
            background-color: var(--darkBlue);
            color: var(--white);
            padding: 10px;
            font-size: 0.9em;
        }

        .email-footer p {
            margin: 0;
        }

        a {
            color: var(--lightBlue);
            text-decoration: none;
            background-color: var(--white);
        }

        #sign{
            color: var(--darkBlue);
            font-weight: bold;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Adolices</h1>
            <img src="{{ asset('images/icon.png') }}" alt="Logo">
        </div>
        <div class="email-body">
            <p>Bonjour,</p>
            @yield('content')
            <p>Cordialement,</p>
            <p id="sign">L'équipe Adolices</p>
        </div>
        <div class="email-footer">
            <p>© 2023 Adolices. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
