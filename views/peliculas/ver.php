<?php
use yii\grid\GridView;

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Ver una película';
$this->params['breadcrumbs'][] = $this->title;
$inputOptions = [
    'inputOptions' => [
        'class' => 'form-control',
        'readonly' => true,
    ],
];
?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'persona.nombre',
        'pelicula.titulo:text:Título',
        'papel.papel'
    ],
]) ?>


<!-- <?php foreach ($pelicula->participaciones as $paticipacion): ?>
    <dl>
        <dt>Nombre</dt>
        <dd><?= $paticipacion->persona->nombre  ?></dd>
        <dt>Papel</dt>
        <dd><?= $paticipacion->papel->papel  ?></dd>
    </dl>
<?php endforeach; ?> -->

<?php $form = ActiveForm::begin(['enableClientValidation' => false]) ?>
    <?= $form->field($pelicula, 'titulo', $inputOptions) ?>
    <?= $form->field($pelicula, 'anyo', $inputOptions) ?>
    <?= $form->field($pelicula, 'duracion', $inputOptions) ?>
    <?= $form->field($pelicula, 'genero_id', $inputOptions) ?>
    <div class="form-group">
        <?= Html::a('Volver', ['peliculas/index'], ['class' => 'btn btn-danger']) ?>
    </div>
<?php ActiveForm::end() ?>
