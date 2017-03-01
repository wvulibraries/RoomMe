<?php

$form = formBuilder::createForm('equipmentTypes');
$form->linkToDatabase(array(
    'table' => "roomTemplates"
));

if(!is_empty($_POST) || session::has('POST')) {
    $processor = formBuilder::createProcessor();
    $processor->processPost();
}

// form titles
$form->insertTitle = "New Room Template";
$form->editTitle   = "Edit Room Templates";

$form->addField(
    array(
        'name'            => "ID",
        'label'           => "Table ID",
        'primary'         => TRUE,
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'type'            => 'hidden'
    )
);

$form->addField(
    array(
        'name'            => "name",
        'label'           => "Equipment Type",
        'showInEditStrip' => TRUE,
        'required'        => TRUE,
        'type'            => 'text'
    )
);

?>
