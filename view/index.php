
<ul>
<?php foreach ($argue_list as $key => $argue): ?>
<li>
    <a href="/a/<?= $argue['id'] ?>"><?= htmlspecialchars($argue['title']) ?></a>
</li>
<?php endforeach ?>
</ul>
<div class="content">
    <form action="/new_argue" method="POST" style="border: 1px solid; padding: 1rem;" >
        <div><textarea name="title" id="" cols="30" rows="10"></textarea></div>
        <input type="submit" value="新建辩题">
    </form>
</div>

