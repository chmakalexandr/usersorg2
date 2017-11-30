/* connect the plugin */
var Encore = require('@symfony/webpack-encore');

Encore
    /* path build */
    .setOutputPath('./web/build/')
    /* path web/build */
    .setPublicPath('/build')
    /* Clear /build */
    .cleanupOutputBeforeBuild()
    /* Main script */
    .addEntry('scripts', './assets/app.js')
    /*Js for Form add user*/
    .addEntry('form', './assets/js/form.js')
    /*JS for check uploaded XML-file*/
    .addEntry('check', './assets/js/check-file-size.js')
    /* main style css */
    .addStyleEntry('styles', './assets/app.scss')
    /*bootstrap calendar css*/
    .addStyleEntry('form_style', './assets/dist/bootstrap/bootstrap-datepicker/css/bootstrap-datepicker.css')
    /*additional css for home-page*/
    .addStyleEntry('home_page', './assets/css/main_page.css')

    .enableSassLoader()
    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    .enableSourceMaps(!Encore.isProduction());

module.exports = Encore.getWebpackConfig();