<?php
    // Authenticate
    if(empty($_SESSION['logged_in'])){
        header('Location: /?page=login');
    }
    if(empty($_SESSION['logged_in']) || $_SESSION['logged_in'] == false){
        header('Location: /?page=login');
    }
    if(empty($_SESSION["documents"]) ){
        header('Location: /?page=check');
    }

    // Hardcoded url for func
    $uri_func = '/?page=check_list_function';
    
    // Ссылки на смену сортировки
    $current_sort = 'num';
    if (! empty($_SESSION['sort'])){
        $current_sort = $_SESSION['sort'];
    }

    $current_order = 'asc';
    if (! empty($_SESSION['order'])){
        $current_order = $_SESSION['order'];
    }

    // Make Active current sort
    $num_active_sort_class = '';
    if($current_sort === 'num') {
        $num_active_sort_class = 'active';
    }
    $reg_date_active_sort_class = '';
    if($current_sort === 'reg_date') {
        $reg_date_active_sort_class = 'active';
    }

    $state_active_sort_class = '';
    if($current_sort === 'state') {
        $state_active_sort_class = 'active';
    }

    $author_active_sort_class = '';
    if($current_sort === 'author') {
        $author_active_sort_class = 'active';
    }

    $reverse_order =  $current_order === 'asc' ? 'desc' : 'asc';

    $uri_sort_num = $uri_func . '&sort=num&order=' . ($num_active_sort_class === 'active' ? $reverse_order : 'asc');
    $uri_sort_date = $uri_func . '&sort=reg_date&order=' . ($reg_date_active_sort_class === 'active' ? $reverse_order : 'asc');
    $uri_sort_state = $uri_func . '&sort=state&order=' . ($state_active_sort_class === 'active' ? $reverse_order : 'asc');
    $uri_sort_author = $uri_func . '&sort=author&order=' . ($author_active_sort_class === 'active' ? $reverse_order : 'asc');

    // Текущее правило сортировки для кнопок prev/next
    $current_sort_uri_props = '&sort=' . $current_sort . '&order=' . $current_order;
    // Расчёт страницы для кнопок prev/next
    $last_list_page = 1;
    if (! empty($_SESSION['last_list_page'])){
        $last_list_page = $_SESSION['last_list_page'];
    }
    $current_list_page = 1;
    if (! empty($_SESSION['list_page'])){
        $current_list_page = $_SESSION['list_page'];
    }
    $uri_prev_list_page = $uri_func . '&list_page=' . ($current_list_page - 1) . $current_sort_uri_props;
    $uri_next_list_page = $uri_func . '&list_page=' . ($current_list_page + 1) . $current_sort_uri_props;


    // Данные списка
    $data = $_SESSION["documents"];
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
            
            table {
                border: 1px solid #ccc;
                border-collapse: collapse;
                margin: 0;
                padding: 0;
                width: 100%;
                table-layout: fixed;
            }

            table caption {
                font-size: 1.5em;
                margin: .5em 0 .75em;
            }

            table tr {
                background-color: #f8f8f8;
                border: 1px solid #ddd;
                padding: .35em;
            }

            table th, table td {
                padding: .625em;
                text-align: center;
            }

            table th {
                font-size: .85em;
                letter-spacing: .1em;
                text-transform: uppercase;
            }

            table th.active {
                display: flex;
                justify-content: center;
                align-items: center;
            }

            table th.active a {
                font-weight: 700;
                margin-right: 5px;
            }

            table th a {
                text-decoration:none;
            }

            table th.active span{
                display: none;
            }

            table th.active span.asc{
                width: 16px;
                height: 16px;
                display: inline-block;
                background-image: url("https://cdn4.iconfinder.com/data/icons/universal-arrow-collection/64/Arrow_Direction_Navigation_Interface_UI-70-512.png");
                background-size: cover;
            }

            table th.active span.desc{
                width: 16px;
                height: 16px;
                display: inline-block;
                background-image: url("https://static.thenounproject.com/png/1624633-200.png");
                background-size: cover;
            }

            table th > a:hover {
                cursor: pointer;
            }

            @media screen and (max-width: 600px) {
                table {
                    border: 0;
                }

                table caption {
                    font-size: 1.3em;
                }

                table thead {
                    display: flex;
                    flex-direction: row;
                    justify-content: center;
                    flex-wrap: wrap;
                }
            
                table thead > tr {
                    font-size: 10px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }
                
                table tr {
                    border-bottom: 3px solid #ddd;
                    display: block;
                    margin-bottom: .625em;
                }
                
                table td {
                    border-bottom: 1px solid #ddd;
                    display: block;
                    font-size: .8em;
                    text-align: right;
                }
                
                table td::before {
                    content: attr(data-label);
                    float: left;
                    font-weight: bold;
                    text-transform: uppercase;
                }
                
                table td:last-child {
                    border-bottom: 0;
                }
            }

            button {
                background: #dddddd;
                border: 1px solid #ddd;
                border-radius: 10px;
                padding: 5px 15px 5px 15px;
            }
            
            button:hover {
                background: #afafaf;
                cursor: pointer;
            }

            .prev-button , .next-button {
                display: flex;
                width: 60px;
                height: 30px;
                background: #dddddd;
                color: black;
                border-radius: 20px;
                justify-content: center;
                align-items: center;
                margin: 2px 8px 2px 8px;
                text-decoration:none;
            }

            .prev-button:hover , .next-button:hover {
                background: #afafaf;
                cursor: pointer;
            }

            .previous-next-button {
                display: flex;
                flex-direction: row;
                justify-content: center;
                align-items: center;
                font-size: 20px;
            }
        </style>
    </head>
    <body>
        <div class="app">
            <div class="content">
                <div class="back-container">
                    <div class="back-button">
                        <a href="/?page=check">Back</a>
                    </div>
                </div>

                <div class="previous-next-button">
                    <?php if ($current_list_page > 1) : ?>
                        <a class="prev-button" href="<?= $uri_prev_list_page ?>">&laquo;</a>
                    <?php endif; ?>

                    <?php if ($last_list_page != 1) : ?>
                        (<?= $current_list_page . ' of ' . $last_list_page ?>)
                    <?php endif; ?>

                    <?php if ($current_list_page < $last_list_page) : ?>
                        <a class="next-button" href="<?= $uri_next_list_page ?>">&raquo;</a>
                    <?php endif; ?>
                </div>

                <form method="POST" action="/?page=check_function">
                    <table id="myTable">
                        <caption style="font-size: 24px;">List of documents</caption>
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col" class="<?= $num_active_sort_class ?>"><a href="<?= $uri_sort_num ?>">Number</a><span class="<?= $current_order?>"></span></th>
                                <th scope="col" class="<?= $state_active_sort_class ?>"><a href="<?= $uri_sort_state ?>">State</a><span class="<?= $current_order?>"></span></th>
                                <th scope="col" class="<?= $reg_date_active_sort_class ?>"><a href="<?= $uri_sort_date ?>">Reg. date</a><span class="<?= $current_order?>"></span></th>
                                <th scope="col" class="<?= $author_active_sort_class ?>"><a href="<?= $uri_sort_author ?>">Author</a><span class="<?= $current_order?>"></span></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($data as $row): ?>
                            <tr>
                                <?php foreach($row as $key => $attr): ?>
                                    <?php if ($key === 0) : ?>
                                        <td scope="row" data-label="">
                                            <button type="submit" value="<?=$row[0]['value']?>" name="select_document">View</button>
                                        </td>
                                    <?php endif; ?>
                                    <td scope="row" data-label="<?= $attr['label'] ?>"><?=$attr['value']?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </form>

                <div class="previous-next-button">
                    <?php if ($current_list_page > 1) : ?>
                        <a class="prev-button" href="<?= $uri_prev_list_page ?>">&laquo;</a>
                    <?php endif; ?>

                    <?php if ($last_list_page != 1) : ?>
                        (<?= $current_list_page . ' of ' . $last_list_page ?>)
                    <?php endif; ?>

                    <?php if ($current_list_page < $last_list_page) : ?>
                        <a class="next-button" href="<?= $uri_next_list_page ?>">&raquo;</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </body>
</html>