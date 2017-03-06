<?php


$localvars = localvars::getInstance();

$form = formBuilder::createForm('roomTemplates');
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
$form->submitFieldCSSEdit = "display: none;";

$form->addField(
    array(
        'name'            => "ID",
        'primary'         => TRUE,
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
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => FALSE,
        'type'            => 'textarea',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "fromEmail",
        'label'           => "From Email",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => FALSE,
        'type'            => 'email',
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
        'name'            => "mapURL",
        'label'           => "Map URL",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
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
		'name'  => "edit",
		'value' => sprintf('<a href="%s/admin/roommanagement/templates/equipment/?roomTemplate={ID}">Edit</a>',$localvars->get("roomReservationHome")),
		'label' => "Manage Equipment",
		'type'  => "plaintext",
		'showIn' => array(formBuilder::TYPE_EDIT)
		)
);

?>
