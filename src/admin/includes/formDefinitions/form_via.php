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
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
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
