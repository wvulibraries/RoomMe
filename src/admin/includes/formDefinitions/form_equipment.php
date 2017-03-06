<?php

$form = formBuilder::createForm('equipement');
$form->linkToDatabase(array(
    'table' => "equipement"
));

if(!is_empty($_POST) || session::has('POST')) {
    $processor = formBuilder::createProcessor();
    $processor->processPost();
}

// form titles
$form->insertTitle = "New Equipment";
$form->editTitle   = "Edit Equipment";
$form->submitFieldCSSEdit = "display: none;";

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
        'label'           => "Equipment Name",
        'showInEditStrip' => TRUE,
        'required'        => TRUE,
        'type'            => 'text'
    )
);

$form->addField(
    array(
        'name'            => "description",
        'label'           => "Description",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => FALSE,
        'type'            => 'textarea',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "url",
        'label'           => "URL",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => FALSE,
        'type'            => 'url',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "type",
        'label'           => "Type",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => TRUE,
        'type'            => 'select',
        'duplicates'      => TRUE,
        'blankOption'     => "-- Select Type --",
        'linkedTo'         => array(
        	'foreignTable' => 'equipementTypes',
        	'foreignKey'   => 'ID',
        	'foreignLabel' => 'name'
            )
    )
);

?>
