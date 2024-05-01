function renderMiradorViewerComponent(jsonlink, catalog, settings) {
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
        language: settings["language"],
        windows: [
            {
                manifestId: jsonlink,
            }
        ],
        catalog: catalog.map(function(manifestPathLink) {
            return { manifestId: manifestPathLink, provider: "" };
        })
    });
}