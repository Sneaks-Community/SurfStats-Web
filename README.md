# Hello!

This is a simple web interface for the CS:GO plugin, CKSurf.

It was built with Bootstrap!

It is a basic stats display web page where you can view the leaderboard of different player's surf times.

You can see a working demo of this project here : http://www.rowedahelicon.com/workshop/stats/?view=home

![Alt text](http://i.imgur.com/1MESc1E.png "Screenshot")


# Features:
You can look up players by name!
View top players per map!
View specific player data!
View recent logged data!

# Instructions
REQ : php 5.0+ is recommended
REQ : Web server
REQ : Database with CkSurf stats mod : https://forums.alliedmods.net/showthread.php?t=264498

1. Open the config.php
2. Edit the following
    stat_name,group_name,group_url,local_timezone,db_type,db_server,db_name,db_user,db_passwd,db_prefix
3. Save and you should be all good to go!

To add servers to your server list

1. Open servers.txt
2. Add ONE server per line, in this format
    XXX.XXX.XXX.XXX:XXXXX|Test gaming Surf Server
    
You can put whatever name there, as long as you separate it with a |

# Important
Make sure the database enter you fill in contains your CKSurfStats data, otherwise nothing will work and everything will explode.
If you have any questions, feel free to contact me! 

twitter: @rowedahelicon

email: theoneandonly[AT]rowedahelicon.com
