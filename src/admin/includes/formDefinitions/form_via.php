<?php

formBuilder::ajaxHandler();

$form = formBuilder::createForm('via');
$form->linkToDatabase(array(
    'table' => 'via'
));

$form->addField(
    array(
        'name'            => 'id',
        'primary'         => TRUE,
        'showInEditStrip' => FALSE,
        'type'            => 'hidden'
    )
);

$form->addField(
    array(
        'name'  => 'name',
        'label' => 'Via'
    )
);

?>
