<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = Yii::t('app','About');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>
    <br>
    <h3><?=Yii::t('app','System for Field Service Management for Elevators Repair')?></h3>

    <code><?php /*echo __FILE__ */?></code>
</div>
