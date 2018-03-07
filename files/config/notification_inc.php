<?php

/*
 * Notification types:
 * +--------------------------------------------------------------------+
 * |     Type     | Description                                         |
 * +--------------+-----------------------------------------------------+
 * | bugnote      | Note added to issue
 * | acknowledged | Status changed to acknowledged                      |
 * | assigned     | Status changed to assigned                          |
 * | closed       | Status changed to closed                            |
 * | deleted      | Issue deleted                                       |
 * | confirmed    | Status changed to confirmed                         |
 * | monitor      | New user monitoring the issue                       |
 * | new          | New issue created or status changed to new          |
 * | owner        | Assignee (handler) changed                          |
 * | relation     | Relationship between issues changed                 |
 * | reopened     | Status changed to `feedback` or issue reopened      |
 * | resolved     | Issue status changed to resolved                    |
 * | sponsor      | ?                                                   |
 * | updated      | Issue information updated                           |
 * +--------------+-----------------------------------------------------+
 */

/*
 * `$notification_config` is an array of projects and each project
 * is an array of users. Each user is then an array with the names
 * of notifications that should **not** be sent to *that* user for
 * *that* project.
 * 
 * For projects and users not set here notifications will be sent
 * according to Mantis' settings.
 */
$notification_config = array(
    "project1" => array(
        "samir" => ["resolved"],
        "samir2" => ["new", "reopened", "closed"]
    ),
    "project2" => array(
        "samir" => ["bugnote", "acknowledged", "new"],
        "samir2" => ["new", "reopened", "closed"]
    )
);