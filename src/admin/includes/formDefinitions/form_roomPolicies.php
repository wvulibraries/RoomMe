<?php

$form = formBuilder::createForm('roomPolicies');
$form->linkToDatabase(array(
    'table' => "policies"
));

$form->insertTitle = "New Policy Type";
$form->editTitle   = "Edit Policy Types";

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
        'showInEditStrip' => FALSE,
        'required'        => TRUE,
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
        'type'            => 'url'
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
        'name'            => "hoursAllowed",
        'label'           => "Hours per Period",
        'showInEditStrip' => FALSE,
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
        'showInEditStrip' => FALSE,
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
        'showInEditStrip' => FALSE,
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
        'showInEditStrip' => FALSE,
        'required'        => TRUE,
        'type'            => 'text'
    )
);
$form->addField(
    array(
        'name'            => "publicScheduling",
        'label'           => "Public Scheduling",
        'showInEditStrip' => FALSE,
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
        'showInEditStrip' => FALSE,
        'required'        => TRUE,
        'type'            => 'boolean',
        'duplicates'      => TRUE,
        'options'         => array("No","Yes")
    )
);

$form->addField(
    array(
        'name'            => "sameDayReservations",
        'label'           => "Create Same Day Reservation",
        'showInEditStrip' => FALSE,
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
        'showInEditStrip' => FALSE,
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
        'showInEditStrip' => FALSE,
        'required'        => TRUE,
        'type'            => 'text',
        'duplicates'      => TRUE,
        'validate'        => "integer"
    )
);
?>