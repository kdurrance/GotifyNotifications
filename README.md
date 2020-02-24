# Pocketmine plugin to send Pocketmine events to a Gotify notifications server

Presently, the following events are pushed to Gotify:
- PlayerQuitEvent
- PlayerJoinEvent
- PlayerKickEvent
- PlayerLoginEvent
- PlayerGameModeChangeEvent
- CommandEvent
- ServerCommandEvent
- LowMemoryEvent
- UpdateNotifyEvent

You need to have http access to a Gotify server for this plugin to work
```
Step 1. Host a Gotify server (https://github.com/gotify/server)
Step 2. Within Gotify, create a new App for Pocketmine, record the App token
Step 3. Update config.yml for this plugin with the Gotify configuration
```

Sample config.yml

```
--- 
# GotifyNotifications Configuration File
server: "192.168.0.100"
port: "8383"
apptoken: "A95sRmQ0awaJUy4"
...
```
