Contributors: lucasstark
Tags: slack
Requires at least: 4.4
Tested up to: 4.5.3
Stable tag: 1.1.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Requires:  WP Rest API

WP Slack Server is a developer framework for creating slash commands for slack.   Easily create new commands by pluging into the wp_slack_server_init action and registering your own commands.

See the /commands directory for example slash command handlers.


You can also create commands using the Dashboard.  Create your command from Dashboard -> Slack -> Slack Commands.
You can enter the command name, the command token and then enter the phrases ( utterances ) which the command will respond to.
Once an utterance is matched, the filter you enter in the Response Filter Name.

 An example filter below:

add_filter('respond_with_joke', 'my_respond_with_a_joke', 10, 3);
function my_respond_with_a_joke($response, $message, $args){
	//This is where you would do whatever you need your command to do.
	$response = 'Here is your joke';
	return $response;

	//Note:  You can return plain text, or an instance of a WP_Slack_Server_Message_Outgoing as shown below.
	$response = new WP_Slack_Server_Message_Outgoing('Here is your joke');
	return $response;
}
