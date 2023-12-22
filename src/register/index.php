<?php
    if(empty($_SESSION['logged_in']) || $_SESSION['logged_in'] == false){
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

    $doc_types = $_SESSION['document_types'];
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
            
            input[type="text"] {
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

            .input-container > label {
                font-size: 20px;
                font-style: italic;
            }

            .input-container > input , select {
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

            #unlimited, #valid_to, #start_date {
                width: fit-content;
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

                <div class="input-form">
                    <div class="title">
                        <label>Upload document</label>
                    </div>
                    <div class="form">
                        <form method="POST" action="/?page=register_function" enctype="multipart/form-data">
                            <input type="hidden" name="msg_type" value="3000"/>
                            <div class="input-container">
                                <label>File </label>
                                <input id="input_file" type="file" name="register_file" style="width: 200px;" accept=".jpg, .png, .jpeg, .gif, .pdf, .docx, .doc, .excel, .odt, .txt" required/>
                            </div>
                            <div class="input-container">
                                <label>Description </label>
                                <textarea style="resize: none;" name="register_description" rows="6"></textarea>
                                <div class="error">
                                    <p><?= $error_message; ?></p>
                                </div>
                            </div>
                            <div class="input-container" id="start_date">
                                <label>Start date </label>
                                <input type="date" name="register_start_date"/>
                            </div>
                            <div class="input-container" id="valid_to">
                                <label>Valid to </label>
                                <input type="date" name="register_valid_to"/>
                            </div>
                            <div class="input-container">
                                <label>Unlimited </label>
                                <input style="width:18px; height:18px" type="checkbox" name="register_unlimited" id="unlimited" onchange="onClickCheckbox()"/>
                            </div>
                            <div class="input-container">
                                <label for="doc_types">Document type </label>
                                <select style="width: fit-content;" name="register_doc_type" id="doc_types" required>
                                    <option value="" selected="selected"></option>
                                    <?php foreach($doc_types as $type): ?>
                                        <option value="<?= $type['id'] ?>"><?= $type['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="button-container">
                                <input type="submit" value="Register">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function onClickCheckbox() {
                if (document.getElementById('unlimited').checked) {
                    document.getElementById("valid_to").style.display = 'none';
                } else {
                    document.getElementById("valid_to").style.display = 'flex';
                }
            }
        </script>
    </body>
</html>