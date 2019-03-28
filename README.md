# silverstripe-team

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/wernerkrauss/silverstripe-team/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/wernerkrauss/silverstripe-team/?branch=master)

A simple and extensible module for listing a team on a page.

## Adding team members to a xml sitemap

When you're using [wilr/silverstripe-googlesitemaps ](https://packagist.org/packages/wilr/silverstripe-googlesitemaps) for generating a sitemap.xml you can add the team members by adding this to your /app/config.php:

```
\Wilr\GoogleSitemaps\GoogleSitemap::register_dataobject(\Netwerkstatt\Team\Model\TeamMember::class);
```

## Credits
Icon by [entypo](http://www.entypo.com) CC-BY-SA 4.0
