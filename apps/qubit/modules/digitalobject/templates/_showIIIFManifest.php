<?php use_helper('Text') ?>

<?php if ($iconOnly): ?>

    <?php if (isset($link)): ?>
    <?php echo link_to(image_tag($representation->getFullPath(), array('alt' => __('Open original %1%', array('%1%' => sfConfig::get('app_ui_label_digitalobject'))))), $link) ?>
    <?php else: ?>
    <?php echo image_tag($representation->getFullPath(), array('alt' => __('Original %1% not accessible', array('%1%' => sfConfig::get('app_ui_label_digitalobject'))))) ?>
    <?php endif; ?>

<?php else: ?>

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
        <?php ?>

        <?php $miradorSettings = json_encode(array('language' => sfContext::getInstance()->user->getCulture())) ?>

        document.addEventListener("DOMContentLoaded", function () {
            renderMiradorViewerComponent("<?php echo $resource->informationObject->getDigitalObjectLink(); ?>",
            <?php echo json_encode($getCatalog); ?>, <?php echo $miradorSettings ?>);
        });
    </script>

<?php endif; ?>
