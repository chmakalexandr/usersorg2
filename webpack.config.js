/* connect the plugin */
var Encore = require('@symfony/webpack-encore');

Encore
/* Установим путь куда будет осуществляться сборка */
    .setOutputPath('./web/build/')
    /* Укажем web путь до каталога web/build */
    .setPublicPath('/build')
    /* Каждый раз перед сборкой будем очищать каталог /build */
    .cleanupOutputBeforeBuild()
    /* --- Добавим основной JavaScript в сборку --- */
    .addEntry('scripts', './assets/app.js')

    .addEntry('form', './assets/form.js')

    /*.addEntry('file_size', './assets/js/check-file-size.js')*/

    /* Добавим наш главный файл ресурсов в сборку */
    .addStyleEntry('styles', './assets/app.scss')

    .addStyleEntry('form_style', './assets/dist/bootstrap/bootstrap-datepicker.css')

    /* Включим поддержку sass/scss файлов */
    .enableSassLoader()
    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()
    /* В режиме разработки будем генерировать карту ресурсов */
    .enableSourceMaps(!Encore.isProduction());

/* Экспортируем финальную конфигурацию */
module.exports = Encore.getWebpackConfig();