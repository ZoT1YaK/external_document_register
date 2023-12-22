<?php
    if(empty($_SESSION['logged_in'])){
        header('Location: /?page=login');
    }
    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == false){
        header('Location: /?page=login');
    }

    if(isset($_SESSION["error_message"])){
        unset($_SESSION["error_message"]);
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
                height: 100%;
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

            .grid-container {
                display: grid;
                gap: 10px;
                background-color: #4eb5f1;
                padding: 10px;
                width: auto;
                border-radius: 10px;
                grid-template-columns: auto;
            }

            .grid-container > a > button {
                font-family: Cambria, Cochin, Georgia, Times, "Times New Roman", serif;
                font-size: 17px;
                cursor: pointer;
            }

            .grid-item {
                height: 50px;
            }

            button {
                width: 100%;
                height: 50px;
            }

            @media only screen and (min-width: 600px) {
                .app {
                    height: unset;
                }

                .content {
                    align-items: center;
                }

                .grid-container {
                    width: 500px;
                }
            }
        </style>
    </head>
    <body>
        <div class="app">
            <div class="content">
                <h1>Welcome!</h1>
                <div class="grid-container">
                    <a class="grid-item" href="/?page=check">
                        <button>
                            View document
                        </button>
                    </a>
                    <a class="grid-item" href="/?page=register">
                        <button>   
                            Upload document
                        </button>
                    </a>
                    <a class="grid-item" href="?page=login">
                        <button>
                            Exit
                        </button>
                    </a>
                </div>
            </div>
        </div>
</html>