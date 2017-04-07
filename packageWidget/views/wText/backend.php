<?php

?>
<div class="widget wText">
    <div class="head">
        <i class="icon icon-wText"></i>
        <span class="title">Текст</span>
        <div class="btns"><i class="icon icon-cross js-delWidget"></i></div>
    </div>
    <div class="body">
        <div class="textField inputBox widgetTitle">
            <input placeholder="Заголовок блока" name="w[<?=$data['position']; ?>][wText][title]" type="text" value="<?=$data['title']; ?>" data-pattern="text">
        </div>
        <textarea placeholder="Текст статьи" name="w[<?=$data['position']; ?>][wText][text]" class="initialized js-ckeditor"><?=$data['content']; ?></textarea>
        <input type="hidden" name="w[<?=$data['position']; ?>][wText][id]" value="<?=$data['id']; ?>">
    </div>
</div>