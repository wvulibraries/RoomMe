<?php

$form = formBuilder::createForm('roomPolicies');
$form->linkToDatabase(array(
    'table' => "policies"
));

if(!is_empty($_POST) || session::has('POST')) {
    $processor = formBuilder::createProcessor();
    $processor->processPost();
}

// form titles
$form->insertTitle        = "New Policy Type";
$form->editTitle          = "Edit Policy Types";
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
        'label'           => "Policy Name",
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
        'required'        => TRUE,
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
        'type'            => 'url'
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
        'name'            => "hoursAllowed",
        'label'           => "Hours per Period",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => TRUE,
        'type'            => 'text',
        'duplicates'      => TRUE,
        'validate'        => 'integer'
    )
);

$form->addField(
    array(
        'name'            => "bookingsAllowedInPeriod",
        'label'           => "Bookings per Period",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => TRUE,
        'type'            => 'text',
        'validate'        => 'integer',
        'duplicates'      => TRUE
    )
);
$form->addField(
    array(
        'name'            => "maxLoanLength",
        'label'           => "Max Loan Length",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => TRUE,
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
        'required'        => TRUE,
        'type'            => 'text'
    )
);
$form->addField(
    array(
        'name'            => "publicScheduling",
        'label'           => "Public Scheduling",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => TRUE,
        'type'            => 'boolean',
        'duplicates'      => TRUE,
        'options'         => array("No","Yes")
    )
);

$form->addField(
    array(
        'name'            => "publicViewing",
        'label'           => "Public Viewing",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => TRUE,
        'type'            => 'boolean',
        'duplicates'      => TRUE,
        'options'         => array("No","Yes")
    )
);

$form->addField(
    array(
        'name'            => "roomsClosed",
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
        'name'            => "roomsClosedSnippet",
        'label'           => "Rooms Closed Snippet",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => FALSE,
        'type'            => 'text',
        'validate'        => 'integer',
        'duplicates'      => TRUE
    )
);

$form->addField(
    array(
        'name'            => "sameDayReservations",
        'label'           => "Create Same Day Reservation",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => TRUE,
        'type'            => 'boolean',
        'duplicates'      => TRUE,
        'options'         => array("No","Yes")
    )
);


$form->addField(
    array(
        'name'            => "reservationIncrements",
        'label'           => "Reservations Increments",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => TRUE,
        'type'            => 'text',
        'duplicates'      => TRUE,
        'value'           => getConfig('defaultReservationIncrements')
    )
);
$form->addField(
    array(
        'name'            => "futureScheduleLength",
        'label'           => "Future Schedule Length",
        'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
        'required'        => TRUE,
        'type'            => 'text',
        'duplicates'      => TRUE,
        'validate'        => "integer"
    )
);
?>
