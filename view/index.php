<style>
    div {
        /* border: 1px solid red; */
    }

    .username {
        font-weight: bold;
    }

    .i-eye {
        color: white;
    }

    .i-yes {
        color: white;
        background-color: green;
    }

    .i-no {
        color: white;
        background-color: red;
    }

    .yes-group {
        margin: .5em 0;
        padding: .5em;
        font-size: small;
        border: 1px solid green;
    }

    .no-group {
        margin: .5em 0;
        padding: .5em;
        font-size: small;
        border: 1px solid red;
    }
</style>
<ul>
    <?php //foreach ($argue_list as $key => $argue): ?>
    <li>
        <?php include 'block/question.php' ?>
    </li>
    <?php //endforeach ?>
</ul>
<!-- <div class="content" style="display: none">
    <form action="/new_argue" method="POST" style="border: 1px solid; padding: 1rem;">
        <div><textarea name="title" id="" cols="30" rows="10"></textarea></div>
        <input type="submit" value="新建辩题">
    </form>
</div> -->