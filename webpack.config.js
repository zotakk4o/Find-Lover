var Encore = require('@symfony/webpack-encore');
var jsFilesPath = "./web/bundles/findlover/js/";
var controllersPath = jsFilesPath + "Controllers/";

Encore
    // the project directory where all compiled assets will be stored
    .setOutputPath('web/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    .addEntry('home', jsFilesPath + 'find-lover-main.js')
    .addEntry("loverController", controllersPath + 'LoverController.js')

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // show OS notifications when builds finish/fail
    .enableBuildNotifications()

// create hashed filenames (e.g. app.abc123.css)
// .enableVersioning()
;

// export the final configuration
module.exports = Encore.getWebpackConfig();