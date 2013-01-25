# YouTube-Fruitcaster
Creates an iTune Store Podcast from a YouTube channel.

Fruitcaster analyses the videos in a given YouTube-Channel using the YouTube API. It then creates metadata-files for all videos. Then, it uses youtube-dl to download those videos.

The PHP side of Fruitcaster takes those metadata files and creates an iTunes-compatible RSS-Feed, which can then be registered in iTunes.

Warning: This script will need a ton of diskspace, since it will download _ALL_ videos in the given YouTube channel. Please be aware of that.

This stuff was [@silsha](https://twitter.com/silsha)'s idea, and was written by [@silsha](https://twitter.com/silsha) and [@tiefpunkt](https://twitter.com/tiefpunkt). Enjoy.

## Install
1. Download the script.
2. Get a YouTube API key, and put it in a copy of config.yaml.example, with the name config.yaml.
3. Set all the other options in config.php and maybe even in feed.php (hopefully, we'll get around to pull all this into one single file).
4. Run channel.py, and make it a cron job.
5. Put the htdocs-folder on the web, e.g. by setting it as a DocumentRoot in Apache
6. Submit your feed to iTunes.
7. PROFIT!!1!
