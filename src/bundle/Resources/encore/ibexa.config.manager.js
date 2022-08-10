const path = require('path');

module.exports = (ibexaConfig, ibexaConfigManager) => {
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-content-edit-parts-js',
        newItems: [path.resolve(__dirname, '../public/js/fieldType/locationrelationlist.js')],
    });

    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-content-edit-parts-css',
        newItems: [path.resolve(__dirname, '../public/scss/fieldType/locationrelationlist.scss')],
    });

};