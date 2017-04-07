<div class="widget wParams">
    <div class="head">
        <i class="icon icon-wParams"></i>
        <span class="title">Блок параметров</span>
        <div class="btns"><i class="icon icon-cross js-delWidget"></i></div>
    </div>
    <div class="body">
        <div class="textField inputBox widgetTitle">
            <input type="hidden" name="w[<?=$data['position']; ?>][wParams][id]" value="<?=$data['id']; ?>">
            <input placeholder="Заголовок блока" name="w[<?=$data['position']?>][wParams][title]" type="text" value="<?=$data['title']?>" data-pattern="text">
        </div>
        <?php if($data['content']): ?>
            <?php $cnt = 0; $lastIndex = count($data['content']) - 1; ?>
            <div class="row">
                <div class="col"><h2>Параметр</h2></div>
                <div class="col"><h2>Занчение</h2></div>
                <div class="col"></div>
            </div>
            <?php foreach ($data['content'] as $keyRow => $row): ?>
                <div class="row">
                    <div class="col">
                        <div class="textField inputBox">
                            <input class="initialized" placeholder="Введите параметр" name="w[<?=$data['position']?>][wParams][params][<?=$cnt; ?>][param]" type="text"  value="<?=$row['param']?>">
                        </div>
                    </div>
                    <div class="col">
                        <div class="textField inputBox">
                            <input class="initialized" placeholder="Введите параметр" name="w[<?=$data['position']?>][wParams][params][<?=$cnt; ?>][value]" type="text"  value="<?=$row['value']?>">
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
                <?php  $cnt++; ?>
            <?php endforeach;?>
        <?php else: ?>
            <div class="row">
                <div class="col"><h2>Параметр</h2></div>
                <div class="col"><h2>Занчение</h2></div>
                <div class="col"></div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="textField inputBox">
                        <input class="initialized" placeholder="Введите параметр" name="w[][wParams][params][0][param]" type="text" data-pattern="text">
                    </div>
                </div>
                <div class="col">
                    <div class="textField inputBox">
                        <input class="initialized" placeholder="Введите значение" name="w[][wParams][params][0][value]" type="text" data-pattern="text">
                    </div>
                </div>
                <div class="col">
                    <div class="js-plusRow">+</div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>