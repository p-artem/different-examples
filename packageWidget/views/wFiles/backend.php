<?php
$lastIndex = 0;
?>

<div class="widget wFiles">
    <div class="head">
        <i class="icon icon-wFiles"></i>
        <span class="title">Файлы</span>
        <div class="btns"><i class="icon icon-cross js-delWidget"></i></div>
    </div>
    <div class="body">
        <div class="textField inputBox widgetTitle">
            <input type="hidden" name="w[<?=$data['position']; ?>][wFiles][id]" value="<?=$data['id']; ?>">
            <input placeholder="Заголовок блока" name="w[<?=$data['position']?>][wFiles][title]" type="text" value="<?=$data['title']?>" data-pattern="text">
        </div>
        <?php if($data['content']): ?>
            <?php $lastIndex = count($data['content']); ?>
            <?php foreach($data['content'] as $keyItem => $item): ?>
                <div class="fileField file">
                    <label><input name="w[<?=$data['position']?>][wFiles][<?=$keyItem?>]" type="file" data-file-src="<?=$item['url']?>"></label>
                    <input name="w[<?=$data["position"]; ?>][wFiles][oldFiles][<?=$keyItem; ?>]" type="hidden" value="<?=$item['translit']; ?>">
                </div>
            <?php endforeach; ?>
            <div class="fileField file">
                <label><input name="w[<?=$data["position"]; ?>][wFiles][<?=$lastIndex; ?>]" type="file" class="js-addNextFileField "></label>
            </div>
        <?php else: ?>
            <div class="fileField file">
                <label><input name="w[][wFiles][]" type="file" class="js-addNextFileField "></label>
            </div>
        <?php endif; ?>
    </div>
</div>
