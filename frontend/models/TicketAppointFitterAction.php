<?php
namespace frontend\models;
use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;


/*
 * This is example code for how to get data from db:
 *	1. With createComman: see dgetTicketsList()
 *	2. With SqlDataProvider: see search()
 */
class TicketAppointFitterAction extends Model
{
	public $id;
	public $fitterId;
	public function save()
	{
		Yii::warning('I am in TicketAppointFitterAction::save.',__METHOD__);
	}
}
