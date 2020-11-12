# Ceres

A lightweight, Markdown based website engine built in PHP.

## Installation

Download the latest release or clone the repository. Place all files in the root of your domain or subdomain.

### Dependancies

Ceres uses Parsedown to convert Markdown to HTML. Download and include it in the `ceres` directory.

## Usage

Configuration is in the `ceres/config.php` file.

**Pages** are Markdown files which live in the `pages` directory. Link them together to create your site.

**Posts** are Markdown files which can be used to create a blog or news articles. Put them in the `posts` directory. 

**Fragments** are Markdown files which are used to customise the site's header, footer and navigation. They are used used in the generated pages. Fragments go in the `fragments` directory.

## Generating Pages

Run the `updatesite.php` script to build the home, archive and site map pages and the RSS feed.

## Documentation

Further documentation is available on the [Ceres site](http://ceres.strudwick.org).
