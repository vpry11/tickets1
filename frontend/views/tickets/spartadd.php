<?php

use yii\helpers\Html;
use yii\helpers\Url;


$this->title = Yii::t('app','Spare Parts');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Tickets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Ticket').' №'.$model->ticket['ticode'], 'url' => ['view','id'=>$model->ticket['id']]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="spart-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= Html::beginForm(['spartadd','id'=>$model->ticket['id']],'post') ?>
    <?php
    foreach ($model->spartlist as $id => $spart ){
        echo 
        '<div class="row">'.
            '<div class="col-md-1">'.
                Html::checkbox('spAdd'.$spart['id']/*,false,['class'=>'form-control']*/).
            '</div>'.
            '<div class="col-md-4">'.
                Html::label($spart['elspart']).
            '</div>'.
            '<div class="col-md-2">'.
                Html::input('text','spNum'.$spart['id'],'',['class'=>'form-control','size'=>5]).
            //echo $spart['id'].' '.$spart['elspart'].' '.$spart['elspunit'].'<br>';
            '</div>'.
            '<div class="col-md-2">'.
                Html::label($spart['elspunit']).
            '</div>'.
        '</div>';
    }
    ?>
    <?= Html::hiddenInput('senderId'    ,$model->useroprights['id'])?>
    <?= Html::hiddenInput('senderdeskId',$model->useroprights['division_id'])?>
    <?= Html::hiddenInput('tistatus','MASTER_ASSIGN')?>
    <?= Html::submitButton(Yii::t('app','Add'),['class'=>'submit btn btn-success','formaction'=>Url::toRoute(['spartadd','id'=>$model->ticket['id']]) ]) ?>
    <?= Html::endForm() ?>
</div>
