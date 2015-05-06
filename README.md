# Microcron

[![Latest Stable Version](https://img.shields.io/packagist/v/appserver-io/microcron.svg?style=flat-square)](https://packagist.org/packages/appserver-io/microcron) 
 [![Total Downloads](https://img.shields.io/packagist/dt/appserver-io/microcron.svg?style=flat-square)](https://packagist.org/packages/appserver-io/microcron)
 [![License](https://img.shields.io/packagist/l/appserver-io/microcron.svg?style=flat-square)](https://packagist.org/packages/appserver-io/microcron)
 [![Build Status](https://img.shields.io/travis/appserver-io/microcron/master.svg?style=flat-square)](http://travis-ci.org/appserver-io/microcron)
 [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/appserver-io/microcron/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/appserver-io/microcron/?branch=master)
 [![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/appserver-io/microcron/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/appserver-io/microcron/?branch=master)

## Introduction

This library aim for a complete PHP implementation of a cron expression with the addition of seconds as smallest time span to be controlled.
Cron expressions used with this library have, other than [normal cron](https://en.wikipedia.org/wiki/Cron), 6 digits where as the schema starts with seconds and continues with a normal cron schedule.

The `second`-digit can be used the same as the `minute` or `hour` digit.

## Issues

In order to bundle our efforts we would like to collect all issues regarding this package in [the main project repository's issue tracker](https://github.com/appserver-io/appserver/issues).
Please reference the originating repository as the first element of the issue title e.g.:
`[appserver-io/<ORIGINATING_REPO>] A issue I am having`

## Notice

This project is heavily inspired and based on [mtdowling/cron-expression](https://github.com/mtdowling/cron-expression) which provides the technical background and calculation engine.
Therefor the license of this project has to be taken into account.
It is included within the enclosed `LICENSE` file.
