<?php

$form = formBuilder::createForm('buildings');
$form->linkToDatabase(array(
    'table' => "building"
));

if(!is_empty($_POST) || session::has('POST')) {
    $processor = formBuilder::createProcessor();
    $processor->processPost();
}

// form titles
$form->insertTitle = "New Building";
$form->editTitle   = "Edit Buildings";
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
        'label'           => "Building Name",
        'showInEditStrip' => TRUE,
        'required'        => TRUE,
        'type'            => 'text'
    )
);

$form->addField(
    array(
        'name'            => "email",
        'label'           => "Building Email",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => FALSE,
        'type'            => 'email',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "phone",
        'label'           => "Building Phone",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => FALSE,
        'type'            => 'text',
        'validate'        => 'phoneNumber',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "fromEmail",
        'label'           => "From Email",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => TRUE,
        'type'            => 'email',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "roomListDisplay",
        'label'           => "Room List Display",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => TRUE,
        'type'            => 'text',
        'value'           => "{name} -- {number}",
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "roomSortOrder",
        'label'           => "Room Sort Order",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => TRUE,
        'type'            => 'select',
        'duplicates'      => TRUE,
        'options'         => array(
			'name'        => 'Room Name',
			'number'      => 'Room Number',
            'name,number'  => 'Room Name, Then Number',
            'number,name'  => 'Room Number, Then Name'
        	)
    )
);

$form->addField(
    array(
        'name'            => "period",
        'label'           => "Period",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => FALSE,
        'type'            => 'text',
        'validate'        => 'integer',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "maxHoursAllowed",
        'label'           => "Max Hours Per Period",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => FALSE,
        'type'            => 'text',
        'validate'        => 'integer',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "bookingsAllowedInPeriod",
        'label'           => "Max Bookings Per Period",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => FALSE,
        'type'            => 'text',
        'validate'        => 'integer',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "fineAmount",
        'label'           => "Fine Amount",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => FALSE,
        'type'            => 'text',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "url",
        'label'           => "Building URL",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => FALSE,
        'type'            => 'url',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "externalURL",
        'label'           => "External URL",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => FALSE,
        'type'            => 'url',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "hoursRSS",
        'label'           => "Hours RSS URL",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => FALSE,
        'type'            => 'url',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "hoursURL",
        'label'           => "Hours URL",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => FALSE,
        'type'            => 'url',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "imageURL",
        'label'           => "Image URL",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => FALSE,
        'type'            => 'url',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "policyURL",
        'label'           => "Policy URL",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => FALSE,
        'type'            => 'url',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "fineLookupURL",
        'label'           => "Fine Lookup URL",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => FALSE,
        'type'            => 'url',
        'duplicates'      => TRUE
    )
);

// $form->addField(
//     array(
//         'name'            => "restricted",
//         'label'           => "Restrict Building",
//         'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
//         'required'        => FALSE,
//         'type'            => 'boolean',
//         'duplicates'      => TRUE,
//         'disabled'        => TRUE
//     )
// );

?>
