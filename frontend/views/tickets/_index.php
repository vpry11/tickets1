<?php

/* @var $this yii\web\View */
/*
 * It's an example code for 2 methods of passing parameters to view (se TicketsController.php):
 *	1. Push method:	using push we're getting $tilist1 and $provider here
 *	2. Pull method:	using pull we're getting $here tilist2
 */
//use Yii;
use yii\grid\GridView;

?>
    <?php 
    	$tiColumns = [
					[
					'attribute' => 'tipriority',
					'content' => function($data){
						switch($data['tipriority']){
							case 'NORMAL': return '-';
							case 'EMERGENCY':return '<span class="glyphicon glyphicon-exclamation-sign" style="color:red"></span>';
							case 'CONTROL1':return '<span class="glyphicon glyphicon-exclamation-sign" style="color:red">1</span>';
							case 'CONTROL2':return '<span class="glyphicon glyphicon-exclamation-sign" style="color:red">2</span>';
						}
					},
					 //'value' => "ggg",//($model->ticket['tipriority']=='NORMAL')?'N':'H',
					 'label'=>'Пр.', 
					// 'contentOptions' => ['class'=> ($data['tipriority']=='NORMAL')?"glyphicon glyphicon-eye-open":""]
					 ],
					['attribute' => 'ticode', 		'label'=>'Номер'],
					[
						'attribute' => $model->isUserFitter()?'tiiplannedtime':'tiplannedtimenew',
						'label'=>'Срок устранения',
						'contentOptions'=> function($data){ return ( !strpos($data['tistatus'],'COMPLETE') && strtotime($data['tiplannedtimenew']) < time() ) ? ['style'=>'color:red']:[];},
					],
					//['attribute' => 'tistatustime','label'=>'Дата статуса'],
					['attribute' => 'tiaddress',	'label'=>'Адрес'],
			];
			if( !$model->isUserFitter() ) $tiColumns = array_merge( $tiColumns, [
					[
						'attribute' => 'tistatus',		
						'label'=>'Статус',
						'content' => function($data){ return Yii::$app->params['TicketStatus'][ $data['tistatus' ]].': '.$data['tistatustime'];},
						'contentOptions'=> function($data){return 
							('MASTER_COMPLETE'== $data['tistatus'] ) ? ['style'=>'background-color:lightgreen']:
							(strpos($data['tistatus'],'REFUSE' ) ? ['style'=>'background-color:yellow']:
							(('DISPATCHER_COMPLETE'  == $data['tistatus'] ) ? ['style'=>'background-color:lightgreen']:
							(strpos($data['tistatus'],'REASSIGN' ) ? ['style'=>'background-color:red;color:white']:[])));}
						//'value'=>//$statustxt[$data['tistatus']]
					],
					[
						'attribute' => 'executant',	
						'label'=>'Исполнитель',
						//'content'=>function($data){return $data['executant'];} 
					//'content'=>function($data){return $data['executant'].($data['tiexecutantread']=='1'?' <span class="glyphicon glyphicon-ok" style="color:green"></span> ':
                    //(isset($data['tiexecutant_id'])?' <span class="glyphicon glyphicon-envelope" style="color:red"></span> ':'-'));}
                    'content'=>function($data){return (!isset( $data['tiexecutant_id'] ))?'-':
                    	($data['executant'].($data['tiexecutantread']=='1'?' <span class="glyphicon glyphicon-ok" style="color:green"></span> ':' <span class="glyphicon glyphicon-envelope" style="color:red"></span> '));}
                    ],
						
			]);
			$tiColumns = array_merge( $tiColumns, [['class' => 'yii\grid\ActionColumn', 'template' => '{view}']]);
		
    	echo GridView::widget([
			'dataProvider' => $provider,
			'columns' => $tiColumns
		]);
	?>

    <?php/*<code><?= __FILE__ ?></code>*/?>
