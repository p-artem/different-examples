<?php

?>
<div class="widget wGallery">
    <div class="head">
        <i class="icon icon-camera"></i>
        <span class="title">Галерея</span>
        <div class="btns"><i class="icon icon-cross js-delWidget"></i></div>
    </div>
    <div class="body">
        <input type="hidden" name="w[<?=$data['position']; ?>][wGallery][id]" value="<?=$data['id']; ?>">
        <div class="textField inputBox widgetTitle">
            <input placeholder="Заголовок блока" name="w[<?=$data["position"]; ?>][wGallery][title]" type="text" value="<?=$data["title"]; ?>" data-pattern="text">
        </div>
        <?php if($data['content']): ?>
            <?php $cntImages = count($data['content']); ?>
            <?php foreach ($data['content'] as $keyImg => $image): ?>
                <div class="fileField">
                    <label><input name="w[<?=$data["position"]; ?>][wGallery][images][<?=$keyImg; ?>]" type="file" data-img-src="<?=$image['url']; ?>" class="js-addNextFileField"></label>
                    <input name="w[<?=$data["position"]; ?>][wGallery][oldImages][<?=$keyImg; ?>]" type="hidden" value="<?=$image['imageName']; ?>">
                </div>
            <?php endforeach;?>
            <div class="fileField">
                <label><input name="w[<?=$data["position"]; ?>][wGallery][<?=$cntImages;?>]" type="file" class="js-addNextFileField"></label>
            </div>
        <?php else: ?>
            <div class="fileField">
                <label><input name="w[][wGallery][]" type="file" class="js-addNextFileField"></label>
<!--                <input name="w[][wGallery]" type="hidden" value="">-->
            </div>
        <?php endif; ?>
    </div>
</div>