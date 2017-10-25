
	<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\jui\DatePicker;
use yii\helpers\ArrayHelper;

use yii\widgets\Pjax;


?>

	<?php 
	Pjax::begin(['id' => 'model-grid', 'enablePushState' => false]);

		    echo GridView::widget([
		'dataProvider' => $model->tispartprovider,
		'columns' => [
			['attribute' => 'tiltime', 		'label'=>'Дата'],
			[
				'attribute' => 'tilstatus',		
				'label'=>'Операция',
				'content' => function($data){ return Yii::$app->params['TicketStatus'][ $data['tilstatus']];},
				'contentOptions'=> function($data){return ('MASTER_COMPLETE'== $data['tilstatus'] ) ? ['style'=>'background-color:lightgreen']:[];}
			],
			['attribute' => 'tilspname',	'label'=>'Наименование'],
			['attribute' => 'tilspquantity',	'label'=>'Кол.'],
			['attribute' => 'tilspunit',	'label'=>'ед.'],
			['attribute' => 'tiltext', 		'label'=>'Комментарий'],
			['attribute' => 'sender', 	'label'=>'ФИО инициатора'],
			['attribute' => 'receiver', 	'label'=>'ФИО получателя'],
			['attribute' => 'senderdesk', 	'label'=>'Сервисное подразделение'],
			['class' => 'yii\grid\ActionColumn', 
				'template' => '{delete}',
				'urlCreator'=>function( $action, $model, $key, $index,  $this) use ($model) {return Url::toRoute(['spartdelete','id'=>$model->ticket['id'],'spartid'=>$key]); },
				'buttonOptions' => ['data-pjax'=>"#model-grid"],
			]
					
		]
	    ]);

    Pjax::end();
	?>


