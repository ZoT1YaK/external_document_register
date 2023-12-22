<?php
    if(empty($_SESSION['logged_in'])){
        header('Location: /?page=login');
    }
    if(empty($_SESSION['logged_in']) || $_SESSION['logged_in'] == false){
        header('Location: /?page=login');
    }
    if(empty($_SESSION["documents"]) ){
        header('Location: /?page=check');
    }

    $error_message = "";

    if(isset($_SESSION["error_message"])){
        $error_message = '<p>'.$_SESSION["error_message"].'</p>';
        unset($_SESSION["error_message"]);
    }

    $cfg = require_once(__DIR__ . '/../config/i_default.php');

    $cfg_custom = require_once(__DIR__ . '/../../i_custom.php');

    foreach ($cfg_custom as $key => $value) {
        $cfg[$key] = $value;
    }

    $data = $_SESSION["document"];

    $doc_state = $data[0][0]["value"];

    function isMobile() {
        return preg_match("/(android|webos|avantgo|iphone|ipad|ipod|blackberry|iemobile|bolt|boost|cricket|docomo|fone|hiptop|mini|opera mini|kitkat|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }

    $isMobileDevice = false;

    if($_SESSION['username'] != $cfg['admin_login'] && $_SESSION['password'] != $cfg['admin_password']) {
        $uri_func = '/?page=check_approve_function';

        $obj_id = $_SESSION['object_id'];
    
        $uri_approve = $uri_func . '&action=approve&obj_id=' . $obj_id;
    
        $uri_decline = $uri_func . '&action=decline&obj_id=' . $obj_id;
    
        
    
        $fileId = $data[0][count($data[0])-1]["value"];
    
        $file_name = null;
        $file_base64_pdf = null;
        if(! empty($_SESSION['files']) && ! empty($_SESSION['files'][$fileId]) && !empty($_SESSION['files'][$fileId]['name'])) {
            $file_name = $_SESSION['files'][$fileId]['name'];
            if (!empty($_SESSION['files'][$fileId]['base64_pdf'])){
                $file_base64_pdf = $_SESSION['files'][$fileId]['base64_pdf'];
            }
        }
    
        $fileFolderName = '';
    
        $file_no_type = '';
        
        if (! empty($file_name)) {
            $tempFileName = __DIR__ . '/../public/uploads/'.$file_name ;
        
            if (!isMobile()) {
                $file_types = ["jpg", "png", "jpeg", "gif", "pdf", "docx", "doc", "excel", "odt", "txt"];
                $outDir = __DIR__ . '/../public/convertedFiles';
        
        
                $file_name_split = explode(".", $file_name);
    
                $file_type = $file_name_split[count($file_name_split)-1];
        
                array_pop($file_name_split);
        
                $file_no_type = $file_name_split[0];
        
                $fileFolderName = md5($file_name);
    
                $files = scandir($outDir);
    
                $files = array_diff($files, array('.', '..'));
    
                $fileExist = false;
    
                foreach ($files as $file) {
                    if ($file == $fileFolderName){
                        $sub_files = scandir( __DIR__ . '/../public/convertedFiles/' . $file);
                        
                        if($sub_files['2'] == $file_no_type . '.pdf'){
                            
                            $file_contents = file_get_contents(__DIR__ . '/../public/convertedFiles/' . $file . '/' . $sub_files['2']);
                            
                            if((string)base64_encode($file_contents) == $file_base64_pdf && !empty($file_base64_pdf)) {
                                $fileExist = true;
                            }
                        };
                    }
                }
        
                if(!$fileExist) {
                    if(strpos($cfg['libreoffice_path'], ':')) {
                        $command = "\"" . $cfg['libreoffice_path'] . "\" --headless --convert-to pdf \"$tempFileName\" --outdir \"$outDir/$fileFolderName\"";
                    }
                    else {
                        $command = "HOME=\"" . __DIR__ . "/../../tmp\" \"" . $cfg['libreoffice_path'] . "\" --headless --convert-to pdf \"$tempFileName\" --outdir \"$outDir/$fileFolderName\" 2>&1";
                    }
        
                    exec($command, $output, $returnCode);
        
                    if ($returnCode === 0) {
                        //
                    } else {
                        echo "Conversion failed. Error code: $returnCode";
                        echo "Output: " . implode("\n", $output) . "\n";
                        die();
                    }
    
                    $file_contents = file_get_contents(__DIR__ . '/../public/convertedFiles/' . $fileFolderName . '/' . $file_no_type . '.pdf');
            
                    $_SESSION['files'][$fileId]['base64_pdf'] = (string)base64_encode($file_contents);

                    
                }
                $src_pdf_link = "convertedFiles/" . $fileFolderName . "/" . $file_no_type .".pdf";
                
            } else {
                $file_types = ["jpg", "png", "jpeg", "gif"];
                $isMobileDevice = true;
                $file_name_split = explode(".", $file_name);
                $file_type = $file_name_split[count($file_name_split)-1];
                $src_img_link = "uploads/" . $file_name;
            }
        }
    } else {
        $file_type = "jpg";
        $file_name = "diagram.jpg";
        $file_name_pdf = "diagram.pdf";
        
        if (!isMobile()) {
            $file_types = ["jpg", "png", "jpeg", "gif", "pdf", "docx", "doc", "excel", "odt", "txt"];
            $isMobileDevice = false;
            $src_pdf_link = $file_name_pdf;
        } else {
            $file_types = ["jpg", "png", "jpeg", "gif"];
            $isMobileDevice = true;
            $src_img_link = $file_name;
        }
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
                margin: 20px;
                width: 1600px;
                gap: 20px;
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
            
            .read-container > p, a {
                height: auto;
                margin-top: 5px;
                margin-bottom: 5px;
                text-wrap:wrap;
                word-wrap: break-word;
                word-break: break-word;
            }
            
            .read-form {
                background-color: white;
                padding: 2rem;
                box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            }
            
            .read-container {
                display: flex;
                flex-direction: column;
                gap: 1px;
                margin: 10px;
                border: 1px solid;
                border-radius: 15px;
                border-color: #00000024;
                padding: 8px 12px 8px 12px;
            }

            .myIframe {
                position: relative;
                overflow: auto;
                width: 100%;
                height: 700px;
            }

            .myIframe iframe {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 700px;
            }

            .tab {
                overflow: hidden;
                border: 1px solid #ccc;
                background-color: #f1f1f1;
                border-radius: 10px;
                align-self: center;
            }

            .tab button {
                background-color: inherit;
                float: left;
                border: none;
                outline: none;
                cursor: pointer;
                padding: 14px 16px;
                transition: 0.3s;
                font-size: 13px;
            }

            .tab button:hover {
                background-color: #ddd;
            }

            .tab button.active {
                background-color: #ccc;
            }

            .tabcontent {
                width: 100%;
                animation: fadeEffect 1s;
                flex-direction: column;
                align-items: stretch;
                display: flex;
            }

            @keyframes fadeEffect {
                from {opacity: 0;}
                to {opacity: 1;}
            }

            .approvement-buttons a {
                display: flex;
                width: 70px;
                height: 40px;
                color: black;
                border-radius: 15px;
                justify-content: center;
                align-items: center;
                margin: 2px 15px 2px 8px;
                text-decoration:none;
            }

            .approve-button {
                background-color: #00ff0066;
            }

            .decline-button {
                background-color: #f54e4e99;
            }
            
            .approvement-buttons a:hover {
                background: #afafaf;
                cursor: pointer;
            }

            .approvement-buttons {
                display: flex;
                flex-direction: row;
                justify-content: space-around;
                align-items: center;
                padding-top: 20px;
            }

            .error {
                font-size:12px;
                color:red;
            }

            .error_message_highlight {
                font-weight: 700;
                color: black;
            }
            
            @media only screen and (min-width: 670px) {
                .content .back-container {
                    align-self: start;
                }

                .content {
                    align-items: center;
                    flex-direction: row;
                    
                }
                
                .back-container {
                    width: 500px;
                }

                .read-form {
                    width: 587px;
                }

                .tab button {
                    font-size: 17px;
                }

                .tabcontent {
                    align-items: center;
                }
            }

            @media only screen and (max-width: 1620px) {
                .content {
                    flex-direction: column;
                    width: unset;
                }
            }
        </style>
    </head>
    <body>
        <div class="app">
            <div class="content">
                <div class="back-container">
                    <div class="back-button">
                        <a href="/?page=check_list_function">Back</a>
                    </div>
                </div>

                <?php if($isMobileDevice) : ?>
                    <?php if (! empty($file_name) && in_array($file_type, $file_types)) : ?>
                        <div class="tab">
                            <button class="tablinks" id="defaultOpen" onclick="openView(event, 'card')">Document</button>
                            <button class="tablinks" onclick="openView(event, 'file_frame')">Preview file</button>
                        </div>  
                    <?php endif ?>
                <?php endif; ?>

                <div id="card" class="tabcontent">
                    <?php foreach($data as $key=>$value): ?>
                    <div class="read-form" >
                        <div class="form">
                            <form>
                                <?php foreach($value as $key=>$attr): ?>
                                <div class="read-container">
                                    <?php if($attr['type'] == 'file') : ?>
                                        <label>File: </label>
                                        <a href="?download=<?= $attr['value'] ?>" download="<?= $attr['label'] ?>"><?= $attr['label'] ?></a>
                                    <?php elseif($attr['type'] == 'field') : ?>
                                        <label><?= $attr['label']?>: </label>
                                        <p><b><?= (empty($attr['value']) ? "-" : $attr['value'] ); ?></b></p>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                                <div class="error">
                                    <?php echo $error_message; ?>
                                </div>
                                <?php if($doc_state == 'Need approve') : ?>
                                    <div class="approvement-buttons">
                                        <a class="approve-button" href="<?= $uri_approve ?>">Approve</a>
                                        <a class="decline-button" href="<?= $uri_decline ?>">Decline</a>
                                    </div>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if (! empty($file_name) && in_array($file_type, $file_types)) : ?>
                    <div id="file_frame" class="tabcontent">
                        <div class="myIframe">
                            <?php 
                                if (!$isMobileDevice) {
                                    echo '<iframe src="' . $src_pdf_link . '"></iframe>';
                                } else {
                                    echo '<img style="width: 100%; height: auto;" src="' . $src_img_link . '" alt="Image">';
                                }
                            ?>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>
        <script>
            document.getElementById("defaultOpen").click();

            function openView(evt, viewName) {
                var i, tabcontent, tablinks;
                tabcontent = document.getElementsByClassName("tabcontent");
                for (i = 0; i < tabcontent.length; i++) {
                    tabcontent[i].style.display = "none";
                }
                tablinks = document.getElementsByClassName("tablinks");
                for (i = 0; i < tablinks.length; i++) {
                    tablinks[i].className = tablinks[i].className.replace(" active", "");
                }
                document.getElementById(viewName).style.display = "flex";
                evt.currentTarget.className += " active";
            }
        </script>
    </body>
</html>