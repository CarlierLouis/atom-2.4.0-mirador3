<?php use_helper('Text') ?>

<div id="miradorViewer-wrapper">
    <div id="<?php echo "mirador" ?>"></div>
</div>
<br>

<?php $miradorCatalogSettings = QubitSetting::getByName('mirador_catalog') ?>

<?php if ($miradorCatalogSettings == "fromsameparent"):  ?>
    <?php $getCatalog = QubitDigitalObject::getParentDirectChildren($resource->informationObject) ?>
<?php endif; ?>

<?php if ($miradorCatalogSettings == "allfromsameparent"):  ?>
    <?php $getCatalog = QubitDigitalObject::getAllChidrenFromParent($resource->informationObject) ?>
<?php endif; ?>

<?php if ($miradorCatalogSettings == "allfromroot"):  ?>
    <?php $getCatalog = QubitDigitalObject::getAllChildrenFromRoot($resource->informationObject) ?>
<?php endif; ?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        renderMiradorViewerComponent("<?php echo $resource->informationObject->getDigitalObjectLink(); ?>",
        <?php echo json_encode($getCatalog); ?>);
    });
</script>