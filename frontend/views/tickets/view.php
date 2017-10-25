<?php

use yii\helpers\Html;
//use yii\helpers\Url;
use yii\bootstrap\Tabs;

$qq=Yii::t('app','Accept job',[],'ru-Ru');// echo $qq;
$this->title = /*Yii::t('app','Ticket')*/'Заявка'.' №'.$model->ticket['ticode'];
$this->params['breadcrumbs'][] = ['label' => /*Yii::t('app','Tickets')*/'Заявки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="tickets-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= Tabs::widget([
        'items' => [
            [
            'label' => $this->title,
            'content' => $this->render('_viewtab', ['model' => $model]),
            'active' => true
            ],
            [
            'label' => Yii::t('app','Ticket history'),
            'content' => $this->context->renderpartial('_historytab', ['model' => $model]),
            ],
            [
            'label' => Yii::t('app','Ticket spair parts'),
            'content' => $this->context->renderpartial('_sparttab', ['model' => $model]),
            ],
            [
            'label' => Yii::t('app','Photo'),
            'content' => $this->context->renderpartial('_uploadtab', ['model' => $model,'imagemodel' => $imagemodel]),
            ],
        ],
    ])?>
</div>
