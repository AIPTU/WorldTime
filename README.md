# WorldTime

[![](https://poggit.pmmp.io/shield.state/WorldTime)](https://poggit.pmmp.io/p/WorldTime)
[![](https://poggit.pmmp.io/shield.dl.total/WorldTime)](https://poggit.pmmp.io/p/WorldTime)

The WorldTime plugin is a PocketMine plugin that allows you to set the time of specific worlds in your server based on a configuration file.

## Features

- Set the time of individual worlds in your server.
- Support for different time values, including predefined values like day, night, sunset, sunrise, etc.
- Option to stop the time in a world.
- Customizable log message when setting the time of a world.
- Automatic generation of a new configuration file if an outdated one is provided.

## Configuration

The plugin comes with a configuration file named `config.yml`. You can modify this file to configure the plugin behavior.

The `config.yml` file contains the following options:

- `config-version`: **(Do not modify)** The version of the configuration file.
- `worlds`: An array of worlds and their settings. Each world has the following options:
  - `time`: The desired time value for the world. It can be one of the predefined values (day, night, sunset, sunrise, noon) or a custom integer value.
  - `stop`: *(Optional)* Set to `true` if you want to stop the time in the world.
- `message`: The log message to display when setting the time of a world. You can use placeholders to dynamically insert values. Available placeholders: `{WORLD}`, `{TIME}`.

## Usage

1. Edit the `config.yml` file to specify the worlds and their time settings.
2. Start or reload the server.
3. The plugin will automatically load the specified worlds and set their time according to the configuration.

The plugin will log a message for each world it sets the time for, using the configured log message. The `{WORLD}` and `{TIME}` placeholders in the log message will be replaced with the respective values.

## Example Configuration

```yaml
worlds:
  world1:
    time: day
    stop: false
  world2:
    time: 6000
    stop: true
  world3:
    time: sunset
    stop: false
message: 'Set the time of the {WORLD} world to {TIME}.'
```

# Additional Notes

- If you find bugs or want to give suggestions, please visit [here](https://github.com/AIPTU/WorldTime/issues).
- We accept all contributions! If you want to contribute, please make a pull request in [here](https://github.com/AIPTU/WorldTime/pulls).
- Icons made from [www.flaticon.com](https://www.flaticon.com)
