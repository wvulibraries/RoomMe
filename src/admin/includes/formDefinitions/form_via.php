<?php

$form = formBuilder::createForm('Via');
$form->linkToDatabase(array(
    'table'       => 'via'
));

$form->insertTitle = "New Via";
$form->editTitle   = "Edit Vias";


$form->addField(
    array(
        'name'            => 'ID',
        'primary'         => TRUE,
        'showInEditStrip' => FALSE,
        'type'            => 'hidden',
        'required'        => TRUE
    )
);

$form->addField(
    array(
        'name'  => 'name',
        'label' => 'Via'
    )
);

?>
