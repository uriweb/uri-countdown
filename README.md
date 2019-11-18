# URI Countdown

A tool that creates a message that states "days remaining"

## Shortcode

The base syntax for the shortcode is as follows: `[uri-countdown]`.  There are additional attributes that can be used to further customize the shortcode's output.



### Shortcode attributes

All of the options below are optional.

	# `deadline` expects the date and time of the deadline. It's parsed by strtotime() so it's fairly flexible
	# `event` the name of the event that occurs at the end of the countdown
	# `show_expired` boolean. default: FALSE.  If true, a message will be displayed after the deadline passes
	# `until` default: "until" - customizes the countdown message e.g. 5 days until such-and-such.
	# `is_today` default: "is today" - customizes the countdown message on the day of the deadline e.g. such-and-such is today.
	# `passed` default: "passed" - customizes the countdown message after the deadline e.g. such-and-such passed.
	# `link` the URL that the countdown links to
	# `class` additional CSS classes to add to the message
	
## Examples

Display a countdown to the end of Dec 1, 2024

```[uri-countdown deadline="Dec 1 2024 23:59:59" event="my event name" link="https://www.uri.edu"]```
