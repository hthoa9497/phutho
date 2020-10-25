let mix = require('laravel-mix');
let fs = require('fs');

let getFiles = function (dir) {
  // get all 'files' in this directory
  // filter directories
  return fs.readdirSync(dir).filter(file => {
    return fs.statSync(`${dir}/${file}`).isFile();
  });
};

mix.sass('app/scss/style.scss', 'css/');

mix.js('node_modules/bootstrap/dist/js/bootstrap.bundle.js', 'js/')
   .js('node_modules/bootstrap/dist/js/bootstrap.js', 'js/');

getFiles('app/js/components').forEach(function (filepath) {
  mix.js('app/js/components/' + filepath, 'js/components/');
});

mix.options({
  processCssUrls: false
});
