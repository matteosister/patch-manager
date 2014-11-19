gulp = require('gulp')
exec = require('child_process').exec

gulp.task 'phpunit', (cb) ->
    exec 'bin/phpunit', (err, stdout) ->
        if (err)
            console.log err
        console.log stdout
        cb()

gulp.task 'phpspec', (cb) ->
    exec 'bin/phpspec run --format=dot', (err, stdout) ->
        if (err)
            console.log err
        console.log stdout
        cb()

gulp.task 'watch-unit', (event) ->
    watcher = gulp.watch ['**/*.php'], ['phpunit']
    watcher.on 'change', (event) ->

gulp.task 'watch-spec', (event) ->
    watcher = gulp.watch ['**/*.php'], ['phpspec']
    watcher.on 'change', (event) ->


gulp.task 'default', ['watch-spec']