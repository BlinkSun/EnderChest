![Image of Yaktocat](http://hydra-media.cursecdn.com/minecraft.gamepedia.com/f/f6/Ender_Chest.gif)EnderChest
==========

Keep your stuffs anywhere !

Reacram(REACtor + progRAM) is a programmable reactor.  
Reacram can run the special program "Reactension"(REACram + exTENSION)  
You can load reactension you like into your reacram freely.  
For example, farming, mining, building and so on...  
Reactension can be written in PHP. So you can make it easily.  

# Installation
1.  Drop it into your /plugins folder.
2.  Restart your server.

# Installation(Reactension)
1.  Drop it into your /plugins/Reacram/reactension folder.

# My reactensions

| Name | URL |
| :-----: | :-------: |
| Mining | https://dl.dropboxusercontent.com/s/3tc2q3wbexybsh1/Mining.php |
| Farming | https://dl.dropboxusercontent.com/s/qzxtkuvjge7598k/Farming.php |

# Chat commands

| Command | Parameter | Description |
| :-----: | :-------: | :---------: |
| /reacram | `None` | Show available command list |
| /reacram help | `None` | Show help |
| /reacram list | `None` | Show list of reactensions |
| /reacram name | `<name>` | Name reactor `<name>` |
| /reacram load | `<name>` `<reactension>` | Load `<reactension>` into `<name>` |
| /reacram link | `<name>` | Link chest to `<name>` |
| /reacram run | `<name>` | Run the program loaded into `<name>` |
| /reacram stop | `<name>` | Stop the program loaded into `<name>` |

# How to use

1. Type `/reacram name <name>`
2. Touch the reactor you want to name `<name>`
3. Type `/reacram load <name> <reactension>`
4. If you need to link chest to your Reacram, type `/reacram link <name>`, then touch the chest you want to link to `<name>`
5. Type `/reacram run <name>`
6. If you want to stop your Reacram, type `/reacram run <name>`

For example, case "Mining" reactension

1. /reacram name test
2. Touch a reactor
3. /reacram load test Mining
4. /reacram link test
5. Touch a chest
6. /reacram run test

Reacram "test" collects block going down.

# Video
http://www.youtube.com/watch?v=73sOQiwQ40I

# For developers

You can make a reactension in PHP.  
The rule you must follow:  

1. Filename should be the same as the classname.   
ex) classname: TestReactension => filename: TestReactension.php  
2. Your reactension class must inherit from "Reactension" class.  
3. Your reactension class must implement two function: public function init() and public function run()  
init() is the function which is called at the beginning. You mustn't implement __construct!  
run() is the main function which is called when your reacram is executed.  

## Reactension sample

```php

<?php

class ReactensionName extends Reactension
{
	public function init()
	{
	}

	public function run()
	{
	}
}
```
----

## Functions you can use in Reactension

| Function | Description |
| :-----: | :-------: |
| $this->sendToChest(Item $item) | Send item to the chest linked to your Reacram |
| $this->move($x, $y, $z) | If you want to move your Reacram, use this |



