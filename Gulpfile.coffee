gulp = require('gulp')
exec = require('child_process').exec

gulp.task 'phpunit', (cb) ->
    exec 'bin/phpunit', (err, stdout) ->
        if (err)
            console.log err
        console.log stdout
        cb()

gulp.task 'watch', (event) ->
    watcher = gulp.watch ['**/*.php'], ['phpunit']
    watcher.on 'change', (event) ->


gulp.task 'default', ['watch']