gulp = require('gulp')
exec = require('child_process').exec

gulp.task 'phpunit', (cb) ->
    exec 'vendor/bin/phpunit', (err, stdout) ->
        console.log stdout
        cb()

gulp.task 'watch', (event) ->
    watcher = gulp.watch ['**/*.php'], ['phpunit']
    watcher.on 'change', (event) ->
        console.log('Event type: ' + event.type)

gulp.task 'default', ['watch']