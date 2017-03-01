<?php

$localvars = localvars::getInstance();
$form = formBuilder::createForm('createPermissions');

$form->linkToDatabase( array(
    'table' => 'reservePermissions'
));

recurseInsert("includes/formDefinitions/callbacks.php", "php");

if(!is_empty($_POST) || session::has('POST')) {
    $processor = formBuilder::createProcessor();
    $processor->setCallback('beforeInsert', 'processInsert');
    $processor->processPost();
}

// form titles
$form->insertTitle = "Add Permissions";
$form->editTitle   = "Edit Permissions";
$form->updateTitle = "Update Permissions";

// form information
$form->addField(array(
    'name'    => 'ID',
    'type'    => 'hidden',
    'value'   => $localvars->get("id"),
    'primary' => TRUE,
    'fieldClass' => 'id',
    'showIn'     => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
));

$form->addField(array(
    'name'     => 'resourceID',
    'label'    => 'Building:',
    'type'     => 'select',
    'value'    => $localvars->get("building"),
    'fieldClass' => 'resourceID',
    'blankOption' => 'Select a Building',
    'linkedTo' => array(
          'foreignTable' => 'building',
          'foreignField' => 'ID',
          'foreignLabel' => 'name',
        ),
    'required' => TRUE
));

$form->addField(array(
    'name'       => 'resourceType',
    'label'      => 'Type:',
    'type'       => 'select',
    'value'      => $localvars->get("type"),
    'fieldClass' => 'resourceType',
    'options'    => array("Building", "Policy", "Template", "Room"),
    'required'   => TRUE,
    'duplicates' => TRUE
));

$form->addField(array(
    'name'     => 'roomID',
    'label'    => 'Room:',
    'type'     => 'select',
    //'value'    => $id,
    'fieldClass' => 'rooms',
    'blankOption' => 'Select a Room',
    'linkedTo' => array(
          'foreignSQL' => "SELECT `ID`, CONCAT(`name`, ' - ', `number`) AS `name` FROM `rooms` ORDER BY `number`",
          'foreignTable' => 'rooms',
          'foreignField' => 'ID',
          'foreignLabel' => 'name',
        ),
    'required' => FALSE
));

$form->addField(array(
    'name'            => "email",
    'label'           => "Email",
    'showIn'          => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
    'required'        => FALSE,
    'type'            => 'email',
    'duplicates'      => TRUE
));

// buttons and submissions
$form->addField(array(
    'showIn'     => array(formBuilder::TYPE_UPDATE),
    'name'       => 'update',
    'type'       => 'submit',
    'fieldClass' => 'submit',
    'value'      => 'Update Permissions'
));
$form->addField(array(
    'showIn'     => array(formBuilder::TYPE_UPDATE),
    'name'       => 'delete',
    'type'       => 'delete',
    'fieldClass' => 'delete',
    'value'      => 'Delete Permissions'
));
$form->addField(array(
    'showIn'     => array(formBuilder::TYPE_INSERT),
    'name'       => 'insert',
    'type'       => 'submit',
    'fieldClass' => 'submit something',
    'value'      => 'Save Permissions'
));
?>
