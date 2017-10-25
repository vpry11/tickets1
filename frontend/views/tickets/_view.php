<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Country */

$this->title = Yii::t('app','Ticket').' №'.$model->ticket['ticode'];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Tickets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tickets-view">

    <h1><?= Html::encode($this->title) ?></h1>

    
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [                   
            'label' => 'Статус',
            'value' => Yii::$app->params['TicketStatus'][$model->ticket['tistatus']]
            ],
            [                   
            'label' => 'Дата последней операции по заявке',
            'value' => $model->ticket['tistatustime']
            ],
            [                   
            'label' => 'Номер',
            'value' => $model->ticket['ticode']
            ],
            [                   
            'label' => 'Приоритет',
            //'value' => ($model->ticket['tipriority']=='NORMAL')?'Обычный':'Высокий',
            'value' =>Yii::$app->params['TicketPriority'][$model->ticket['tipriority']],
            'contentOptions'=> ( $model->ticket['tipriority'] < 'NORMAL') ? ['style'=>'color:red']:[]
            ],
            [                   
            'label' => 'Дата инцидента',
            'value' => $model->ticket['tiincidenttime']
            ],
            [                   
            'label' => 'Дата открытия заявки ',
            'value' => $model->ticket['tiopenedtime']
            ],
            [                   
            'label' => 'Плановый срок выполнения',
            'value' => $model->ticket['tiplannedtime'],
            'contentOptions'=> ( strtotime($model->ticket['tiplannedtime']) < time() ) ? ['style'=>'color:red']:[]
            ],
            [                   
            'label' => 'Объект',
            'value' => $model->ticket['tiobject']
            ],
            [                   
            'label' => 'Проблема',
            'value' => $model->ticket['tiproblemtypetext']
            ],
            [                   
            'label' => 'Описание',
            'value' => $model->ticket['tidescription']
            ],
            [                   
            'label' => 'Адрес',
            'value' => $model->ticket['tiaddress']
            ],
            [                   
            'label' => 'Ответственное подразделение',
            'value' => $model->ticket['divisionname']
            ],
            [                   
            'label' => 'Исполнитель',
            'value' => $model->ticket['executant']
            ]],
    ]) ?>
    <?php /*print_r ($model->flist)*/ ?>

    
    <h3 align='middle' class="glyphicon     glyphicon glyphicon-pencil" style='color:RoyalBlue'></h3>
    <?php /*All parameters passing to beginForm will be in get, to be in the post, hidden fields need to be defined */?>
    <?= Html::beginForm(['appoint','ticketId'=>$model->ticket['id']],'post') ?>
    <?= Html::hiddenInput('ticketId'    ,$model->ticket['id'])?>
    <?= Html::hiddenInput('senderId'    ,$model->useroprights['id'])?>
    <?= Html::hiddenInput('senderdeskId',$model->useroprights['division_id'])?>
    
    <?= Html::label('Комментарий :') ?>
    <?= Html::input('text', 'tiltext','',['class'=>'form-control','size'=>50])?>
    
    <?php 
        //----User is FOREMAN:
        if($model->isUserForeman() ) { 
            echo Html::hiddenInput('actor','FOREMAN');
            if( 'COMPLETED' == $model->ticket['tistatus'] ) { 
                echo '<br>';
                echo Html::submitButton(Yii::t('app','Accept job'),['class'=>'submit btn btn-success','formaction'=>Url::toRoute(['appoint','tistatus'=>'COMPLETED_TESTED']) ]).' ';
                echo Html::submitButton(Yii::t('app','Reject job'),['class'=>'submit btn btn-danger','formaction'=>Url::toRoute(['appoint','tistatus'=>'ACCEPTED_REASSIGNED','tilstatus'=>'WORKORDERRECL']) ]);
                echo '<br><br>';
            } 
            echo Html::label('Назначить исполнителя :');
            echo Html::dropDownList('receiverId', $model->ticket['tiexecutant_id'],  ArrayHelper::map($model->fitterslist,'id','name'),['class'=>'form-control']);
            echo '<br>';
            echo Html::submitButton(Yii::t('app','Appoint'), ['class'=>'submit btn btn-primary','formaction'=>Url::toRoute(['appoint','tistatus'=>'ACCEPTED_ASSIGNED']) ]);
        } 
        //----User is FITTER:
        else if( $model->isUserFitter() ) { 
            echo Html::hiddenInput('actor','FITTER').'<br>';
            echo Html::submitButton(Yii::t('app','Done'), ['class' => 'submit btn btn-primary','formaction'=>Url::toRoute(['appoint','tistatus'=>'COMPLETED']) ]);
        }
    ?>
    <?= Html::endForm() ?>
    
    

</div>
