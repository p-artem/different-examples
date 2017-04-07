<div class="widget wVideo">
    <div class="head">
        <i class="icon icon-wVideo"></i>
        <span class="title">Видео</span>
        <div class="btns"><i class="icon icon-cross js-delWidget"></i></div>
    </div>
    <div class="body">
        <div class="textField inputBox widgetTitle">
            <input type="hidden" name="w[<?=$data['position']; ?>][wVideo][id]" value="<?=$data['id']; ?>">
            <input placeholder="Заголовок блока" name="w[<?=$data['position']; ?>][wVideo][title]" type="text" value="<?=$data['title']; ?>" data-pattern="text">
        </div>
        <h2>Ссылка на видео в youtube</h2>
        <?php if($data['content']): ?>
        <?php $cnt = 0; $lastIndex = count($data['content']) - 1; ?>
            <?php foreach ($data['content'] as $keyRow => $row): ?>
                <div class="row">
                    <div class="col">
                        <p>https://www.youtube.com/embed/</p>
                    </div>
                    <div class="col">
                        <div class="textField inputBox">
                            <input class="initialized" placeholder="id видео" name="w[<?=$data['position']; ?>][wVideo][url][<?=$cnt; ?>]" type="text" data-pattern="text" value="<?=$row; ?>">
                        </div>
                    </div>
                    <div class="col">
                        <?php if($cnt == $lastIndex): ?>
                            <div class="js-plusRow">+</div>
                        <?php else: ?>
                            <i class="icon icon-cross js-delRowParams"></i>
                        <?php endif;?>
                    </div>
                </div>
            <?php $cnt++; endforeach;?>
        <?php else: ?>
            <div class="row">
                <div class="col">
                    <p>https://www.youtube.com/embed/</p>
                </div>
                <div class="col">
                    <div class="textField inputBox">
                        <input class="initialized" placeholder="id видео" name="w[<?=$data['position']; ?>][wVideo][url][]" type="text" data-pattern="text" value="">
                    </div>
                </div>
                <div class="col">
                    <div class="js-plusRow">+</div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>