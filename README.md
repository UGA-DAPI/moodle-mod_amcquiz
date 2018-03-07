## Amcquiz Moodle activity module

Amcquiz is a moodle activity module aimed to allow teachers to create quizes (based on exiting questions), print them and distribute them to student.
Once students have finished. Sheets should be scanned and uploaded so that the plugin can automatically correct and mark them.

## Requirements

- This plugin was written with moodle 34
- No more requirements than moodle ones

## Install

- Download the source
- Extract archive in `moodlesite\mod\`
- Go to moodle administration page `http(s)://mydomain.com/moodlesite/admin/index.php`
- You should see the 'a plugin needs to be installed' message
- Follow the steps proposed by moodle
- You are now able to add an Amcquiz activity to a course section

## More technical informations

- This module uses an API to process data
- This API is `to be set` and uses AMC a perl library (https://www.auto-multiple-choice.net/)
- The API documentation can be found `here...`
