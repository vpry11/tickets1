<?php

/* @var $this yii\web\View */

$this->title = 'ОЗК ОДС КСП "Харьковгорлифт"';

?>
<div class="site-index">

    <div class="jumbotron">
        <h1><?=Yii::t('app','System for Field Service Management for Elevators Repair')?></h1>

        <p class="lead">__________</p>

        <?php if (Yii::$app->user->isGuest) {?>
        <p><a class="btn btn-lg btn-success" href="index.php?r=site/login"><?=YII::t('app','Login')?></a></p>
        <?php } else{?>
        <p><a class="btn btn-lg btn-success" href="index.php?r=tickets/index"><?=YII::t('app','Tickets')?></a></p>
        <p><a class="btn btn-lg btn-success" href="index.php?r=reports/index"><?=YII::t('app','Reports')?></a></p>
        <?php }?>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>О комплексе</h2>

                <p>Оперативно-заявочный комплекс КСП "Харьковгорлифт" предназначен для автоматизации процессов регистрации, выполнения, мониторинга выполнения и закрытия заявок на выполнение работ по оперативному ремонту и техническому обслуживанию лифтов, а также получение отчетной информации</p>

                <p><a class="btn btn-default" href="#">Помощь &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Пользователи</h2>

                <p>Комплекс предназначен для использования диспетчерами ОДС КСП "Харьковгорлифт", старшими мастерами, мастерами, электротехническим персоналом линейных участков, сотрудниками ЛАС, сотрудниками специализированных участков, сотрудиками отдела ОМТС, руководящим персоналом предприятия</p>

                <p><a class="btn btn-default" href="#">Форум &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Смежные системы</h2>

                <p>Комплекс предназначен для обслуживания заявок на выполнение работ по оперативному ремонту и обслуживанию лифтов на основании: вызовов, поступивших в ОДС КСП "Харьковгорлифт" с панелей ГГС установленных в лифтах, обращений граждан, поступивших по телефону, а также заявок, поступивших из системы 1562  </p>

                <p><a class="btn btn-default" href="#">Смежные системы &raquo;</a></p>
            </div>
        </div>

    </div>
</div>
