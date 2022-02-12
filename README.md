# WorldTime

[![](https://poggit.pmmp.io/shield.state/WorldTime)](https://poggit.pmmp.io/p/WorldTime)
[![](https://poggit.pmmp.io/shield.dl.total/WorldTime)](https://poggit.pmmp.io/p/WorldTime)

A PocketMine-MP plugin to set time and stop time easily.

# Features

- Customize messages when setting world time.
- `{PREFIX}` can be used to display world folder name.
- `{SUFFIX}` can be used to display world time.
- Lightweight and open source ❤️

# Default Config
```yaml
---
# Do not change this (Only for internal use)!
config-version: 1.0

# Message used when setting world time.
# Available tags:
# - {WORLD}: Show world folder name.
# - {TIME}: Show world time.
message: "Set the time of the {WORLD} world to {TIME}"

worlds:
  # World folder name.
  world:
    # World time to set.
    time: day
    # Stop time
    stop: true
  # World folder name with spaces.
  "example world":
    time: night
    stop: false
...

```

# Upcoming Features

- Currently none planned. You can contribute or suggest for new features.

# Additional Notes

- If you find bugs or want to give suggestions, please visit [here](https://github.com/AIPTU/WorldTime/issues).
- We accept all contributions! If you want to contribute, please make a pull request in [here](https://github.com/AIPTU/WorldTime/pulls).
- Icons made from [www.flaticon.com](https://www.flaticon.com)
