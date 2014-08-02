<?php

$form = formBuilder::createForm('equipement');
$form->linkToDatabase(array(
    'table' => "equipement"
));

$form->insertTitle = "New Equipement";
$form->editTitle   = "Edit Equipement";

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
        'label'           => "Equipement Name",
        'showInEditStrip' => TRUE,
        'required'        => TRUE,
        'type'            => 'text'
    )
);

$form->addField(
    array(
        'name'            => "description",
        'label'           => "Description",
        'showInEditStrip' => FALSE,
        'required'        => FALSE,
        'type'            => 'textarea',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "url",
        'label'           => "URL",
        'showInEditStrip' => FALSE,
        'required'        => FALSE,
        'type'            => 'url',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "type",
        'label'           => "Type",
        'showInEditStrip' => FALSE,
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