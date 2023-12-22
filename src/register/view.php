<?php
    if(empty($_SESSION['logged_in']) || $_SESSION['logged_in'] == false){
        header('Location: /?page=login');
    }
    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == false){
        header('Location: /?page=login');
    }

    $num = '';
    if (! empty($_GET['num'])){
        $num = $_GET['num'];
    }
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body {
                background-color: rgba(158, 242, 248, 0.096);
                margin: 0;
            }

            .app {
                display: flex;
                flex-direction: column;
                justify-content: center;
                font-family: Cambria, Cochin, Georgia, Times, "Times New Roman", serif;
            }

            .content {
                display: flex;
                flex-direction: column;
                align-items: stretch;
                justify-content: center;
                gap: 20px;
                margin: 20px;
            }
            
            .back-button {
                width: 100px;
            }

            .back-button > a {
                display:block;
                padding:0.3em 1.2em;
                margin:0 0.3em 0.3em 0;
                border-radius:2em;
                box-sizing: border-box;
                text-decoration:none;
                font-family:'Roboto',sans-serif;
                font-weight:300;
                color:#FFFFFF;
                background-color:#4eb5f1;
                text-align:center;
                transition: all 0.2s;
            }

            .back-button > a:hover {
                background-color:#6ac0f2;
            }
            
            .read-form {
                background-color: white;
                padding: 2rem;
                box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            }
            
            .read-container {
                display: flex;
                flex-direction: column;
                gap: 8px;
                margin: 10px;
            }

            .read-container > p {
                text-align: center;
            }

            @media only screen and (min-width: 600px) {
                .content {
                    align-items: center;
                }

                .back-container {
                    width: 500px;
                }

                .read-form {
                    width: 500px;
                }
            }
        </style>
    </head>
    <body>
        <div class="app">
            <div class="content">
                <div class="back-container">
                    <div class="back-button">
                        <a href="/?page=home">Back</a>
                    </div>
                </div>
                
                <div class="read-form">
                    <div class="form">
                        <div class="read-container">
                            <?php if (empty($num)) : ?>
                                <p><span style="color: red;">Failed to get registered document number!</span></p>
                            <?php else : ?>
                                <p>Document <span style="color: green;">"<?= $num ?>"</span> was registered!</p>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>