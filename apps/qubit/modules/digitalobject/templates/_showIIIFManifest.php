<div>
    <div id="miradorViewer-wrapper">
        <div id="mirador"></div>
    </div>
    <br>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            renderMiradorViewerComponent("<?php echo $resource->informationObject->getDigitalObjectLink(); ?>",
            <?php echo json_encode(QubitDigitalObject::getAllChildrenFromRoot($resource->informationObject)); ?>);
        });
    </script>
</div>
