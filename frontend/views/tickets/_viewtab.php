<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use yii\jui\DatePicker;
use frontend\models\Tickets;
/**
 *  Ticket view partial view
 */
//print_r(Tickets::isTicketBeenRead($model->ticket['id']));
?>
<div class="tickets-_viewtab">
    <?php 
        //$isTicketRead = Tickets::isTicketBeenRead($model->ticket['id'],$model->ticket['tiexecutant_id']);
        if(strpos($model->ticket['tistatus'],'COMPLETE') )$isTicketRead = TRUE;
        $tiAttributes = [
            [                   
            'label' => 'Адрес',
            'value' => $model->ticket['tiaddress']
            ],
            [                   
            'label' => 'Проблема',
            'value' => $model->ticket['tiproblemtypetext'].' ('.$model->ticket['tiproblemtext'].'), '.$model->ticket['tidescription']
            ],
            [                   
            'label' => 'Статус',
            'value' => $model->ticket['tistatustime'].' : '.Yii::$app->params['TicketStatus'][$model->ticket['tistatus']],
            //'contentOptions'=>  ('MASTER_COMPLETE'== $model->ticket['tistatus'] ) ? ['style'=>'background-color:lightgreen']:[],
            'contentOptions'=>  (strpos($model->ticket['tistatus'],'COMPLETE') ) ? ['style'=>'background-color:lightgreen']:
                                (strpos($model->ticket['tistatus'],'REFUSE')  ? ['style'=>'background-color:yellow']:
                                (strpos($model->ticket['tistatus'],'REASSIGN')?['style'=>'background-color:red;color:white']:[])),
            ],
            [                   
            'label' => 'Приоритет',
            //'value' => ($model->ticket['tipriority']=='NORMAL')?'Обычный':'Высокий',
            'value' =>Yii::$app->params['TicketPriority'][$model->ticket['tipriority']],
            'contentOptions'=> ( $model->ticket['tipriority'] < 'NORMAL') ? ['style'=>'color:red']:[]
            ],
            [                   
            'label' => 'Открыл заявку ',
            'value' => $model->ticket['tioriginator']
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
            'label' => 'Плановый срок',
            'value' => $model->isUserFitter() ? $model->ticket['tiiplannedtime']:$model->ticket['tiplannedtimenew'],
            'contentOptions'=> (( strtotime($model->ticket['tiplannedtimenew']) < time() )&&('MASTER_COMPLETE'!= $model->ticket['tistatus'])) ? ['style'=>'color:red']:[]
            ],
            ];
            if( !$model->isUserFitter() ) $tiAttributes = array_merge( $tiAttributes, [
                [                   
                'label' => 'Плановый срок исполнителю ',
                'value' => $model->ticket['tiiplannedtime'],
                'contentOptions'=> (( strtotime($model->ticket['tiiplannedtime']) < time() )&&('MASTER_COMPLETE'!= $model->ticket['tistatus'])) ? ['style'=>' color:red']:[]
                ],
                [                   
                'label' => 'Плановый срок поставки МТЦ',
                'value' => $model->ticket['tisplannedtime'],
                'contentOptions'=> (( strtotime($model->ticket['tisplannedtime']) < time() )&&('MASTER_COMPLETE'!= $model->ticket['tistatus'])) ? ['style'=>'color:red']:[]
                ],
                [                   
                'label' => 'Объект',
                'value' => $model->ticket['tiobject'].' № '.$model->ticket['tiobjectcode'].' (Дом № '.$model->ticket['tifacilitycode'].')',
                'contentOptions'=> ['style'=>' font-weight:bold']
                ],
                [                   
                'label' => 'Ответственное подразделение',
                'value' => $model->ticket['divisionname']
                ],
                [ 
                'label' => 'Исполнитель',
                'format'=>'html',
                'value' => isset($model->ticket['executant']) ?
                    ($model->ticket['executant'].($model->ticket['tiexecutantread'] ? ' <span class="glyphicon glyphicon-ok" style="color:green"></span> ':
                    (isset($model->ticket['executant'])?' <span class="glyphicon glyphicon-envelope" style="color:red"></span> ':'-'))):'-',
                //'value' => $model->ticket['executant'].($isTicketRead?'':' (не прочитано)'),
                'contentOptions'=> $model->ticket['tiexecutantread'] ? []:['style'=>' font-weight:bold']
                ],
                [                   
                'label' => 'Причина отказа',
                'value' => $model->ticket['tiresulterrorcode'].": ".$model->ticket['tiresulterrortext']
                ],
            ]);
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $tiAttributes,
    ]) ?>
    <?php /*print_r ($model->flist)*/ ?>

    
    <h4 align='middle' class="glyphicon     glyphicon glyphicon-pencil" style='color:RoyalBlue'></h4>
    <?php /*All parameters passing to beginForm will be in get, to be in the post, hidden fields need to be defined */?>
    <?= Html::beginForm(['appoint','ticketId'=>$model->ticket['id']],'post') ?>
    <?= Html::hiddenInput('ticketId'    ,$model->ticket['id'])?>
    <?= Html::hiddenInput('senderId'    ,$model->useroprights['id'])?>
    <?= Html::hiddenInput('senderdeskId',$model->useroprights['division_id'])?>
    
    <?= Html::label('Комментарий :') ?>
    <?= Html::input('text', 'tiltext','',['class'=>'form-control','size'=>50])?>
    
    <?php 
    //----User is MASTER:
    if($model->isUserMaster() ) { 
        echo Html::hiddenInput('actor','MASTER');
        
        echo 
        '<div class="row">'.
            '<div class="col-md-3">'.
                Html::label('Исполнитель :').
            '</div>'.
            '<div class="col-md-4">'.
                Html::label('Плановый срок исполнителю:').
            '</div>'.
            '<div class="col-md-3">'.
                Html::label('Плановый срок по заявке:').
            '</div>'.
        '</div>'.
        '<div class="row">'.
            '<div class="col-md-3">'.
                Html::dropDownList('receiverId', $model->ticket['tiexecutant_id'],  ArrayHelper::map($model->fitterslist,'id','name'),['class'=>'form-control']).
            '</div>'.
            '<div class="col-md-2">'.
                DatePicker::widget(['name'  => 'fitterplanneddate',
                                    'value'  => $model->ticket['tiiplannedtime'] ? $model->ticket['tiiplannedtime']:$model->ticket['tiplannedtimenew'],
                                    'dateFormat' => 'yyyy-MM-dd',
                                    'options'=>['class'=>'form-control']]).
            '</div>'.
            '<div class="col-md-2">';
                if( 'EXECUTANT_COMPLETE' == $model->ticket['tistatus'] ) { 
                
                echo 
                Html::submitButton(Yii::t('app','Accept job'),['class'=>'submit btn btn-success','formaction'=>Url::toRoute(['appoint','tistatus'=>'MASTER_COMPLETE']) ]).'<br>'.
                Html::submitButton(Yii::t('app','Reject job'),['class'=>'submit btn btn-danger','formaction'=>Url::toRoute(['appoint','tistatus'=>'MASTER_REASSIGN']) ]).'<br>';
                }  else {
                echo
                Html::submitButton(Yii::t('app','Appoint'), ['class'=>'submit btn btn-primary','formaction'=>Url::toRoute(['appoint','tistatus'=>'MASTER_ASSIGN']) ]);
                }
            echo '</div>'.
            '<div class="col-md-3">'.
                DatePicker::widget(['name'  => 'ticketplanneddate',
                                    'value'  => $model->ticket['tiplannedtimenew'],
                                    'dateFormat' => 'yyyy-MM-dd',
                                    'options'=>['class'=>'form-control']]).
            '</div>'.
            '<div class="col-md-2">'.
                Html::submitButton(Yii::t('app','Set'), ['class'=>'submit btn btn-primary','formaction'=>Url::toRoute(['appoint','tistatus'=>'MASTER_ASSIGN_DATE']) ]).
            '</div>'.
        '</div>';
        echo '<br>'.Html::submitButton(Yii::t('app','Refuse'),['class'=>'submit btn btn-danger','formaction'=>Url::toRoute(['appoint','tistatus'=>'MASTER_REFUSE']) ]);
        } 
        //----User is FITTER:
        else if( $model->isUserFitter() ) { 
            //print_r($model->elerrorcodelist);
            echo Html::hiddenInput('actor','FITTER').'<br>';
            echo Html::dropDownList('errorcode', 0,  ArrayHelper::map($model->elerrorcodelist,'errorcode','errortext'),['class'=>'form-control']);
            echo Html::submitButton(Yii::t('app','Done'), ['class' => 'submit btn btn-primary','formaction'=>Url::toRoute(['appoint','tistatus'=>'EXECUTANT_COMPLETE']) ]).' ';
            echo Html::submitButton(Yii::t('app','Refuse'),['class'=>'submit btn btn-danger','formaction'=>Url::toRoute(['appoint','tistatus'=>'EXECUTANT_REFUSE']) ]);
        }
    ?>
    <?= Html::endForm() ?>
    
  
</div>
