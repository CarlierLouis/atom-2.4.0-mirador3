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
        language: settings.language,
        windows: [
            {
                view: settings.view,
                manifestId: jsonlink,
                allowClose: false
            }
        ],
        catalog: catalog.map(function(manifestPathLink) {
            return { manifestId: manifestPathLink, provider: "" };
        })
    });
}