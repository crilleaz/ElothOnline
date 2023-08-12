# ElothOnline
ElothOnline is a Webbased (textbased) Dungeon MMORPG!

# Features
* Login
* Register
* Chat
* Combatlog
* Dungeon system with 4 dungeons
* Experience and level system
* Inventory system
* Monsters
* Highscores
* Loot system
* Stamina system
* Hitpoints system
* Combat system
* Item system
* Ban system
* GM system


# Disclaimer
I went into this project by myself, thinking I would finish it, I grew tired.<br>
It's sloppy and unfinished, but rather good start or if you would like to continue building!

# Ingame screenshots
![UI_1](https://user-images.githubusercontent.com/20803604/215297319-a4ef0dac-6c14-4132-8d45-76578b8b35bd.PNG)
![UI_2](https://user-images.githubusercontent.com/20803604/215297010-5bcecc3d-4763-4912-bd8c-6ca30edbd37c.PNG)
![highscores](https://user-images.githubusercontent.com/20803604/215297301-d61704c9-7e1a-4df0-b1d4-0cbea936ab0f.PNG)
![inventory](https://user-images.githubusercontent.com/20803604/215297005-508d0f90-c858-46a0-a3cb-887129f34eef.PNG)
![library](https://user-images.githubusercontent.com/20803604/215297007-b50045d0-7e88-4718-af6a-3eb7395f0af7.PNG)
![server](https://user-images.githubusercontent.com/20803604/215297008-bcb9b38d-bcce-4f67-ab9b-7f913d657b32.PNG)


# Installation
* Upload the files to your webserver so that `./www/` should be exposed as the root folder. 
* Set db properties in `config.php`
* Run ```php install.php```
* Cronjob the server.php


# Updates

Eventually, there will be updates which means that database structure and some fixtures will change.  
For those purposes there is `updates` folder which contains `sql` files.  

To create new update run `php ./updates/create-new.php`. Modify created file according your needs.  

To install updates just run `php install.php`.  

**Beware!**  
Do not modify existing updates once they are under VCS. Each update is executed only once.  
If you need more changes, create a new one.

# Discord
Crilleaz, khan0454
