# Subathon Watchtime

Provide the watchtime per user on a single event / stream on Twitch using [StreamElements](https://streamelements.com/). These are very simple PHP scripts and easy to use. Feel free to edit the code for your needs.

## How-to

- Download all files and upload them to any webserver providing PHP.
- Edit the 'cron.php' and change the URL. It has to point to your 'subathon.php'.
- Edit the 'subathon.php' and change the texts in the very last line as you want.
- Create a cron job on your webserver for opening the 'cron.php' every few minutes. The time between every execution must be < 10 minutes.
- Add a new chat command in your StreamElements-panel using the following code:

```sh
${urlfetch http://www.urltoyour.php/subathon.php?action=get&channel=your_channel&user=${user.name}}
```

## Donations

If you like 'Subathon Watchtime', support me with a donation or buy me a coffee:
- BTC (Bitcoins): 1MiKuJrTCNST3haCX6sCnmMWTxJ4ZXtYgw
- LTC (Litecoins): LT2NaafnqpsCcnuDKYcF9NMm7Dzxc2JgYE

I appreciate every donation and support!
