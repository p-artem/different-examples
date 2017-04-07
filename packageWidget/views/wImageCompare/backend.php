<?php
$arrPosition = ['G' => 'По горизонтали', 'V' => 'По вертикали'];
?>
<div class="widget wImageCompare">
    <div class="head">
        <i class="icon icon-wImage"></i>
        <span class="title">Сравнение фотографий</span>
        <div class="btns"><i class="icon icon-cross js-delWidget"></i></div>
    </div>
    <div class="body">
        <div class="textField inputBox widgetTitle">
            <input type="hidden" name="w[<?=$data['position']; ?>][wImageCompare][id]" value="<?=$data['id']; ?>">
            <input placeholder="Заголовок блока" name="w[<?=$data['position']?>][wImageCompare][title]" type="text" value="<?=$data['title']?>" data-pattern="text">
        </div>
        <?php if($data['content']): ?>
            <div class="row">
                <div class="col">
                    <div class="fileField">
                        <label><input name="w[<?=$data['position']?>][wImageCompare][content][0]" type="file" data-img-src="<?=$data['content']['images'][0]['url']; ?>"></label>
                        <input name="w[<?=$data['position']?>][wImageCompare][oldImages][0]"  type="hidden" value="<?=$data['content']['images'][0]['imageName']; ?>">
                    </div>
                    <div class="fileField">
                        <label><input name="w[<?=$data['position']?>][wImageCompare][content][1]" type="file" data-img-src="<?=$data['content']['images'][1]['url']; ?>" ></label>
                        <input name="w[<?=$data['position']?>][wImageCompare][oldImages][1]"  type="hidden" value="<?=$data['content']['images'][1]['imageName']; ?>">
                    </div>
                </div>
                <div class="col">
                    <p>Сравнивать</p>
                    <?php foreach ($arrPosition as $key => $item): ?>
                        <?php $checked = ($key  == $data['content']['position']) ? 'checked' : '';?>
                        <label><input type="radio" name="w[<?=$data['position']?>][wImageCompare][position]" <?=$checked; ?> value="<?=$key; ?> "><span><?=$item; ?></span></label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col">
                    <div class="fileField">
                        <label><input name="w[][wImageCompare][0]" type="file"></label>
                    </div>
                    <div class="fileField">
                        <label><input name="w[][wImageCompare][1]"  type="file"></label>
                    </div>
                </div>
                <div class="col">
                    <p>Сравнивать</p>
                    <label><input type="radio" name="w[][wImageCompare][position]" checked value="G"><span>По горизонтали</span></label>
                    <label><input type="radio" name="w[][wImageCompare][position]" value="V"><span>По вертикали</span></label>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>