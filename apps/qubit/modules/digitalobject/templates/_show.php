<div class="digital-object-reference">
  <?php if (!empty($accessWarning)): ?>
    <div class="access-warning">
      <?php echo $accessWarning ?>
    </div>
  <?php else: ?>
    <?php echo get_component('digitalobject', $showComponent, array('iconOnly' => $iconOnly, 'link' => $link, 'resource' => $resource, 'usageType' => $usageType)) ?>
  <?php endif; ?>
</div>


<?php //include '../atom-2.4.0-mirador3/mirador3/miradorViewerComponent.php'?>
<?php //include '../atom-2.4.0-mirador3/mirador3/MiradorUtils.php'?>
<?php //echo renderMiradorViewerComponent($resource->informationObject->getDigitalObjectLink(),  MiradorUtils::getAllChildrenFromRoot($resource->informationObject)) ?>
