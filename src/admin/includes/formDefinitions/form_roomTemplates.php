<?php

$form = formBuilder::createForm('roomTemplates');
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
        'label'           => "Template Name",
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
        'name'            => "fromEmail",
        'label'           => "From Email",
        'showInEditStrip' => FALSE,
        'required'        => FALSE,
        'type'            => 'email',
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
        'name'            => "mapURL",
        'label'           => "Map URL",
        'showInEditStrip' => FALSE,
        'required'        => FALSE,
        'type'            => 'url',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "policy",
        'label'           => "Policy",
        'showInEditStrip' => TRUE,
        'required'        => TRUE,
        'type'            => 'select',
        'duplicates'      => TRUE,
        'blankOption'     => "-- Select Policy --",
        'linkedTo'         => array(
        	'foreignTable' => 'policies',
        	'foreignKey'   => 'ID',       
        	'foreignLabel' => 'name'
            )
    )
);

$form->addField (
	array(
		'name'  => "Manage Equipment Link",
		'value' => '<a href="addEQtoRoom.php?roomTemplate={ID}">Edit</a>',
		'label' => "Manage Equipment",
		'type'  => "plaintext",
		'showIn' => array(formBuilder::TYPE_EDIT)
		)
);

?>