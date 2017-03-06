<?php

$form = formBuilder::createForm('rooms');
$form->linkToDatabase(array(
    'table' => "rooms"
));

if(!is_empty($_POST) || session::has('POST')) {
    $processor = formBuilder::createProcessor();
    $processor->processPost();
}

// form titles
$form->insertTitle = "New Room";
$form->editTitle   = "Edit Rooms";
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
        'label'           => "Room Name",
        'showInEditStrip' => TRUE,
        'required'        => TRUE,
        'duplicates'      => TRUE,
        'type'            => 'text'
    )
);

$form->addField(
    array(
        'name'            => "number",
        'label'           => "Room Number",
        'showInEditStrip' => TRUE,
        'required'        => TRUE,
        'duplicates'      => TRUE,
        'type'            => 'text'
    )
);

$form->addField(
    array(
        'name'            => "building",
        'label'           => "Building",
        'showInEditStrip' => TRUE,
        'required'        => TRUE,
        'type'            => 'select',
        'duplicates'      => TRUE,
        'blankOption'     => "-- Select Building --",
        'linkedTo'         => array(
        	'foreignTable' => 'building',
        	'foreignKey'   => 'ID',
        	'foreignLabel' => 'name'
            )
    )
);

$form->addField(
    array(
        'name'            => "pictureURL",
        'label'           => "Picture URL",
        'showInEditStrip' => TRUE,
        'required'        => FALSE,
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "roomTemplate",
        'label'           => "Room Template",
        'showInEditStrip' => TRUE,
        'required'        => TRUE,
        'type'            => 'select',
        'duplicates'      => TRUE,
        'blankOption'     => "-- Select Room Template --",
        'linkedTo'         => array(
        	'foreignTable' => 'roomTemplates',
        	'foreignKey'   => 'ID',
        	'foreignLabel' => 'name'
            )
    )
);

$form->addField(
    array(
        'name'            => "roomClosed",
        'label'           => "Rooms Closed",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => TRUE,
        'type'            => 'boolean',
        'duplicates'      => TRUE,
        'options'         => array("No","Yes")
    )
);

$form->addField(
    array(
        'name'            => "capacity",
        'label'           => "Room Capacity",
        'showInEditStrip' => TRUE,
        'required'        => FALSE,
        'duplicates'      => TRUE,
        'type'            => 'text'
    )
);

?>
