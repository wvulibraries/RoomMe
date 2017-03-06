<?php

$form = formBuilder::createForm('Via');
$form->linkToDatabase(array(
    'table'       => 'via'
));

if(!is_empty($_POST) || session::has('POST')) {
    $processor = formBuilder::createProcessor();
    $processor->processPost();
}

// form titles
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
