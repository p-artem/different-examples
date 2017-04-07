<?php
//echo '<pre>';
//print_r($data['content']);die;
?>
<style>
    .addDelRow *{
        /*margin-top: 5px !important;*/
    }
</style>
<div class="widget wTable">
    <div class="head">
        <i class="icon icon-wParams"></i>
        <span class="title">Таблица</span>
        <div class="btns"><i class="icon icon-cross js-delWidget"></i></div>
    </div>
    <div class="body">
        <div class="textField inputBox widgetTitle">
            <input type="hidden" name="w[<?=$data['position']; ?>][wTable][id]" value="<?=$data['id']; ?>">
            <input placeholder="Заголовок блока" name="w[<?=$data['position']?>][wTable][title]" type="text" value="<?=$data['title']?>" data-pattern="text">
        </div>

        <?php if($data['content']): ?>
            <?php $cntGroup = 0; ?>
            <?php foreach ($data['content'] as $group):?>
                <div class="group" groupIndex="<?=$cntGroup; ?>">
                    <span class="addGroup">+ Добавить группу</span>
                    <div class="textField inputBox groupTitle">
                        <input placeholder="Название группы" name="w[<?=$data['position']?>][wTable][group][<?=$cntGroup; ?>][groupTitle]" type="text" value="<?=$group['groupTitle']; ?>" data-pattern="text">
                    </div>
                    <?php $cntBlock = 0; $countBlock = count($group['blocks']); ?>
                    <?php foreach ($group['blocks'] as $block):?>
                        <div class="block" blockIndex="<?=$cntBlock; ?>">
                            <div class="row">
                                <div class="col"><h2>Блок параметров таблицы</h2></div>
                                <div class="col"><h2>Занчение</h2></div>
                                <div class="col"></div>
                            </div>
                            <?php $cntRow = 0; $count = count($block['value']); ?>
                            <?php foreach ($block['value'] as $row): ?>
                                <div class="row" rowIndex="<?=$cntRow; ?>">
                                    <div class="col">
                                        <?php if($cntRow == 0): ?>
                                        <div class="textField inputBox paramInput">
                                            <input class="initialized" placeholder="Введите параметр" name="w[<?=$data['position']?>][wTable][group][<?=$cntGroup; ?>][blocks][<?=$cntBlock; ?>][param]" value="<?=$block['param']; ?>" type="text" data-pattern="text">
                                        </div>
                                            <?php if($cntBlock == $countBlock - 1): ?>
                                                <button class="addBlock" type="button">Добавить блок параметров</button>
                                            <?php else: ?>
                                                &nbsp;<button class="delBlock" type="button">Удалить блок параметров</button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            &nbsp;
                                        <?php endif; ?>
                                    </div>

                                    <div class="col" colIndex="0">
<!--                                        <div class="textField inputBox valueInput">-->
<!--                                            <input class="initialized" placeholder="Введите значение" name="w[--><?//=$data['position']?><!--][wTable][group][--><?//=$cntGroup; ?><!--][blocks][--><?//=$cntBlock; ?><!--][blockParams][--><?//=$cntRow; ?><!--][title]" value="--><?//=$row['title']; ?><!--" type="text" data-pattern="text">-->
<!--                                        </div>-->
                                        <div class="textField inputBox valueInput">
                                            <input class="initialized" placeholder="Описание значения" name="w[<?=$data['position']?>][wTable][group][<?=$cntGroup; ?>][blocks][<?=$cntBlock; ?>][value][<?=$cntRow; ?>]" value="<?=$row; ?>" type="text">
                                        </div>
                                    </div>
                                    <?php $tag= ($cntRow == $count - 1) ? '<div class="js-plusRow">+</div>' : ' <i class="icon icon-cross js-del-wTableValueInputRow"></i>'; ?>
                                    <div  class="col addDelRow">
                                        <?=$tag; ?>
                                    </div>
                                </div>
                                <?php $cntRow++; endforeach;?>
                        </div>
                    <?php $cntBlock++; endforeach;?>
                </div>
            <?php $cntGroup++; endforeach;?>
        <?php else: ?>
            <div class="group" groupIndex="0">
                <span class="addGroup">+ Добавить группу</span>
                <div class="textField inputBox groupTitle">
                    <input type="hidden" name="w[0][wTable][id]" value="0">
                    <input placeholder="Название группы" name="w[0][wTable][group][0][groupTitle]" type="text" data-pattern="text">
                </div>
                <div class="block" blockIndex="0">
                    <div class="row">
                        <div class="col"><h2>Блок параметров таблицы</h2></div>
                        <div class="col"><h2>Занчение</h2></div>
                        <div class="col"></div>
                    </div>
                    <div class="row" rowIndex="0">
                        <div class="col">
                            <div class="textField inputBox paramInput">
                                <input class="initialized" placeholder="Введите параметр" name="w[0][wTable][group][0][blocks][0][param]" type="text" data-pattern="text">
                            </div>
                            <button class="addBlock" type="button">Добавить блок параметров</button>
                        </div>
                        <div class="col" colIndex="0">
<!--                            <div class="textField inputBox valueInput">-->
<!--                                <input class="initialized" placeholder="Введите значение" name="w[0][wTable][group][0][blocks][0][blockParams][0][title]" type="text" pattern="text">-->
<!--                            </div>-->
                            <div class="textField inputBox valueInput">
                                <input class="initialized" placeholder="Описание значения" name="w[0][wTable][group][0][blocks][0][value][0]" type="text">
                            </div>
                        </div>
                        <div class="col addDelRow">
                            <div class="js-plusRow">+</div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>