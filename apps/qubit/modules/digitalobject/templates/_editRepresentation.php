<div>

  <div style="float: right;">

  <?php if(QubitSetting::getByName('iiifviewer_mirador') == "yes" && QubitDigitalObject::isIIIFManifest($resource->informationObject->getDigitalObjectLink())): ?>
    
    <div class="digital-object-reference" style="">
      <?php echo link_to(image_tag($representation->getFullPath(), array('alt' => __('Open original %1%', array('%1%' => sfConfig::get('app_ui_label_digitalobject'))))), $link, array('target' => '_blank')) ?>
   </div>

  <?php else: ?>
    <?php echo get_component('digitalobject', 'show', array(
      'iconOnly' => true,
      'link' => public_path($representation->getFullPath()),
      'resource' => $resource,
      'usageType' => QubitTerm::THUMBNAIL_ID)) ?>

  <?php endif;?>

  </div>

  <div>

    <?php echo render_show(__('Filename'), $representation->name) ?>

    <?php echo render_show(__('Filesize'), hr_filesize($representation->byteSize)) ?>

    <?php echo link_to(__('Delete'), array($representation, 'module' => 'digitalobject', 'action' => 'delete'), array('class' => 'delete')) ?>

  </div>

</div>
