<?php decorate_with('layout_2col.php') ?>

<?php slot('sidebar') ?>

  <?php echo get_component('settings', 'menu') ?>

<?php end_slot() ?>

<?php slot('title') ?>

  <h1><?php echo __('IIIF Viewer') ?></h1>

<?php end_slot() ?>

<?php slot('content') ?>

  <?php echo $form->renderFormTag(url_for(array('module' => 'settings', 'action' => 'iiifViewer'))) ?>

    <div id="content">

      <fieldset class="collapsible">

        <legend><?php echo __('IIIF Viewer settings') ?></legend>

        <?php echo $form->mirador
          ->label(__('Mirador'))
          ->renderRow() ?>

      </fieldset>

      <fieldset class="collapsible">

        <legend><?php echo __('Mirador Catalog') ?></legend>

        <?php echo $form->miradorCatalog
          ->label(__('Mirador Catalog (elements in the viewer resources)'))
          ->renderRow() ?>

      </fieldset>

      <fieldset class="collapsible">

        <legend><?php echo __('Mirador view') ?></legend>

        <?php echo $form->miradorView
          ->label(__('Mirador default window view'))
          ->renderRow() ?>

      </fieldset>

    </div>

    <section class="actions">
      <ul>
        <li><input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Save') ?>"/></li>
      </ul>
    </section>

  </form>

<?php end_slot() ?>
