# Project Notifications
MantisBT plugin that enables customization of notifications on a per-project basis

The first step to use this plugin is to disable the notifications you don't want to receive on Mantis settings page. Then you need to tweak the [notification_inc.php](files/config/notification_inc.php) file and add from which project each user wants to receive notifications.

The `$notification_config` variable is an array of projects and each project is an array of users. When Mantis is sending a notification, we will check if the corresponding project has an entry on that file. If it does, we will add the users to the list of recipients for that notification. Finally we will overwrite Mantis settings and make sure that those users receive that notification (if we don't, Mantis will check that they have notifications disabled and won't send it to them). 

If a project or a user is not set in that variable, notifications will be sent according to Mantis' settings.
