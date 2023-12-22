<?php
    session_destroy();
    $_SESSION['logged_in'] = false;
    
    $error_message = "";

    if(isset($_SESSION["error_message"])){
        $error_message = '<p>'.$_SESSION["error_message"].'</p>';
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
                align-items: stretch;
                justify-content: center;
                flex-direction: column;
                height: 100%;
                font-family: Cambria, Cochin, Georgia, Times, "Times New Roman", serif;
            }
            
            input[type="text"],
            input[type="password"] {
                height: 25px;
                border: 1px solid rgba(0, 0, 0, 0.2);
            }
            
            input[type="submit"] {
                margin-top: 10px;
                cursor: pointer;
                font-size: 15px;
                background: #01d28e;
                border: 1px solid #01d28e;
                color: #fff;
                padding: 10px 20px;
            }
            
            input[type="submit"]:hover {
                background: #6cf0c2;
            }
            
            .button-container {
                display: flex;
                justify-content: center;
            }
            
            .login-form {
                background-color: white;
                padding: 2rem;
                box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
                margin: 20px;
            }
            
            .input-container {
                display: flex;
                flex-direction: column;
                gap: 8px;
                margin: 10px;
            }

            .title {
                margin: 10px;
                margin-bottom: 20px;
                font-size: 25px;
            }

            .input-container > label, .input-container > div > label {
                font-size: 20px;
                font-style: italic;
            }

            .input-container > input {
                font-size: 15px;
            }

            .error {
                font-size:12px;
                color:red;
            }

            @media only screen and (min-width: 600px) {
                .app {
                    align-items: center;
                    height: unset;
                }

                .login-form {
                    width:500px;
                }
            }
        </style>
    </head>
    <body>
        <div class="app">
            <div class="login-form">
                <div class="title">
                    <label>Sign in</label>
                </div>
                <div class="form">
                    <form method="POST" action="/?page=login_function">
                        <input type="hidden" name="msg_type" value="9000"/>
                        <div class="input-container">
                            <label>Login </label>
                            <input type="text" name="username" pattern="^mm\..+$" placeholder="Starts with: 'mm.'" required/>
                        </div>
                        <div class="input-container">
                            <label>Password </label>
                            <input type="password" name="password" required/>
                            <div class="error">
                                <?php echo $error_message; ?>
                            </div>
                        </div>
                        <div class="button-container">
                            <input type="submit" value="Login">
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </body>
</html>