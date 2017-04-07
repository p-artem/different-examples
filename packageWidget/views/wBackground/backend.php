<?php
?>
<div class="widget wImage">
    <div class="head">
        <i class="icon icon-camera"></i>
        <span class="title">Главное фото</span>
        <div class="btns"><i class="icon icon-cross js-delWidget"></i></div>
    </div>
    <div class="body">
        <div class="textField inputBox widgetTitle">
            <input type="hidden" name="w[<?=$data['position']; ?>][wBackground][id]" value="<?=$data['id']; ?>">
            <input placeholder="Заголовок блока" name="w[<?=$data["position"]; ?>][wBackground][title]" type="text" value="<?=$data["title"]; ?>" data-pattern="text">
        </div>
        <div class="fileField">
            <label>
                <?php if($data['content']): ?>
                    <input name="w[<?=$data["position"]; ?>][wBackground][content]" type="file" data-img-src="<?=$data['content']['url']; ?>">
                    <input name="w[<?=$data["position"]; ?>][wBackground][oldImage]" type="hidden" value="<?=$data['content']['imageName']; ?>">
                <?php else: ?>
                    <input name="w[][wBackground]" type="file">
                <?php endif; ?>
            </label>
        </div>
        <p>Загрузите фотографию</p>
    </div>
</div>