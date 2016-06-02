<?php

$form = new \yii\widgets\ActiveForm();

echo $form->field($model, 'section');

echo $form->field($model, 'key');

echo $form->field($model, 'required')->radioList(['No', 'Yes']);

echo $form->field($model, 'type')->dropDownList(\cszchen\setting\types\Type::typeNames());

echo \yii\helpers\Html::submitButton('Submit');
