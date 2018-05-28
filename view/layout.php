<!DOCTYPE html>
<html lang="zh-cmn-Hans">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Argue</title>

    <meta name="author" content="xiaochi">

    <meta name="HandheldFriendly" content="true">
    <meta name="apple-mobile-web-app-title" content="Step-PHP">

    <link rel="stylesheet" href="/vendor/bootstrap-4.1.1-dist/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="/vendor/bootstrap-4.1.1-dist/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>

    <!-- development version, includes helpful console warnings -->
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>

    <script>
    // 当用户操作到需要登录的操作时，登录
    $(document).ajaxComplete( function (e,xhr) {
        if (xhr.responseText === 'need_login') {
            console.log('need_login');
            location.href='/login?back='+encodeURIComponent(location.href);
        }
    });

    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function() {
    'use strict';
    window.addEventListener('load', function() {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {
        form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
        });
    }, false);
    })();

    console.log(<?= json_encode($_SERVER) ?>);
    </script>

</head>

<body>

    <div style="margin: 2%;
    border-bottom: 1px solid;
    color: #5f5b5b;">

        <?php if($GLOBALS['cur_user']): ?>
        <div style="float:right"><?= htmlspecialchars($GLOBALS['cur_user']['name']) ?>(<?= htmlspecialchars($GLOBALS['cur_user']['nickname']) ?>)</div>
        <?php else: ?>
        <div style="float:right"><a href="/login?<?= http_build_query(['back'=>$_SERVER['REQUEST_URI']]) ?>">登录</a></div>
        <?php endif ?>

        <h2>辩论网</h2>
    </div>

    <div id="conentWrap" style="margin: 2%"><?php include $_inner_tpl_list['content'] ?></div>

</body>

</html>