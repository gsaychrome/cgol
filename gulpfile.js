var gulp = require("gulp");
var htmlReplace = require("gulp-html-replace");
var jeditor = require("gulp-json-editor");
var fs = require("fs");
var debug = require("gulp-debug");
var ignore = require("gulp-ignore");
var clean = require('gulp-clean');
//var exec = require("gulp-shell");
//var exists = require("files-exist");

var PUBLIC = "public";
var ASSETS = PUBLIC+"/assets";
var VENDOR = PUBLIC+"/vendor";
var WORKBENCH = PUBLIC+"/workbench";
var CONFIG = "config";
var SRC = "src";
var RELEASE = "release";

function checkDirectory(directory, callback) {
    fs.stat(directory, function(err, stats) {
        if (err && err.code === "ENOENT") {
            fs.mkdir(directory, callback);
        } else {
            callback(err)
        }
    });
}

/**
 * A workbench mappa alá letölti a bower.json alapján a szükséges saját repókat
 */
gulp.task("init", function() {

    var git = require("gulp-git");
    var typings = require("gulp-typings");

    checkDirectory(WORKBENCH, function(error) {

        if (error) {
            throw error;
        }

        fs.readFile("workbench.json", function(error, data) {
            if (error) {
                throw error;
            }

            var bower = JSON.parse(data);

            Object.keys(bower.dependencies).forEach(function(name) {

                var dest = WORKBENCH+"/"+name;

                // levágjuk róla a tag jelzést
                var remote = bower.dependencies[name].split("#")[0];

                fs.stat(dest, function(error, stats) {

                    if (error) {
                        // ekkor nem létezik
                        if (error.code === "ENOENT") {
                            git.clone(remote, {args: "-b develop "+dest}, function(error) {
                                if (error) {
                                    throw error;
                                }
                                gulp.src(WORKBENCH+"/"+name+"/typings.json")
                                    .pipe(typings());
                            });
                        }
                        else {
                            throw error;
                        }
                    } else {
                        var cwd = process.cwd();
                        process.chdir(dest);
                        git.pull('origin', 'develop');
                        process.chdir(cwd);
                    }
                });

                // TODO : bower install repositories without save for development
                /*fs.stat(dest + "/bower.json", function (data, err) {
                    if (err == null)
                    {
                        console.log("In '" + process.cwd() + "'executing command '" + "bower install " + bower.dependencies[name] + "'");
                        exec.task(["bower install " + bower.dependencies[name]]);
                    }
                });*/
            });
        });
    });
});

gulp.task("clean-release", function() {
    return gulp.src(RELEASE, {read: false})
        .pipe(clean());
});

gulp.task("copy", ["clean-release"], function() {
    return gulp.src(["composer.json", ".bowerrc"])
        .pipe(gulp.dest(RELEASE));
});

gulp.task("copy-public", ["copy"], function() {

    var replace = require("gulp-replace");

    return gulp.src([
        PUBLIC+"/*",
        "!"+ASSETS,
        "!"+WORKBENCH,
        "!"+VENDOR])
        .pipe(replace("src=\"workbench/", "src=\"vendor/"))
        .pipe(gulp.dest(RELEASE+"/"+PUBLIC));
});

gulp.task("copy-assets", ["copy-public"], function() {
    return gulp.src(ASSETS+"/**/*")
        .pipe(gulp.dest(RELEASE+"/"+ASSETS));
});

gulp.task("copy-admin", ["copy-assets"], function() {
    return gulp.src([
        "admin/{bootstrap,config,migration,storage,tasks}/*"])
            .pipe(gulp.dest(RELEASE+"/admin"));
});

gulp.task("copy-config", ["copy-admin"], function() {
    return gulp.src([CONFIG+"/*"]).pipe(gulp.dest(RELEASE+"/"+CONFIG));
});

gulp.task("create-bower", ["copy-config"], function() {

    var merge = require("gulp-merge-json");

    return gulp.src(["bower.json", "workbench.json"])
        .pipe(merge("bower.json"))
        .pipe(gulp.dest(RELEASE));
});

gulp.task("create-bower-stage", ["copy-config"], function() {

    var merge = require("gulp-merge-json");

    return gulp.src(["bower.json", "stage.json"])
        .pipe(merge("bower.json"))
        .pipe(gulp.dest(RELEASE));
});

gulp.task("release", ["create-bower"]);
gulp.task("release-stage", ["create-bower-stage"]);

gulp.task("module-typings", function() {

    var typings = require("gulp-typings");

    fs.readdir(WORKBENCH, function(error, items) {

        items.forEach(function(dir) {

            gulp.src(WORKBENCH+"/"+dir+"/typings.json")
                .pipe(typings());
        });
    });
});

gulp.task("module-local", function() {

    fs.readdir(WORKBENCH, function(error, items) {

        items.forEach(function(module) {
            var fws = fs.createWriteStream(WORKBENCH+"/"+module+"/local.d.ts", {
                defaultEncoding:    "utf8"
            });
            items.forEach(function(def) {
                if (def != module) {
                    try {
                        fs.statSync(WORKBENCH+"/"+def+"/dist/"+def+".d.ts");
                        fws.write("///<reference path=\"../"+def+"/dist/"+def+".d.ts\"/>\n");
                    }
                    catch (ex) {
                        //console.log(ex);
                    }
                }
            });
            fws.end();
        });
    });
});

gulp.task("module-local-clean", function () {

    fs.readdir(WORKBENCH, function (error, items) {

        items.forEach(function (module) {

            var filepath = WORKBENCH + "/" + module + "/local.d.ts";

            try {
                fs.unlink(filepath, function (err) {
                    if (!err) {
                        console.log('successfully deleted ' + filepath);
                    } else {
                        console.log('not found ' + filepath);
                    }
                });
            }
            catch (ex) {
                //console.log(ex);
            }

        });
    });
});

gulp.task("module-typescript", ["module-local"], function () {

    var tsc = require("gulp-typescript");

    fs.readdir(WORKBENCH, function(error, items) {
        items.forEach(function(module) {
            var tsConfig = WORKBENCH+"/"+module+"/tsconfig.json";
            try {
                fs.statSync(tsConfig);
                var tsProject = tsc.createProject(tsConfig);

                var tsResult = tsProject.src()
                    .pipe(tsc(tsProject));
            }
            catch (ex) {
                //console.log(ex);
            }
        });
    });
});



// NEW RELEASE

gulp.task("create-bower-alfa", function() {

    var merge = require("gulp-merge-json");

    return gulp.src(["bower.json", "workbench.json"])
        .pipe(merge("bower.json"))
        .pipe(gulp.dest("."));
});

gulp.task("create-bower-stage", function() {

    var merge = require("gulp-merge-json");

    return gulp.src(["bower.json", "stage.json"])
        .pipe(merge("bower.json"))
        .pipe(gulp.dest("."));
});
