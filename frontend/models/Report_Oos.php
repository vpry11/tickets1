<?php
namespace frontend\models;
use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use frontend\models\Tickets;


class Report_Oos extends Model
{
	public function generate()
	{
		$sqltext="SELECT tiincidenttime,ticode,tiaddress,tiobjectcode,divisionname, TIMESTAMPDIFF(HOUR,tiincidenttime,now()) as ooshours from ticket left join division on division.id=tidivision_id where tiproblemtype_id=3 and tistatus not like '%COMPLETE' and TIMESTAMPDIFF(HOUR,tiincidenttime,now()) > 24";
		$oprights = Tickets::getUserOpRights();

		//---Prepare the sql statement for tickets according to the user rights
		if(FALSE !== $oprights )$sqltext = $sqltext.' and tidivision_id = '.$oprights[division_id];
		
		$provider = new SqlDataProvider([
			'sql' => $sqltext,
			'key' => 'id',
			'sort' => [
				'attributes' => [
					'tiincidenttime',
					'ooshours',
				],
				'defaultOrder' => [ 'tiincidenttime' => SORT_ASC ],
			],
		]);
		return $provider;
	}
}
	