<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\Report_Oos;

class ReportsController extends Controller
{
	public function actionIndex()	
    {
            return $this->render( 'index' );
    }
	public function actionOosnow()	
    {
    	$model = new Report_Oos();
    	$provider = $model->generate();
    	return $this->render( 'oosnow',['provider'=>$provider] );
    }
}