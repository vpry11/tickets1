<?php
use yii\helpers\Html;
use yii\grid\GridView;


$this->title = 'Отчет по неработающим лифтам';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="report-oos">
	<h1><?= Html::encode($this->title) ?></h1>
	 <?=  GridView::widget([
			'dataProvider' => $provider,
			'columns' => [
            	['class' => 'yii\grid\SerialColumn'],
            	[
                'label' =>"Время инцидента",
                'attribute' => 'tiincidenttime',
            	],
            	[
                'label' =>"Часов простоя",
                'attribute' => 'ooshours',
            	],
            	[
                'label' =>"Номер лифта",
                'attribute' => 'tiobjectcode',
            	],
            	[
                'label' =>"Адрес",
                'attribute' => 'tiaddress',
            	],
            	[
                'label' =>"Номер заявки",
                'attribute' => 'ticode',
            	],
            	[
                'label' =>"Сервисное подразделение",
                'attribute' => 'divisionname',
            	],
            ]
		]);
	?>

</div>