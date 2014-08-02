<?php

$form = formBuilder::createForm('equipmentTypes');
$form->linkToDatabase(array(
    'table' => "roomTemplates"
));

$form->insertTitle = "New Room Template";
$form->editTitle   = "Edit Room Templates";

$form->addField(
    array(
        'name'            => "ID",
        'label'           => "Table ID",
        'primary'         => TRUE,
        'showInEditStrip' => FALSE,
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