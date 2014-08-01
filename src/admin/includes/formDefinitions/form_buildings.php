<?php

$form = formBuilder::createForm('buildings');
$form->linkToDatabase(array(
    'table' => "building"
));

$form->insertTitle = "New Building";
$form->editTitle   = "Edit Buildings";

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
        'showInEditStrip' => FALSE,
        'required'        => FALSE,
        'type'            => 'email',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "phone",
        'label'           => "Building Phone",
        'showInEditStrip' => FALSE,
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
        'showInEditStrip' => FALSE,
        'required'        => TRUE,
        'type'            => 'email',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "roomListDisplay",
        'label'           => "Room List Display",
        'showInEditStrip' => FALSE,
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
        'showInEditStrip' => FALSE,
        'required'        => TRUE,
        'type'            => 'select',
        'duplicates'      => TRUE,
        'options'         => array(
			'name'   => 'Room Name',
			'number' => 'Room Number'
        	)
    )
);

$form->addField(
    array(
        'name'            => "period",
        'label'           => "Period",
        'showInEditStrip' => FALSE,
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
        'showInEditStrip' => FALSE,
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
        'showInEditStrip' => FALSE,
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
        'showInEditStrip' => FALSE,
        'required'        => FALSE,
        'type'            => 'text',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "url",
        'label'           => "Building URL",
        'showInEditStrip' => FALSE,
        'required'        => FALSE,
        'type'            => 'url',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "externalURL",
        'label'           => "External URL",
        'showInEditStrip' => FALSE,
        'required'        => FALSE,
        'type'            => 'url',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "hoursRSS",
        'label'           => "Hours RSS URL",
        'showInEditStrip' => FALSE,
        'required'        => FALSE,
        'type'            => 'url',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "hoursURL",
        'label'           => "Hours URL",
        'showInEditStrip' => FALSE,
        'required'        => FALSE,
        'type'            => 'url',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "imageURL",
        'label'           => "Image URL",
        'showInEditStrip' => FALSE,
        'required'        => FALSE,
        'type'            => 'url',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "policyURL",
        'label'           => "Policy URL",
        'showInEditStrip' => FALSE,
        'required'        => FALSE,
        'type'            => 'url',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "fineLookupURL",
        'label'           => "Fine Lookup URL",
        'showInEditStrip' => FALSE,
        'required'        => FALSE,
        'type'            => 'url',
        'duplicates'      => TRUE
    )
);

?>