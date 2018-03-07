<?php

# ProjectNotifications - a MantisBT plugin that enables customization
# of e-mail notifications on a per-project basis
#
# You should have received a copy of the GNU General Public License
# along with ProjectNotifications.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @copyright Copyright (C) 2018 Samir Aguiar for Intra2net AG - www.intra2net.com
 */

require_api( 'bug_api.php' );
require_api( 'project_api.php' );
require_api( 'user_api.php' );
require_api( 'email_api.php' );
require_api( 'constant_inc.php' );
require_api( 'logging_api.php' );

class ProjectNotificationsPlugin extends MantisPlugin {
    public function register() {
        $this->name = plugin_lang_get( "title" );
        $this->description = plugin_lang_get( "description" );
        $this->page = '';

        $this->version = "1.0";
        $this->requires = array(
            "MantisCore" => "2.5.0",
        );

        $this->author = "Samir Aguiar";
        $this->contact = "samirjaguiar@gmail.com";
        $this->url = "https://github.com/samiraguiar/project-notifications";
    }

    public function hooks() {
        return array(
            "EVENT_NOTIFY_USER_INCLUDE" => "include_users",
            "EVENT_NOTIFY_USER_PREF" => "get_user_notification_preference"
        );
    }

    /**
     * 
    * @param string $p_event_name  Event name.
    * @param integer $p_bug_id Bug id.
    * @param string $p_notify_type Notification type.
    * @return boolean Array with users IDs that should be notified
    */
    public function include_users( $p_event_name, $p_bug_id, $p_notify_type ) {
        $t_project_name = $this->get_project_name( $p_bug_id );
        $t_project_config = $this->get_config_for_project( $t_project_name );

        $users_to_include = array();
        if ( !$t_project_config ) {
            log_event( LOG_PLUGIN, "notification settings for project %s don't exist", $t_project_name );
            return $users_to_include;
        }

        foreach ( $t_project_config as $username => $enabled_notifications ) {
            if ( in_array( $p_notify_type, $enabled_notifications ) ) {
                $t_user_id = user_get_id_by_name( $username );
                array_push( $users_to_include, $t_user_id );
                log_event( LOG_PLUGIN, "user '%s' added as notification recipient for project %s", $username, $t_project_name );
            }
        }

        return $users_to_include;
    }

    /**
     * 
    * @param string $p_event_name  Event name.
    * @param string $p_notify "1" if user should be notified, false otherwise.
    * @param string $p_pref_field Field in the database representing this notification settings.
    * @param integer $p_user_id User id.
    * @param integer $p_bug_id Bug id.
    * @param string $p_notify_type Notification type.
    * @return boolean Array with the parameters received, with the first value possibly modified
    */
    public function get_user_notification_preference( $p_event_name, $p_notify, $p_pref_field, $p_user_id, $p_bug_id, $p_notify_type ) {
        $t_project_name = $this->get_project_name( $p_bug_id );
        $t_username = user_get_name ( $p_user_id );

        $t_return = array( $p_notify, $p_pref_field, $p_user_id, $p_bug_id, $p_notify_type );

        $project_config = $this->get_config_for_project( $t_project_name );
        if ( !$project_config ) {
            log_event( LOG_PLUGIN, "notification settings for project %s don't exist", $t_project_name );
            return $t_return;
        }

        $user_config = $project_config[$t_username];
        if ( !is_array( $user_config ) ) {
            log_event( LOG_PLUGIN, "notification settings for user %s don't exist for project %s", $t_username, $t_project_name );
            return $t_return;
        }

        // Only change notification settings if all sanity checks passed
        if ( in_array( $p_notify_type, $user_config ) ) {
            $t_return[0] = ON;
        }

        return $t_return;
    }

    private function get_project_name( $p_bug_id ) {
        $t_project_id = bug_get_field( $p_bug_id, 'project_id' );
        return project_get_name( $t_project_id, false );
    }

    private function get_config_for_project( $p_project_name ) {
        $config_file_path = plugin_file_path( 'config/notification_inc.php', plugin_get_current() );

        /*
         * For the sanity checks below we chose to keep mantis original notification settings
         * and not block notifications here
         */
        if ( !$config_file_path ) {
            log_event( LOG_PLUGIN, "notification settings not found in %s, falling back to mantis settings", $config_file_path );
            return null;
        }

        include $config_file_path;

        if ( !is_array( $notification_config ) ) {
            log_event( LOG_PLUGIN, "invalid notification settings, falling back to mantis settings" );
            return null;
        }

        $project_config = $notification_config[$p_project_name];

        return is_array( $project_config ) ? $project_config : null;
    }
}
