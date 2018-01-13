## MessagesManager ##

> This package support only Laravel 5.x versions
 
### Installation ###
 
Add MessagesManager package to your composer.json file to require it :
```
    require : {
        "laravel/framework": xxxx,
        ...
        "ousamox/messages-manager": "master"
    }
```
 
Update Composer :
```
$ composer update
```
 
The next required step is to add the service provider to ```config/app.php``` :
```
    'providers' => array(
    	...
    	'Ousamox\MessagesManager\Providers\MessagesManagerProvider'
    )
```

Alias the PushNotification facade by adding it to the aliases array in the ```config/app.php``` file :
```
    'aliases' => array(
        ...
        'OMessage' => 'Ousamox\MessagesManager\Facades\OMessage'
    )
```

### Configuration ###

#### Publish Files ####
 
Publish OMM config and migration files in your application with :
```
$ php artisan vendor:publish --tag=omm
```

This will generate a config file in ```config/omm.php``` like this :

```php
return [
    'models' => [
        'channel' => [
            'table_name' => 'OMM_CHANNELS',
            'class' => \Ousamox\MessagesManager\Models\Channel::class,
        ],
        'device' => [
            'table_name' => 'OMM_DEVICES',
            'class' => \Ousamox\MessagesManager\Models\Device::class,
        ],
        'message' => [
            'table_name' => 'OMM_MESSAGES',
            'class' => \Ousamox\MessagesManager\Models\Message::class,
        ],
        'message_seen' => [
            'table_name' => 'OMM_MESSAGE_SEENS',
            'class' => \Ousamox\MessagesManager\Models\MessageSeen::class,
        ],
        'message_file' => [
            'table_name' => 'OMM_MESSAGE_FILES',
            'class' => \Ousamox\MessagesManager\Models\MessageFile::class,
        ],
        'session' => [
            'table_name' => 'OMM_SESSIONS',
            'class' => \Ousamox\MessagesManager\Models\Session::class,
        ],
        'user' => [
            'table_name' => 'OMM_USERS',
            'class' => \Ousamox\MessagesManager\Models\User::class,
        ],
    ],
    'channels_duplication' => env('OMM_CHANNELS_DUPLICATION',false),
];
```

This will generate also a migration file in ```database/migrations``` contains all tables that we must have in our database.

#### Configure files ####

We can customise the name of our tables by editing the ```models.<entity>.table_name``` value in ```config/omm.php``` 

We can alse customise the main model class for each entity by editing the ```models.<entity>.class``` value in ```config/omm.php``` :

**Example :**

```config/omm.php``` :

```php
return [
    'models' => [
        'channel' => [
            'table_name' => 'app_channels',
            'class' => App\Models\Channel::class,
        ],
        ...
``` 

```app/models/Channel.php``` :

```php
namespace App\Models;

use Ousamox\MessagesManager\Models\Channel as BaseChannel;

class Channel extends BaseChannel
{
    // Add or Override the main behavior of this class without touching its defined properties or attributes
    ...
}
```  

The last step is generating tables by running this command :

```
$ php artisan migrate
```

Congratulations, you have successfully installed MessagesManager in your project !

### Usage ###

> 1- Authentification is required when using these methods

> 2- Pagination must be available on the next release

#### Get All Channels (conversations) ####

To get the list of channels :

```php
$channels = \OMessage::getChannels();
```

#### Get Messages by channel (conversation) ####

To get the list of channels :

```php
// $value must be an integer (channel ID) or a Channel Object
$messages = \OMessage::getMessagesByChannel($value);
```

#### Send Message ####

**Send First Message in new channel**

```php
$messages = \OMessage::sendMessageNewChannel($messageData: array, $channelData: array $toUsers: array, $files = []);
```

***Parameters :***

Parameter | Description
--------- | -------
`messageData` | [Required] data array should respect the following structure :
```
[
   'content' => 'Hello World', // REQUIRED
   'sent_from_lat' => 3.993843, // OPTIONAL (MAPS Latituude)
   'sent_from_long' => 23.222843, // OPTIONAL (MAPS Longitude)
   ... // Other attributes added in entity : message
]
```
Parameter | Description
--------- | -------
`channelData` | [Required] data array should respect the following structure :
```
[
   'subject' => 'New Channel', // OPTIONAL
   .. // Other attributes added in entity : channel 
]
```
Parameter | Description
--------- | -------
`toUsers` | [Required] array of users ID that must receive message (one or many)
`files` | [Optional] array of files to join (one or many)


**Send Message in existing channel**

```php
$messages = \OMessage::sendMessageExistingChannel($messageData: array, $channel: ChannelModel|int, $files = []);
```

***Parameters :***

Parameter | Description
--------- | -------
`messageData` | [Required] data array should respect the following structure :
```
[
   'content' => 'Hello World', // REQUIRED
   'sent_from_lat' => 3.993843, // OPTIONAL (MAPS Latituude)
   'sent_from_long' => 23.222843, // OPTIONAL (MAPS Longitude)
   ... // Other attributes added in entity : message
]
```
Parameter | Description
--------- | -------
`channel` | [Required] Channel object or channel ID
`files` | [Optional] array of files to join (one or many)

**Add users to an existing channel**

> Only channel creator could attach users to it 

```php
$messages = \OMessage::addUsersToChannel($channel: ChannelModel|int, $users: array);
```

***Parameters :***

Parameter | Description
--------- | -------
`channel` | [Required] Channel object or channel ID
`users` | [Required] array of users ID that must attach this channel
