  <?php
    require_once("../../../../engineHeader.php");

    $id = isset($_GET['MYSQL']['id']) ? $_GET['MYSQL']['id'] : null;
    $building = isset($_GET['MYSQL']['building']) ? $_GET['MYSQL']['building'] : null;
    $type = isset($_GET['MYSQL']['type']) ? $_GET['MYSQL']['type'] : 3;
    $action = $id !== null ? "Update" : "Insert";

    $localvars->set('action', $action);
    $localvars->set('building', $building);
    $localvars->set('type', $type);
    $localvars->set('id', $id);

    recurseInsert("includes/formDefinitions/form_permissions.php","php");

    templates::display('header');
  ?>

  <header>
    <h1>{local var="action"} a Restriction</h1>
  </header>

  <section>
  {local var="feedbackStatus"}
  {form name="createPermissions" display="form" addGet="true"}
  </section>

  <script type="text/javascript" src="{local var="roomResBaseDir"}/javascript/rooms.js"></script>

  <?php
    templates::display('footer');
  ?>
