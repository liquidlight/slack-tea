# Tea Bot

### Can't decide who should make a brew? Let Slack do it for you

_This uses the Slack API class created by 10w042 - the original can be found [on GitHub](https://github.com/10w042/slack-api). The class is included in this repo for ease of use._

This Outgoing WebHook takes the members of the channel to decide who should get up and make a round of tea. If you have members of the team that don't drink tea - then it might be worth making a dedicated channel.

### Installation

1. Download the PHP files and host them on an externally accessible URL
2. Head to [https://api.slack.com/web](https://api.slack.com/web) and (if you haven't already) scroll down and click the **Create Token** link
3. Copy your token that is generated and paste it into the `$auth_token` variable at the top of `tea.php`
4. Drop over to [https://YOURTEAM.slack.com/services/new](https://YOURTEAM.slack.com/services/new) and right at the bottom, add the **Outgoing WebHooks** integration
5. Create the WebHook with the following settings
	- Channel: **Any** (or your dedicated Tea channel)
	- Trigger Word(s): **!tea** (if you change this - remeber to change the `$trigger_word` var at the top of `tea.php`)
	- URL(s): The url of where your `tea.php` is
	- Descriptive Label: What ever you want
	- Customize Name: I called ours "**Tea Bot**"
	- Customize Icon: There is a tea icon in there somewhere!
6. Save the settings!

Job Done!

If all has worked well, you should be able to type your trigger word into your designated channel and hey presto! A tea maker is selected.

### Customisation

There are a couple variables at the top of `tea.php` - make sure you keep these updated.

`$trigger_word` - Make sure if your trigger word is anything other than **!tea** you update this variable.

`$responses` - feel free to update/change the responses, just us `{{USER}}` where you want the users name to appear.

### Pass in an extra string

The tea bot allows you to pass in an extra paramter to exclude a tea maker. For example, if _Tarquin_ had just made the round of tea - it would be unfair for him to be in the spinning for the next round:

	!tea tarquin

This will select any other channel member except Tarquin. You get your tea, Tarquin doesn't make two rounds in a row! Everyone is happy (unless you get selected).

This does a very rudamentary `strpos()` and so could be slightly abused. Want a round of tea to be made but anyone with an **m** in their name is excluded?

	!tea m


### Todo

- Add initial feedback to let the users know the Tea Bot is working
- Use the Slack class to send back messages to the channel
