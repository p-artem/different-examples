<?php
use yii\helpers\ArrayHelper;
?>
<?php if($flagView !== 'frontend'): ?>
<?php
$widgetsInfo = Yii::$app->widgetPackage->getInfo();
$widgetsInfo = ArrayHelper::getColumn($widgetsInfo, 'widget_id');
$widgetName = ['Текст', 'Блок параметров', 'Фото', 'Видео', 'График', 'Фотогалерея', 'Сравнивания фотографий', 'Файлы', 'Фоновое изображение', 'Таблица'];
?>
<h2 style="margin-top: 20px;">Контент</h2>
<div class="content sortable">
    <?=$content; ?>
    <div class="choiceWidget">
        <ul>
            <?php foreach ($widgetsInfo as $widgetId): ?>
                <li data-widget-id="<?=$widgetId; ?>"><?=$widgetName[$widgetId - 1]; ?></li>
            <?php endforeach; ?>
            <!--        <li data-widget-id="image">Фото</li>-->
            <!--        <li data-widget-id="text">Текст</li>-->
            <!--        <li data-widget-id="imageCompare">Сравнивания фотографий</li>-->
            <!--        <li data-widget-id="video">Видео</li>-->
            <!--        <li data-widget-id="graph">График</li>-->
            <!--        <li data-widget-id="gallery" >Фотогалерея</li>-->
            <!--        <li data-widget-id="files">Файлы</li>-->
            <!--        <li data-widget-id="params">Блок параметров</li>-->
<!--                    <li data-widget-id="table">Таблица</li>-->
        </ul>
    </div>
    <button class="js-addBlock addBlock" type="button">Добавить блок</button>
</div>
<?php else :?>
    <?=$content; ?>
<?php endif;?>
