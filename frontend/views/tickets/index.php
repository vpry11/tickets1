<?php

/* @var $this yii\web\View */
/*
 * It's an example code for 2 methods of passing parameters to view (se TicketsController.php):
 *	1. Push method:	using push we're getting $tilist1 and $provider here
 *	2. Pull method:	using pull we're getting $here tilist2
 */
//use Yii;
use yii\helpers\Url;
use yii\helpers\Html;

//$this->title = 'Заявки';
//$this->title = 'Tickets';
$this->title = Yii::t('app','Tickets');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title).($tiall?'':' (открытые)')?></h1> 

    <?php /*echo Yii::$app->getBasePath()*/ ?>
    <?php /*print_r  ($tilist1); echo "<br>"*/?>
    <?php /*print_r  ($this->context->tilist2); echo "<br>"*/?>

    <?php  if(!$model->isUserFitter()) echo 
    	'<p>'.Html::a($tiall?Yii::t('app','Show opened'):Yii::t('app','Show all'), ['index','tiall'=>!$tiall], ['class' => 'btn btn-success']).'</p>' 
    ?>
    <div id='ticketsIndexGrid'>
    	<?php echo $this->render('_index.php', ['provider'=>$provider, 'model'=>$model,'tiall'=>$tiall]); ?>
    </div>
    
    <?php 
		$url4TicketsIndex = Url::toRoute(['index']); 
		$refreshscript = <<<JS
			function getTicketsGrid(){
    			//$("#ticketsIndexGrid").html('--');
    			$.ajax({
    	   			url: '$url4TicketsIndex',
    	   			type: 'GET',
           			data: {  },
           			success: function(data) {
              			$("#ticketsIndexGrid").html(data);
           			},
           			error:   function() {
              			$("#ticketsIndexGrid").html('--');
           			}
				});
				return true;
			};

				//$(document).ready(function() {
    			setInterval( getTicketsGrid, 15000 );
				//});
JS;
		$this->registerJs($refreshscript,yii\web\View::POS_READY);
	?>

    <?php/*<code><?= __FILE__ ?></code>*/?>
</div>
