<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
//use yii\helpers\ArrayHelper;

/**
 *	Ticket history Partial view
 */

?>

<div class="tickets-_hystorytab">
	 <?php 
	 	Pjax::begin(['id'=>'tihistoryGrid']);
	 	echo GridView::widget([
			'dataProvider' => $model->tilogprovider,
			'columns' => [
				['attribute' => 'tiltime', 		'label'=>'Дата'],
				[
					'attribute' => 'tilstatus',		
					'label'=>'Операция',
					'content' => function($data){ return Yii::$app->params['TicketStatus'][ $data['tilstatus']];},
					'contentOptions'=> function($data){ return 
						(strpos($data['tilstatus'],'COMPLETE') ) ? ['style'=>'background-color:lightgreen']:
						(strpos($data['tilstatus'],'REFUSE')     ? ['style'=>'background-color:yellow']:
						(strpos($data['tilstatus'],'REASSIGN') ? ['style'=>'background-color:red; color:white']:[]));}
				],
				['attribute' => 'tiltext', 		'label'=>'Комментарий'],
				['attribute' => 'tilerrorcode',	'label'=>'Код ошибки'],
				['attribute' => 'sender', 	'label'=>'ФИО инициатора'],
				['attribute' => 'receiver', 	'label'=>'ФИО получателя'],
				['attribute' => 'receiverdesk',	'label'=>'Сервисное подразделение'],
			]
		]);
		Pjax::end();
	?>
</div>