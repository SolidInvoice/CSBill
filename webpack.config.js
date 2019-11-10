const Encore = require('@symfony/webpack-encore'),
    path = require('path'),
    { execSync } = require('child_process'),
    fs = require('fs');

Encore
    .setOutputPath('web/static/')
    .setPublicPath('/static')

    .addEntry('core', './assets/js/core.js')

    .addStyleEntry('app', './assets/less/app.less')
    .addStyleEntry('email', './assets/less/email.less')
    .addStyleEntry('pdf', './assets/less/pdf.less')

    .enableSingleRuntimeChunk()
    .splitEntryChunks()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    .enableLessLoader()
    .autoProvidejQuery()

    .addAliases({
        '~': path.resolve(__dirname, 'assets/js'),
        'SolidInvoiceCore': path.resolve(__dirname, 'src/CoreBundle/Resources/public'),
        'SolidInvoiceDataGrid': path.resolve(__dirname, 'src/DataGridBundle/Resources/public'),
        'jos_js': path.resolve(__dirname, 'web/bundles/fosjsrouting/js'),
        'router': path.resolve(__dirname, 'src/CoreBundle/Resources/public/js/extend/routing'),
        'translator': path.resolve(__dirname, 'src/CoreBundle/Resources/public/js/extend/translator'),
    })
;

const pagesDir = path.resolve(__dirname, 'assets/js/pages');

try {
    const files = fs.readdirSync(pagesDir);

    files.forEach(function(file, index) {
        if ('.js' === path.extname(file)) {
            Encore.addEntry(file.substr(0, file.length - 3), path.join(pagesDir, file));
        }
    });
} catch (err) {
    console.error("Could not list the directory.", err);
    process.exit(1);
}

const output = (err, stdout, stderr) => {
    if (stdout) {
        process.stdout.write(stdout);
    }

    if (stderr) {
        process.stderr.write(stderr);
    }

    if (err) {
        process.stderr.write(err);
    }
};

execSync(path.resolve(__dirname, 'bin/console assets:install web'), output);
execSync(path.resolve(__dirname, 'bin/console fos:js-routing:dump --format=json --target=assets/js/js_routes.json'), output);
execSync(path.resolve(__dirname, 'bin/console bazinga:js-translation:dump assets/js --merge-domains --format=json'), output);

module.exports = Encore.getWebpackConfig();
