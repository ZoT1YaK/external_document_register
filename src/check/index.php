<?php
    if(empty($_SESSION['logged_in'])){
        header('Location: /?page=login');
    }
    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == false){
        header('Location: /?page=login');
    }

    $error_message = "";

    if(isset($_SESSION["error_message"])){
        $error_message = '<p>'.$_SESSION["error_message"].'</p>';
        unset($_SESSION["error_message"]);
    }
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="/../js/vendor/jquery.min.js"></script>
        <script src="/../js/vendor/webrtc_adapter.js"></script>
        <script src="/../js/vendor/zxing.min.js"></script>
        <script src="/../js/scanning.js"></script>
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

            #scan_camera {
                width: 100%;
            }

            #scan_camera > video {
                width:100%;
            }
            
            input[type="text"] {
                height: auto;
                border: 1px solid rgba(0, 0, 0, 0.2);
            }

            .reg_date {
                display: flex;
                flex-direction: column;
                width: min-content;
                gap: 20px;
            }

            .reg_date > div {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            input[type="date"] {
                height: auto;
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
            
            .input-form {
                background-color: white;
                padding: 0.5rem;
                box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            }
            
            .input-container {
                display: flex;
                flex-direction: column;
                gap: 8px;
                margin: 10px;
            }

            .input-container > label, .input-container > div > label {
                font-size: 20px;
                font-style: italic;
            }

            .input-container > div > div > label {
                font-size: 17px;
                font-style: italic;
            }

            .input-container > input , select, div > div > input {
                font-size: 15px;
            }

            .title {
                margin: 10px;
                margin-bottom: 20px;
                font-size: 25px;
            }

            .error {
                font-size:12px;
                color:red;
            }

            .error_message_highlight {
                font-weight: 700;
                color: black;
            }

            @media only screen and (min-width: 600px) {
                .content {
                    align-items: center;
                }
                
                .back-container {
                    width: 500px;
                }

                .input-form {
                    width: 500px;
                }

                #scan_camera {
                    width: 500px;
                }

                #scan_camera > video {
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

                <div id="scan_camera">
                    <!-- <video id="video"></video> -->
                </div>

                <div class="input-form">
                    <div class="title">
                        <label>Search conditions</label>
                    </div>
                    <div class="form">
                        <form method="POST" action="/?page=check_list_function">
                            <input type="hidden" name="msg_type" value="3020"/>
                            <div class="input-container">
                                <label>Number </label>
                                <input type="text" name="check_number"/>
                            </div>
                            <div class="input-container">
                                <div class="reg_date">
                                <label>Reg. Date </label>
                                    <div>
                                        <label>From </label>
                                        <input type="date" name="check_from"/>
                                    </div>
                                    <div>
                                        <label>To </label>
                                        <input type="date" name="check_to"/>
                                    </div>
                                </div>
                            </div>
                            <div class="input-container">
                                <label for="states">State </label>
                                <select style="width: fit-content;" name="check_state" id="states">
                                    <option value="" selected="selected"></option>
                                    <option value="27769">New</option>
                                    <option value="28873">Need approve</option>
                                    <option value="27770">Active</option>
                                    <option value="27771">Archived</option>
                                    <option value="27772">Rejected</option>
                                </select>
                                <div class="error">
                                    <?php echo $error_message; ?>
                                </div>
                            </div>
                            <div class="button-container">
                                <input type="submit" value="Find">
                            </div>
                        </form>
                    </div>
                </div>

                <div id="scan_result"></div>
            </div>
        </div>
    </body>
</html>