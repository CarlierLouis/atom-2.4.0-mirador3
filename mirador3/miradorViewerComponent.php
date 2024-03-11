<?php function renderMiradorViewerComponent($jsonlink, $catalog) { ?> 
    <div id="miradorViewer-wrapper">
        <div id="mirador"></div>
    </div>
    <br>

    <script src="../node_modules/mirador/dist/mirador.min.js"></script>
    
    <style>
        <?php include '../atom-2.4.0-mirador3/mirador3/mirador.css'; ?>
    </style>
    
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        var miradorInstance = Mirador.viewer({
        id: 'mirador',
        themes: {
            light: {
            palette: {
                type: 'light',
                primary: {
                main: '#F66604',
                },
            },
            },
        },
        language: 'fr',
        windows: [
        {
            manifestId: "<?php echo $jsonlink ?>",
        }],
        catalog: [
            <?php foreach ($catalog as $manifestPathLink): ?>
                { manifestId: "<?php echo $manifestPathLink ?>", provider: ""},
            <?php endforeach; ?>
        ]
        });
    });
    </script>
<?php } ?>

