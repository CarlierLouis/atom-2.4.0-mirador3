function renderMiradorViewerComponent(jsonlink, catalog) {
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
                manifestId: jsonlink,
            }
        ],
        catalog: catalog.map(function(manifestPathLink) {
            return { manifestId: manifestPathLink, provider: "" };
        })
    });
}