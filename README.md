# Project Notifications
MantisBT plugin that enables customization of notifications on a per-project basis

Change [notification_inc.php](files/config/notification_inc.php) to add custom notifications.

The `$notification_config` variable is an array of projects and each project is an array of users. Each user is then an array with the names of notifications that should **not** be sent to *that* user for *that* project.

If a project or a user is not set in that variable, notifications will be sent according to Mantis' settings.